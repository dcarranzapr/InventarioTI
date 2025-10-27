const express = require('express');
const router = express.Router();
const db = require('../db');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const { registrarLogEquipo } = require('../middlewares/log');

// ✅ Corrige ruta para guardar fuera de /routes
const firmaDir = path.join(__dirname, '..', 'uploads', 'firmas');
if (!fs.existsSync(firmaDir)) {
  fs.mkdirSync(firmaDir, { recursive: true });
}

// ✅ Configuración de multer
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, firmaDir);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, uniqueSuffix + path.extname(file.originalname));
  }
});
const upload = multer({ storage: storage });

router.post('/:id_resguardo/guardarFirmaColaborador', upload.single('firma'), async (req, res) => {
  const { id_resguardo } = req.params;
  const firmaFile = req.file;

  if (!firmaFile) {
    console.error('⚠️ No se recibió el archivo de firma');
    return res.status(400).json({ success: false, error: 'No se recibió el archivo de firma' });
  }

  try {
    console.log('🖊️ Firma colaborador recibida:', firmaFile.filename);
    const sql = `UPDATE resguardos SET firma_colaborador = ? WHERE id_resguardo = ?`;
    await db.promise().query(sql, [firmaFile.filename, id_resguardo]); // 👈 CORREGIDO
    return res.json({ success: true, mensaje: 'Firma del colaborador guardada' });
  } catch (error) {
    console.error('❌ Error al guardar firma colaborador:', error);
    return res.status(500).json({ success: false, error: error.message });
  }
});

router.post('/:id_resguardo/guardarFirmaTecnico', upload.single('firma'), async (req, res) => {
  const { id_resguardo } = req.params;
  const firmaFile = req.file;

  if (!firmaFile) {
    console.error('⚠️ No se recibió el archivo de firma');
    return res.status(400).json({ success: false, error: 'No se recibió el archivo de firma' });
  }

  try {
    console.log('🖊️ Firma técnico recibida:', firmaFile.filename);
    const sql = `UPDATE resguardos SET firma_tecnico = ? WHERE id_resguardo = ?`;
    await db.promise().query(sql, [firmaFile.filename, id_resguardo]); // 👈 CORREGIDO
    return res.json({ success: true, mensaje: 'Firma del técnico guardada' });
  } catch (error) {
    console.error('❌ Error al guardar firma técnico:', error);
    return res.status(500).json({ success: false, error: error.message });
  }
});




