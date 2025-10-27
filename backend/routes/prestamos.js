const express = require('express');
const router = express.Router();
const db = require('../db');
const fs = require('fs');
const path = require('path');
const multer = require('multer');
const { registrarLogEquipo } = require('../middlewares/log');

// ✅ Asegura que la carpeta exista
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
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1e9);
    cb(null, uniqueSuffix + path.extname(file.originalname));
  }
});
const upload = multer({ storage: storage });

// ✅ GUARDAR firma del colaborador (prestamo)
router.post('/:id_prestamo/guardarFirmaColaborador', upload.single('firma'), async (req, res) => {
  const { id_prestamo } = req.params;
  const firmaFile = req.file;

  if (!firmaFile) {
    return res.status(400).json({ success: false, error: 'No se recibió el archivo de firma' });
  }

  try {
    const sql = `UPDATE prestamos SET firma_colaborador = ? WHERE id_prestamo = ?`;
    await db.promise().query(sql, [firmaFile.filename, id_prestamo]);
    return res.json({ success: true, mensaje: 'Firma del colaborador guardada' });
  } catch (error) {
    console.error('❌ Error al guardar firma colaborador:', error);
    return res.status(500).json({ success: false, error: error.message });
  }
});

// ✅ GUARDAR firma del técnico (prestamo)
router.post('/:id_prestamo/guardarFirmaTecnico', upload.single('firma'), async (req, res) => {
  const { id_prestamo } = req.params;
  const firmaFile = req.file;

  if (!firmaFile) {
    return res.status(400).json({ success: false, error: 'No se recibió el archivo de firma' });
  }

  try {
    const sql = `UPDATE prestamos SET firma_tecnico = ? WHERE id_prestamo = ?`;
    await db.promise().query(sql, [firmaFile.filename, id_prestamo]);
    return res.json({ success: true, mensaje: 'Firma del técnico guardada' });
  } catch (error) {
    console.error('❌ Error al guardar firma técnico:', error);
    return res.status(500).json({ success: false, error: error.message });
  }
});