// Crear nuevo resguardo
router.post('/', (req, res) => {
  const io = req?.io;
  console.log('📥 Datos recibidos en /api/resguardos:', req.body);

  const {
    num_colaborador,
    nombre_colaborador,
    direccion,
    gerencia,
    correo,
    nombre_equipo,
    plataforma,
    comentarios,
    usuario_creador_id
  } = req.body;

  const userId = parseInt(usuario_creador_id);
  if (!nombre_colaborador || !nombre_equipo || !userId) {
    return res.status(400).json({ success: false, mensaje: '❌ Campos obligatorios faltantes.' });
  }

  const verificar = 'SELECT * FROM resguardos WHERE nombre_equipo = ?';
  db.query(verificar, [nombre_equipo], (err, results) => {
    if (err) {
      console.error('❌ Error en SELECT:', err.message);
      return res.status(500).json({ success: false, error: err.message });
    }

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe un resguardo con ese nombre de equipo.' });
    }

    const insertar = `
      INSERT INTO resguardos 
      (num_colaborador, nombre_colaborador, direccion, gerencia, correo, nombre_equipo, plataforma, comentarios, usuario_creador_id)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    const valores = [
      num_colaborador ?? null,
      nombre_colaborador,
      direccion ?? null,
      gerencia ?? null,
      correo ?? null,
      nombre_equipo,
      plataforma ?? null,
      comentarios ?? null,
      userId
    ];

    db.query(insertar, valores, (err2, result) => {
      if (err2) {
        console.error('❌ Error al insertar resguardo:', err2.sqlMessage || err2.message);
        return res.status(500).json({ success: false, error: err2.sqlMessage || err2.message });
      }

      const id_resguardo = result.insertId;

      const select = `
        SELECT 
          r.id_resguardo,
          r.nombre_colaborador,
          r.nombre_equipo,
          r.gerencia AS departamento,
          COALESCE(h.nombre_hotel, 'Sin asignar') AS hotel,
          COALESCE(h.id_hotel, 0) AS id_hotel,
          IFNULL(h.estado, 'BAJA') AS estado_hotel,
          0 AS total_equipos,
          u.nombre AS ingeniero_creador
        FROM resguardos r
        LEFT JOIN hoteles h ON r.id_hotel_origen = h.id_hotel
        LEFT JOIN usuarios u ON r.usuario_creador_id = u.id_user
        WHERE r.id_resguardo = ?
      `;

      db.query(select, [id_resguardo], (err3, rows) => {
        if (err3 || rows.length === 0) {
          return res.status(500).json({ success: false, error: '❌ No se pudo recuperar el resguardo creado.' });
        }

        const nuevoResguardo = rows[0];
        if (io) {
          io.emit('resguardo_creado', nuevoResguardo);
          io.emit('resguardo_disponible_transferencia', nuevoResguardo);
        }

        res.json({ success: true, id_resguardo, resguardo: nuevoResguardo });
      });
    });
  });
});


router.get('/control/lista', (req, res) => {
  const sql = `
SELECT 
  r.id_resguardo,
  r.nombre_colaborador,
  r.nombre_equipo,
  r.gerencia AS departamento,
  COALESCE(h.nombre_hotel, 'Sin asignar') AS hotel,
  COALESCE(h.id_hotel, 0) AS id_hotel,
  IFNULL(h.estado, 'BAJA') AS estado_hotel,
  COUNT(re.id_equipo) AS total_equipos,
  u.nombre AS ingeniero_creador,
  (
    SELECT tr.estado
    FROM transferencias_resguardos tr
    WHERE tr.id_resguardo = r.id_resguardo
    ORDER BY tr.id_transferencia DESC
    LIMIT 1
  ) AS estado_transferencia
FROM resguardos r
LEFT JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
LEFT JOIN equipos e ON re.id_equipo = e.id_equipo
LEFT JOIN hoteles h ON r.id_hotel_origen = h.id_hotel
LEFT JOIN usuarios u ON r.usuario_creador_id = u.id_user
GROUP BY r.id_resguardo
ORDER BY r.id_resguardo DESC;
  `;

  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ success: false, error: err.message });
    res.json({ success: true, resguardos: results });
  });
});

router.get('/:id', (req, res) => {
  const { id } = req.params;
  const sql = `
    SELECT 
      r.nombre_colaborador,
      r.nombre_equipo,
      r.num_colaborador,
      r.direccion,
      r.gerencia,
      r.correo,
      r.plataforma,
      r.comentarios,
      r.firma_colaborador,       -- ✅ Se agregó
      r.firma_tecnico,           -- ✅ Se agregó
      COALESCE(h.id_hotel, 0) AS id_hotel,
      IFNULL(h.estado, 'BAJA') AS estado_hotel
    FROM resguardos r
    LEFT JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
    LEFT JOIN inventario i ON re.id_equipo = i.id_equipo
    LEFT JOIN hoteles h ON r.id_hotel_origen = h.id_hotel
    WHERE r.id_resguardo = ?
  `;

  db.query(sql, [id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    if (results.length === 0) return res.status(404).json({ error: 'Resguardo no encontrado' });

    res.json({ success: true, resguardo: results[0] });
  });
});


router.get('/:id/equipos', (req, res) => {
  const { id } = req.params;
  const sql = `
  SELECT 
      re.id_equipo,
      i.id_hotel, -- ✅ Agregamos el hotel de origen
      t.nombre_tipo AS tipo,
      m.nombre_marca AS marca,
      mo.nombre_modelo AS modelo,
      e.numero_serie,
      re.asignado_por
    FROM resguardo_equipos re
    JOIN equipos e ON re.id_equipo = e.id_equipo
    JOIN inventario i ON e.id_equipo = i.id_equipo -- ✅ Relación necesaria para obtener el hotel
    JOIN modelo mo ON e.id_modelo = mo.id_modelo
    JOIN marca m ON mo.id_marca = m.id_marca
    JOIN tipo_equipo t ON mo.id_tipo_equipo = t.id_tipo_equipo
    WHERE re.id_resguardo = ?
  `;

  db.query(sql, [id], (err, results) => {
    if (err) return res.status(500).json({ success: false, error: err.message });
    res.json({ success: true, equipos: results });
  });
});

// POST - asignar equipo a resguardo
router.post('/:id/equipos', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { id_equipo, asignado_por } = req.body;

  if (!id_equipo || !asignado_por) {
    return res.status(400).json({ success: false, mensaje: '❌ Faltan datos: id_equipo o asignado_por' });
  }

  const verificar = `SELECT * FROM resguardo_equipos WHERE id_resguardo = ? AND id_equipo = ?`;
  db.query(verificar, [id, id_equipo], (err, rows) => {
    if (err) return res.status(500).json({ success: false, mensaje: `❌ ${err.message}` });
    if (rows.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Este equipo ya fue asignado.' });
    }

    const insertar = `
      INSERT INTO resguardo_equipos (id_resguardo, id_equipo, asignado_por)
      VALUES (?, ?, ?)
    `;
    db.query(insertar, [id, id_equipo, asignado_por], (errInsert) => {
      if (errInsert) return res.status(500).json({ success: false, mensaje: `❌ ${errInsert.message}` });

      const updateEstado = `UPDATE inventario SET id_estado = 2 WHERE id_equipo = ?`;
      db.query(updateEstado, [id_equipo], (errEstado) => {
        if (errEstado) return res.status(500).json({ success: false, mensaje: `❌ ${errEstado.message}` });

        const getEstado = `
          SELECT i.id_inventario, e.nombre_estado
          FROM inventario i
          JOIN estado_equipo e ON i.id_estado = e.id_estado
          WHERE i.id_equipo = ?
          LIMIT 1
        `;
        db.query(getEstado, [id_equipo], (errGet, rowsGet) => {
          if (!errGet && rowsGet.length > 0) {
            const { id_inventario, nombre_estado } = rowsGet[0];

            io.emit('estado_equipo_actualizado', {
              id_inventario,
              id_estado: 2,
              estado: nombre_estado,
              asignado_a: 'RESGUARDO'
            });
          }

          io.emit('resguardo_equipo_actualizado', {
            id_resguardo: parseInt(id),
            accion: 'ASIGNADO',
            id_equipo: parseInt(id_equipo)
          });

                // ✅ Registrar log de asignación a resguardo
          registrarLogEquipo({
            id_equipo: parseInt(id_equipo),
            accion: 'ASIGNADO A RESGUARDO',
            id_usuario: parseInt(asignado_por), // <- Este ya lo tienes en req.body
            descripcion: `Equipo asignado al resguardo #${id}`,
            datos_nuevos: {
              estado: 'ASIGNADO',
              tipo: 'RESGUARDO',
              id_resguardo: parseInt(id)
            }
          });


          const verificarHotel = `SELECT id_hotel_origen FROM resguardos WHERE id_resguardo = ?`;
          db.query(verificarHotel, [id], (err2, rows2) => {
            if (err2) return res.status(500).json({ success: false, mensaje: `❌ ${err2.message}` });

            const yaTieneHotel = rows2[0]?.id_hotel_origen;
            if (!yaTieneHotel) {
              const getHotelEquipo = `
                SELECT h.id_hotel, h.nombre_hotel, h.estado 
                FROM inventario i 
                JOIN hoteles h ON i.id_hotel = h.id_hotel 
                WHERE i.id_equipo = ?
              `;
              db.query(getHotelEquipo, [id_equipo], (err3, rows3) => {
                if (err3 || rows3.length === 0)
                  return res.status(500).json({ success: false, mensaje: `❌ ${err3?.message || 'No se encontró hotel'}` });

                const hotel = rows3[0];
                const updateResguardo = `UPDATE resguardos SET id_hotel_origen = ? WHERE id_resguardo = ?`;
                db.query(updateResguardo, [hotel.id_hotel, id], (err4) => {
                  if (err4)
                    return res.status(500).json({ success: false, mensaje: `❌ ${err4.message}` });

                  // ✅ Emitir actualización con el hotel
                  io.emit('resguardo_actualizado', {
                    id_resguardo: parseInt(id),
                    id_hotel: hotel.id_hotel,
                    hotel: hotel.nombre_hotel,
                    estado_hotel: hotel.estado
                  });

                  return res.json({ success: true, mensaje: '✅ Equipo asignado y hotel origen fijado' });
                });
              });
            } else {
              return res.json({ success: true, mensaje: '✅ Equipo asignado correctamente' });
            }
          });
        });
      });
    });
  });
});

// DELETE - eliminar equipo
router.delete('/:id/equipos/:id_equipo', (req, res) => {
  const io = req.io;
  const { id, id_equipo } = req.params;

  const eliminar = `DELETE FROM resguardo_equipos WHERE id_resguardo = ? AND id_equipo = ?`;
  const restaurar = `UPDATE inventario SET id_estado = 1 WHERE id_equipo = ?`;

  db.query(eliminar, [id, id_equipo], (err) => {
    if (err) return res.status(500).json({ success: false, error: err.message });

    db.query(restaurar, [id_equipo], (err2) => {
      if (err2) return res.status(500).json({ success: false, error: err2.message });

      const getInvIdEstado = `
        SELECT i.id_inventario, e.nombre_estado 
        FROM inventario i 
        JOIN estado_equipo e ON i.id_estado = e.id_estado 
        WHERE i.id_equipo = ? LIMIT 1
      `;

      db.query(getInvIdEstado, [id_equipo], (errInv, rowsInv) => {
        if (!errInv && rowsInv.length > 0) {
          const { id_inventario, nombre_estado } = rowsInv[0];

          io.emit('estado_equipo_actualizado', {
            id_inventario,
            id_estado: 1,
            estado: nombre_estado
          });
        }

        io.emit('resguardo_equipo_actualizado', {
          id_resguardo: parseInt(id),
          accion: 'ELIMINADO',
          id_equipo: parseInt(id_equipo)
        });

        
        const id_usuario = parseInt(req.headers['user-id'], 10);

registrarLogEquipo({
  id_equipo: parseInt(id_equipo),
  accion: 'REMOVIDO DE RESGUARDO',
  id_usuario,
  descripcion: `Equipo removido del resguardo #${id}`,
  datos_nuevos: {
    estado: 'ALTA',
    tipo: 'LIBERADO',
    id_resguardo: parseInt(id)
  }
});

        res.json({ success: true, mensaje: '✅ Equipo eliminado del resguardo y restaurado a ALTA' });
      });
    });
  });
});