// CREAR nuevo préstamo
router.post('/', (req, res) => {
  const io = req.io;
  const {
    num_colaborador,
    nombre_colaborador,
    direccion,
    gerencia,
    correo,
    nombre_equipo,
    plataforma,
    comentarios,
    usuario_creador_id,
    fecha_prestamo,
    fecha_devolucion
  } = req.body;

  if (!num_colaborador || !nombre_colaborador || !nombre_equipo || !usuario_creador_id || !fecha_prestamo) {
    return res.status(400).json({ success: false, mensaje: '❌ Campos obligatorios faltantes.' });
  }

  const verificar = 'SELECT * FROM prestamos WHERE nombre_equipo = ?';
  db.query(verificar, [nombre_equipo], (err, resultados) => {
    if (err) return res.status(500).json({ success: false, error: err.message });

    if (resultados.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe un préstamo con ese nombre de equipo.' });
    }

    const insertar = `
      INSERT INTO prestamos 
      (num_colaborador, nombre_colaborador, direccion, gerencia, correo, nombre_equipo, plataforma, comentarios, usuario_creador_id, fecha_prestamo, fecha_devolucion)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    const valores = [
      num_colaborador,
      nombre_colaborador,
      direccion || null,
      gerencia || null,
      correo || null,
      nombre_equipo,
      plataforma || null,
      comentarios || null,
      usuario_creador_id,
      fecha_prestamo,
      fecha_devolucion || null
    ];

    db.query(insertar, valores, (err2, result) => {
      if (err2) return res.status(500).json({ success: false, error: err2.message });

      const id_prestamo = result.insertId;

      const select = `
        SELECT 
          p.id_prestamo,
          p.nombre_colaborador,
          p.nombre_equipo,
          p.gerencia AS departamento,
          p.fecha_prestamo,
          p.fecha_devolucion,
          COALESCE(h.nombre_hotel, 'Sin asignar') AS hotel,
          COALESCE(h.id_hotel, 0) AS id_hotel,
          IFNULL(h.estado, 'BAJA') AS estado_hotel,
          0 AS total_equipos,
          u.nombre AS ingeniero_creador
        FROM prestamos p
        LEFT JOIN hoteles h ON p.id_hotel_origen = h.id_hotel
        LEFT JOIN usuarios u ON p.usuario_creador_id = u.id_user
        WHERE p.id_prestamo = ?
      `;

      db.query(select, [id_prestamo], (err3, rows) => {
        if (err3 || rows.length === 0) {
          return res.status(500).json({ success: false, error: '❌ No se pudo recuperar el préstamo creado.' });
        }

        const nuevoPrestamo = rows[0];
        io.emit('prestamo_creado', nuevoPrestamo);
        res.json({ success: true, id_prestamo, prestamo: nuevoPrestamo });
      });
    });
  });
});

// Obtener listado general de préstamos
router.get('/control/lista', (req, res) => {
  const sql = `
    SELECT 
      p.id_prestamo,
      p.num_colaborador,
      p.nombre_colaborador,
      p.nombre_equipo,
      p.gerencia AS departamento,
      COALESCE(h.nombre_hotel, 'Sin hotel') AS hotel,
      COALESCE(h.id_hotel, 0) AS id_hotel,
      IFNULL(h.estado, 'BAJA') AS estado_hotel,
      p.fecha_prestamo,
      p.fecha_devolucion,
      COUNT(pe.id_equipo) AS total_equipos,
      p.usuario_creador_id
    FROM prestamos p
    LEFT JOIN prestamo_equipos pe ON p.id_prestamo = pe.id_prestamo
    LEFT JOIN inventario i ON pe.id_equipo = i.id_equipo
    LEFT JOIN hoteles h ON p.id_hotel_origen = h.id_hotel
    WHERE i.id_estado IN (1, 2) OR i.id_estado IS NULL
    GROUP BY p.id_prestamo
    ORDER BY p.id_prestamo DESC
  `;

  db.query(sql, (err, results) => {
    if (err) {
      console.error('❌ Error en consulta SQL:', err.message);
      return res.status(500).json({ success: false, error: err.message });
    }
    res.json({ success: true, prestamos: results });
  });
});


// Préstamos vencidos y próximos a vencer (hasta 5 días desde hoy o ya vencidos)
router.get('/proximos', (req, res) => {
  const sql = `
    SELECT 
      p.id_prestamo,
      p.nombre_colaborador,
      p.correo,
      p.fecha_prestamo,
      p.fecha_devolucion,
      p.prorroga,
      u.nombre AS usuario_creador,
      p.usuario_creador_id,
      COALESCE(h.id_hotel, 0) AS id_hotel,
      IFNULL(h.estado, 'BAJA') AS estado_hotel  -- ✅ AGREGA EL ESTADO DEL HOTEL
    FROM prestamos p
    LEFT JOIN usuarios u ON p.usuario_creador_id = u.id_user
    LEFT JOIN prestamo_equipos pe ON p.id_prestamo = pe.id_prestamo
    LEFT JOIN inventario i ON pe.id_equipo = i.id_equipo
    LEFT JOIN hoteles h ON i.id_hotel = h.id_hotel
    WHERE p.fecha_devolucion IS NOT NULL
    GROUP BY p.id_prestamo
    ORDER BY p.fecha_devolucion ASC
  `;

  db.query(sql, (err, results) => {
    if (err) {
      console.error('❌ Error al obtener préstamos próximos:', err.message);
      return res.status(500).json({ success: false, error: err.message });
    }
    res.json({ success: true, prestamos: results });
  });
});

// Obtener detalles de un préstamo por ID
router.get('/:id', (req, res) => {
  const { id } = req.params;
  const sql = `
SELECT 
  p.num_colaborador,
  p.nombre_colaborador,
  p.direccion,
  p.gerencia,
  p.correo,
  p.fecha_prestamo,
  p.fecha_devolucion,
  p.nombre_equipo,
  p.plataforma,
  p.comentarios,
  p.usuario_creador_id,
  COALESCE(h.id_hotel, 0) AS id_hotel,
  IFNULL(h.estado, 'BAJA') AS estado_hotel
FROM prestamos p
LEFT JOIN hoteles h ON p.id_hotel_origen = h.id_hotel   -- 👈 igual que RESGUARDO
WHERE p.id_prestamo = ?
  `;

  db.query(sql, [id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    if (results.length === 0) return res.status(404).json({ error: 'Préstamo no encontrado' });

    res.json({ success: true, prestamo: results[0] });
  });
});

// Obtener equipos asignados a un préstamo
router.get('/:id/equipos', (req, res) => {
  const { id } = req.params;

  const sql = `
    SELECT 
      pe.id_equipo,
      i.id_hotel, -- ✅ Agregamos el hotel de origen
      e.numero_serie,
      t.nombre_tipo AS tipo,
      m.nombre_marca AS marca,
      mo.nombre_modelo AS modelo,
      pe.asignado_por
    FROM prestamo_equipos pe
    INNER JOIN equipos e ON pe.id_equipo = e.id_equipo
    JOIN inventario i ON e.id_equipo = i.id_equipo
    LEFT JOIN tipo_equipo t ON e.id_tipo_equipo = t.id_tipo_equipo
    LEFT JOIN marca m ON e.id_marca = m.id_marca
    LEFT JOIN modelo mo ON e.id_modelo = mo.id_modelo
    WHERE pe.id_prestamo = ?
  `;

  db.query(sql, [id], (err, result) => {
    if (err) return res.status(500).json({ success: false, error: err.message });
    res.json({ success: true, equipos: result });
  });
});

// Asignar equipos al resguardo

router.post('/:id/equipos', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { id_equipo, asignado_por } = req.body;

  if (!id_equipo || !asignado_por) {
    return res.status(400).json({ success: false, mensaje: '❌ Faltan datos: id_equipo o asignado_por' });
  }

  const verificar = `SELECT * FROM prestamo_equipos WHERE id_prestamo = ? AND id_equipo = ?`;
  db.query(verificar, [id, id_equipo], (err, rows) => {
    if (err) return res.status(500).json({ success: false, mensaje: `❌ ${err.message}` });
    if (rows.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Este equipo ya fue asignado.' });
    }

    const insertar = `
      INSERT INTO prestamo_equipos (id_prestamo, id_equipo, asignado_por)
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
              asignado_a: 'PRESTAMO'
            });
          }

            // ✅ REGISTRO DEL LOG
registrarLogEquipo({
  id_equipo: parseInt(id_equipo),
  accion: 'ASIGNADO A PRÉSTAMO',
  id_usuario: parseInt(asignado_por),
  descripcion: `Equipo asignado al préstamo #${id}`,
  datos_nuevos: {
    estado: 'ASIGNADO',
    tipo: 'PRÉSTAMO',
    id_prestamo: parseInt(id)
  }
});


          io.emit('prestamo_equipo_actualizado', {
            id_prestamo: parseInt(id),
            accion: 'ASIGNADO',
            id_equipo: parseInt(id_equipo)
          });

          const verificarHotel = `SELECT id_hotel_origen FROM prestamos WHERE id_prestamo = ?`;
          db.query(verificarHotel, [id], (err2, rows2) => {
            if (err2) return res.status(500).json({ success: false, mensaje: `❌ ${err2.message}` });

            const yaTieneHotel = rows2[0]?.id_hotel_origen;
            if (!yaTieneHotel) {
              const getHotelEquipo = `SELECT h.id_hotel, h.nombre_hotel, h.estado FROM inventario i JOIN hoteles h ON i.id_hotel = h.id_hotel WHERE i.id_equipo = ?`;
              db.query(getHotelEquipo, [id_equipo], (err3, rows3) => {
                if (err3 || rows3.length === 0) return res.status(500).json({ success: false, mensaje: `❌ ${err3?.message || 'No se encontró hotel'}` });

                const hotel = rows3[0];
                const updatePrestamo = `UPDATE prestamos SET id_hotel_origen = ? WHERE id_prestamo = ?`;
                db.query(updatePrestamo, [hotel.id_hotel, id], (err4) => {
                  if (err4) return res.status(500).json({ success: false, mensaje: `❌ ${err4.message}` });

                  // ✅ Emitir actualización con el hotel
                  io.emit('prestamo_actualizado', {
                    id_prestamo: parseInt(id),
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


 // cierre de router.post

// Eliminar equipo de un préstamo
router.delete('/:id/equipos/:id_equipo', (req, res) => {
  const io = req.io;
  const { id, id_equipo } = req.params;

  const eliminar = `DELETE FROM prestamo_equipos WHERE id_prestamo = ? AND id_equipo = ?`;
  const restaurarEstado = `UPDATE inventario SET id_estado = 1 WHERE id_equipo = ?`;

  db.query(eliminar, [id, id_equipo], (err) => {
    if (err) return res.status(500).json({ success: false, error: err.message });

    db.query(restaurarEstado, [id_equipo], (err2) => {
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


      io.emit('prestamo_equipo_actualizado', {
        id_prestamo: parseInt(id),
        accion: 'ELIMINADO',
        id_equipo: parseInt(id_equipo)
      });

        // ✅ LOG: equipo restaurado de préstamo
       const id_usuario = req.headers['id_usuario']; // 🔑 lo envías desde el frontend
        registrarLogEquipo({
          id_equipo: parseInt(id_equipo),
          accion: 'RETIRADO DE PRÉSTAMO',
          id_usuario: parseInt(id_usuario),
          descripcion: `Equipo retirado del préstamo #${id}`,
          datos_nuevos: {
            estado: 'ALTA',
            tipo: 'LIBERADO'
          }
        });

      res.json({ success: true, mensaje: '✅ Equipo eliminado del préstamo y restaurado a ALTA' });
    });
  });
});
});

// Actualizar préstamo
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
    comentarios,
    fecha_prestamo,
    fecha_devolucion
  } = req.body;

  if (!numColaborador || !nombre || !nombreEquipo || !fecha_prestamo) {
    return res.status(400).json({ success: false, mensaje: '❌ Faltan campos obligatorios.' });
  }

  // Verificar nombre equipo duplicado
  const sqlVerificar = `
    SELECT id_prestamo, fecha_devolucion 
    FROM prestamos 
    WHERE nombre_equipo = ? AND id_prestamo != ?
  `;

  db.query(sqlVerificar, [nombreEquipo, id], (err, resultados) => {
    if (err) return res.status(500).json({ success: false, error: err.message });

    if (resultados.length > 0) {
      return res.status(400).json({
        success: false,
        mensaje: '❌ El nombre del equipo ya está asignado a otro préstamo.'
      });
    }

    // 👇 Compara fecha_devolucion actual vs nueva
    const sqlGetFecha = 'SELECT fecha_devolucion FROM prestamos WHERE id_prestamo = ?';
    db.query(sqlGetFecha, [id], (err2, rows) => {
      if (err2) return res.status(500).json({ success: false, error: err2.message });

      const fechaActual = rows[0]?.fecha_devolucion?.toISOString().split('T')[0] || null;

      let sqlActualizar;
      if (fechaActual !== fecha_devolucion) {
        // 🚩 Incrementa prórroga +1 si la fecha cambia
        sqlActualizar = `
          UPDATE prestamos
          SET 
            num_colaborador = ?,
            nombre_colaborador = ?,
            direccion = ?,
            gerencia = ?,
            correo = ?,
            nombre_equipo = ?,
            plataforma = ?,
            comentarios = ?,
            fecha_prestamo = ?,
            fecha_devolucion = ?,
            prorroga = prorroga + 1
          WHERE id_prestamo = ?
        `;
      } else {
        // Sin cambio → NO incrementa prórroga
        sqlActualizar = `
          UPDATE prestamos
          SET 
            num_colaborador = ?,
            nombre_colaborador = ?,
            direccion = ?,
            gerencia = ?,
            correo = ?,
            nombre_equipo = ?,
            plataforma = ?,
            comentarios = ?,
            fecha_prestamo = ?,
            fecha_devolucion = ?
          WHERE id_prestamo = ?
        `;
      }

      const valores = [
        numColaborador,
        nombre,
        direccion || null,
        gerencia || null,
        correo || null,
        nombreEquipo,
        plataforma || null,
        comentarios || null,
        fecha_prestamo,
        fecha_devolucion,
        id
      ];

   db.query(sqlActualizar, valores, (err3) => {
        if (err3) return res.status(500).json({ success: false, error: err3.message });

        db.query('SELECT * FROM prestamos WHERE id_prestamo = ?', [id], (err4, rows2) => {
          if (err4 || rows2.length === 0) {
            return res.status(500).json({ success: false, error: '❌ Error al recuperar préstamo actualizado.' });
          }

          const actualizado = rows2[0];
          io.emit('prestamo_actualizado', actualizado); // ✅

          res.json({ success: true, mensaje: '✅ Préstamo actualizado correctamente', prestamo: actualizado });
        });
      });
    });
  });
});


// Eliminar préstamo
router.delete('/eliminar/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;

  const obtenerEquipos = 'SELECT id_equipo FROM prestamo_equipos WHERE id_prestamo = ?';
  db.query(obtenerEquipos, [id], (err, resultados) => {
    if (err) return res.status(500).json({ success: false, error: err.message });

    const equipos = resultados.map(r => r.id_equipo);

    if (equipos.length > 0) {
      const restaurarEstado = 'UPDATE inventario SET id_estado = 1 WHERE id_equipo IN (?)';
      db.query(restaurarEstado, [equipos], (err2) => {
        if (err2) return res.status(500).json({ success: false, error: err2.message });

        // Obtener los id_inventario y estado para cada equipo restaurado
        const placeholders = equipos.map(() => '?').join(',');
        const sqlEstados = `
          SELECT i.id_inventario, e.nombre_estado
          FROM inventario i
          JOIN estado_equipo e ON i.id_estado = e.id_estado
          WHERE i.id_equipo IN (${placeholders})
        `;

        db.query(sqlEstados, equipos, (err3, estados) => {
          if (!err3 && estados.length > 0) {
            estados.forEach(row => {
              io.emit('estado_equipo_actualizado', {
                id_inventario: row.id_inventario,
                id_estado: 1,
                estado: row.nombre_estado,
                asignado_a: null
              });
            });
          }

          eliminarRelacionYPrestamo();
        });
      });
    } else {
      eliminarRelacionYPrestamo();
    }

    function eliminarRelacionYPrestamo() {
      const eliminarRelacion = 'DELETE FROM prestamo_equipos WHERE id_prestamo = ?';
      const eliminarPrestamo = 'DELETE FROM prestamos WHERE id_prestamo = ?';

      db.query(eliminarRelacion, [id], (err3) => {
        if (err3) return res.status(500).json({ success: false, error: err3.message });

        db.query(eliminarPrestamo, [id], (err4) => {
          if (err4) return res.status(500).json({ success: false, error: err4.message });

          io.emit('prestamo_eliminado', parseInt(id));
          io.emit('prestamo_equipo_actualizado', {
            id_prestamo: parseInt(id),
            accion: 'PRESTAMO_ELIMINADO'
          });

             // ✅ Registrar logs por equipo
      const id_usuario = parseInt(req.headers['user-id'], 10);
      equipos.forEach(id_equipo => {
        registrarLogEquipo({
          id_equipo,
          accion: 'REMOVIDO DE PRÉSTAMO',
          id_usuario,
          descripcion: `Equipo liberado automáticamente al eliminar préstamo #${id}`,
          datos_nuevos: {
            estado: 'ALTA',
            motivo: 'ELIMINACIÓN DE PRÉSTAMO',
            id_prestamo: parseInt(id)
          }
        });
      });


          res.json({ success: true, mensaje: '✅ Préstamo eliminado correctamente' });
        });
      });
    }
  });
});


router.put('/devolver/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;

  const obtenerEquipos = 'SELECT id_equipo FROM prestamo_equipos WHERE id_prestamo = ?';
  db.query(obtenerEquipos, [id], (err, resultados) => {
    if (err) return res.status(500).json({ success: false, error: err.message });

    const equipos = resultados.map(r => r.id_equipo);

    if (equipos.length > 0) {
      const restaurarEstado = 'UPDATE inventario SET id_estado = 1 WHERE id_equipo IN (?)';
      db.query(restaurarEstado, [equipos], (err2) => {
        if (err2) return res.status(500).json({ success: false, error: err2.message });

        // Obtener los id_inventario y estado para emitir eventos
        const placeholders = equipos.map(() => '?').join(',');
        const sqlEstados = `
          SELECT i.id_inventario, e.nombre_estado
          FROM inventario i
          JOIN estado_equipo e ON i.id_estado = e.id_estado
          WHERE i.id_equipo IN (${placeholders})
        `;

        db.query(sqlEstados, equipos, (err3, estados) => {
          if (!err3 && estados.length > 0) {
            estados.forEach(row => {
              io.emit('estado_equipo_actualizado', {
                id_inventario: row.id_inventario,
                id_estado: 1,
                estado: row.nombre_estado,
                asignado_a: null
              });
            });
          }

          eliminarRelacionYPrestamo();
        });
      });
    } else {
      res.json({ success: true, mensaje: '⚠ Sin equipos asignados.' });
    }

    function eliminarRelacionYPrestamo() {
      const eliminarRelacion = 'DELETE FROM prestamo_equipos WHERE id_prestamo = ?';
      const eliminarPrestamo = 'DELETE FROM prestamos WHERE id_prestamo = ?';

      db.query(eliminarRelacion, [id], (err3) => {
        if (err3) return res.status(500).json({ success: false, error: err3.message });

        db.query(eliminarPrestamo, [id], (err4) => {
          if (err4) return res.status(500).json({ success: false, error: err4.message });

          // Emitir que el préstamo fue cerrado
          io.emit('prestamo_equipo_actualizado', {
            id_prestamo: parseInt(id),
            accion: 'PRESTAMO_DEVUELTO'
          });

          res.json({ success: true, mensaje: '✅ Devuelto y equipos en ALTA.' });
        });
      });
    }
  });
});



module.exports = router;