// PUT - actualizar resguardo
router.put('/actualizar/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const {
    numColaborador,
    nombre,
    direccion,
    gerencia,
    correo,
    nombreEquipo,
    plataforma,
    comentarios
  } = req.body;

  if (!numColaborador || !nombre || !nombreEquipo) {
    return res.status(400).json({ success: false, mensaje: '❌ Faltan campos obligatorios.' });
  }

  const verificar = `
    SELECT id_resguardo FROM resguardos 
    WHERE nombre_equipo = ? AND id_resguardo != ?
  `;

  db.query(verificar, [nombreEquipo, id], (err, resultados) => {
    if (err) return res.status(500).json({ success: false, error: err.message });

    if (resultados.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ El nombre del equipo ya está asignado a otro resguardo.' });
    }

    const actualizar = `
      UPDATE resguardos SET 
        num_colaborador = ?,
        nombre_colaborador = ?,
        direccion = ?,
        gerencia = ?,
        correo = ?,
        nombre_equipo = ?,
        plataforma = ?,
        comentarios = ?
      WHERE id_resguardo = ?
    `;

    const valores = [
      numColaborador,
      nombre,
      direccion || null,
      gerencia || null,
      correo || null,
      nombreEquipo,
      plataforma || null,
      comentarios || null,
      id
    ];

    db.query(actualizar, valores, (err2) => {
      if (err2) return res.status(500).json({ success: false, error: err2.message });

// Recuperar el resguardo actualizado + nombre del hotel + estado del hotel
      const consulta = `
        SELECT r.*, h.nombre_hotel AS hotel, h.estado AS estado_hotel
        FROM resguardos r
        JOIN hoteles h ON r.id_hotel = h.id_hotel
        WHERE r.id_resguardo = ?
      `;

      db.query(consulta, [id], (err3, rows) => {
        if (err3 || rows.length === 0) {
          return res.status(500).json({ success: false, error: '❌ Error al recuperar el resguardo actualizado.' });
        }

        const resguardoActualizado = rows[0];

        io.emit('resguardo_actualizado', resguardoActualizado); // ✅ Emisión con nombre hotel y estado
        io.emit('transferencia_resguardo_aceptada', {
  id_resguardo: id,  // ← importante
  estado: 'ACEPTADA'
});


        res.json({
          success: true,
          mensaje: '✅ Resguardo actualizado correctamente',
          resguardo: resguardoActualizado
        });
      });
    });
  });
});

// DELETE - eliminar resguardo
router.delete('/eliminar/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;

  const limpiarTransferencias = `
    UPDATE transferencias_resguardos
    SET id_resguardo = NULL
    WHERE id_resguardo = ?
  `;

  db.query(limpiarTransferencias, [id], (err0) => {
    if (err0) return res.status(500).json({ success: false, error: err0.message });

    const obtenerEquipos = `
      SELECT id_equipo FROM resguardo_equipos WHERE id_resguardo = ?
    `;

    db.query(obtenerEquipos, [id], (err, resultados) => {
      if (err) return res.status(500).json({ success: false, error: err.message });

      const equipos = resultados.map(r => r.id_equipo);

      const restaurar = `
        UPDATE inventario SET id_estado = 1 WHERE id_equipo IN (?)
      `;

      const continuar = () => {
        const eliminarRelacion = 'DELETE FROM resguardo_equipos WHERE id_resguardo = ?';
        const eliminarResguardo = 'DELETE FROM resguardos WHERE id_resguardo = ?';

        db.query(eliminarRelacion, [id], (err3) => {
          if (err3) return res.status(500).json({ success: false, error: err3.message });

          db.query(eliminarResguardo, [id], (err4) => {
            if (err4) return res.status(500).json({ success: false, error: err4.message });

            io.emit('resguardo_equipo_actualizado', {
              id_resguardo: parseInt(id),
              accion: 'RESGUARDO_ELIMINADO'
            });

            io.emit('resguardo_eliminado', parseInt(id));

            res.json({ success: true, mensaje: '✅ Resguardo eliminado correctamente' });
          });
        });
      };

  if (equipos.length > 0) {
  db.query(restaurar, [equipos], (err2) => {
    if (err2) return res.status(500).json({ success: false, error: err2.message });


const id_usuario = parseInt(req.headers['user-id'], 10);
equipos.forEach(id_equipo => {
  registrarLogEquipo({
    id_equipo,
    accion: 'REMOVIDO DE RESGUARDO',
    id_usuario,
    descripcion: `Equipo liberado automáticamente al eliminar resguardo #${id}`,
    datos_nuevos: {
      estado: 'ALTA',
      motivo: 'ELIMINACIÓN DE RESGUARDO',
      id_resguardo: parseInt(id)
    }
  });
});

    // Obtener id_inventario y nombre_estado de cada equipo para emitir evento
    const placeholders = equipos.map(() => '?').join(',');
    const sqlEstados = `
      SELECT i.id_inventario, e.nombre_estado
      FROM inventario i
      JOIN estado_equipo e ON i.id_estado = e.id_estado
      WHERE i.id_equipo IN (${placeholders})
    `;

    db.query(sqlEstados, equipos, (err3, resultadosEstados) => {
      if (!err3 && resultadosEstados.length > 0) {
        resultadosEstados.forEach(row => {
          io.emit('estado_equipo_actualizado', {
            id_inventario: row.id_inventario,
            id_estado: 1,
            estado: row.nombre_estado,
            asignado_a: null
          });
        });
      }

      continuar(); // Elimina el resguardo como ya lo tenías
    });
  });
} else {
  continuar();
}

    });
  });
});

module.exports = router;
