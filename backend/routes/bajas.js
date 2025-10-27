// backend/routes/bajas.js
const express = require('express');
const router = express.Router();
const db = require('../db');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const ExcelJS = require('exceljs');
const { registrarLogEquipo } = require('../middlewares/log');

// Asegurar carpeta:
const uploadPath = path.join(__dirname, '..', 'uploads', 'bajas');
if (!fs.existsSync(uploadPath)) {
  fs.mkdirSync(uploadPath, { recursive: true });
}

const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, uploadPath);
  },
  filename: function (req, file, cb) {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    cb(null, uniqueSuffix + path.extname(file.originalname));
  }
});

const upload = multer({ storage: storage });

// ✅ Obtener equipos ALTA con tipo, marca, modelo y número de serie
router.get('/equipos-alta', (req, res) => {
  const sql = `
SELECT 
  e.id_equipo, 
  e.numero_serie, 
  t.nombre_tipo AS tipo, 
  m.nombre_marca AS marca, 
  mo.nombre_modelo AS modelo,
  i.id_hotel,
  h.nombre_hotel AS hotel_origen
FROM inventario i
JOIN equipos e ON i.id_equipo = e.id_equipo
JOIN modelo mo ON e.id_modelo = mo.id_modelo
JOIN marca m ON mo.id_marca = m.id_marca
JOIN tipo_equipo t ON mo.id_tipo_equipo = t.id_tipo_equipo
JOIN hoteles h ON i.id_hotel = h.id_hotel
WHERE i.id_estado = 1 AND h.estado = 'ALTA'
  `;

  db.query(sql, (err, results) => {
    if (err) {
      console.error('❌ Error en /equipos-alta:', err.message);
      return res.status(500).json({ success: false, error: err.message });
    }
    res.json(results); // 🔥 Devuelve array directo
  });
});

// ✅ Crear Baja con descripción y evidencias
router.post('/', upload.array('evidencias'), (req, res) => {
  const { id_equipo, motivo, hotel_origen, descripcion } = req.body;
  const archivos = req.files;

  if (!id_equipo || !motivo || !descripcion) {
    return res.status(400).json({ mensaje: '❌ Faltan datos obligatorios' });
  }

  // 🔥 1️⃣ Insertar en bajas
  const id_usuario = parseInt(req.headers['user-id'], 10); // ✅ importante


  const sqlBaja = 'INSERT INTO bajas (id_equipo, motivo, hotel_origen, descripcion) VALUES (?, ?, ?, ?)';
  db.query(sqlBaja, [id_equipo, motivo, hotel_origen, descripcion], (err, result) => {
    if (err) {
      console.error(err);
      return res.status(500).json({ mensaje: '❌ Error insertando baja' });
    }

    const id_baja = result.insertId;

    // 🔥 2️⃣ Insertar evidencias
    const promises = archivos.map(archivo => {
      return new Promise((resolve, reject) => {
        const sqlEvidencia = 'INSERT INTO baja_evidencias (id_baja, nombre_archivo, ruta_archivo) VALUES (?, ?, ?)';
        db.query(sqlEvidencia, [id_baja, archivo.filename, archivo.path], (err) => {
          if (err) reject(err);
          else resolve();
        });
      });
    });

    // 🔥 3️⃣ Actualizar inventario después de guardar evidencias
    Promise.all(promises)
      .then(() => {
        const sqlUpdate = 'UPDATE inventario SET id_estado = 3 WHERE id_equipo = ?';
        db.query(sqlUpdate, [id_equipo], (err) => {
          if (err) {
            console.error(err);
            return res.status(500).json({ mensaje: '❌ Error actualizando inventario' });
          }

registrarLogEquipo({
  id_equipo: parseInt(id_equipo),
  accion: 'BAJA',
  id_usuario,
  descripcion: `Motivo: ${motivo} – ${descripcion}`,
  datos_nuevos: {
    estado: 'BAJA',
    hotel_origen,
    motivo
  }
});


          req.io.emit('baja_actualizada', { tipo: 'CREADA', id_baja });
           req.io.emit('inventario_actualizado', { tipo: 'BAJA_CREADA', id_equipo });

          return res.json({ mensaje: '✅ Baja registrada correctamente', id_baja });
        });
      })
      .catch(err => {
        console.error(err);
        return res.status(500).json({ mensaje: '❌ Error insertando evidencias' });
      });

  });
});

router.get('/exportar-excel', async (req, res) => {
  const { hotel } = req.query; // 👈 recibe el nombre del hotel (opcional)

  let sql = `
    SELECT 
      b.id_baja, 
      b.descripcion, 
      b.motivo, 
      b.hotel_origen,
      eq.numero_serie,
      evi.nombre_archivo
    FROM bajas b
    JOIN equipos eq ON b.id_equipo = eq.id_equipo
    LEFT JOIN baja_evidencias evi ON b.id_baja = evi.id_baja
  `;

  const params = [];

  if (hotel) {
    sql += ` WHERE b.hotel_origen = ?`;
    params.push(hotel);
  }

  sql += ` ORDER BY b.id_baja DESC`;

  db.query(sql, params, async (err, rows) => {
    if (err) {
      console.error(err);
      return res.status(500).json({ error: err.message });
    }

    const agrupadas = {};
    rows.forEach(row => {
      if (!agrupadas[row.id_baja]) {
        agrupadas[row.id_baja] = {
          id_baja: row.id_baja,
          descripcion: row.descripcion,
          motivo: row.motivo,
          hotel_origen: row.hotel_origen,
          numero_serie: row.numero_serie,
          evidencias: []
        };
      }
      if (row.nombre_archivo) {
        agrupadas[row.id_baja].evidencias.push(row.nombre_archivo);
      }
    });

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Bajas');

    worksheet.columns = [
      { header: 'CANTIDAD', key: 'cantidad', width: 10 },
      { header: 'ID', key: 'id_vacio', width: 10 },
      { header: 'DESCRIPCIÓN', key: 'descripcion', width: 30 },
      { header: 'FOTO', key: 'evidencia', width: 30 },
      { header: 'MOTIVO DE BAJA', key: 'motivo', width: 20 },
      { header: '# SERIE', key: 'numero_serie', width: 25 },
      { header: 'Precio', key: 'precio', width: 15 },
      { header: 'Importe', key: 'importe', width: 15 },
      { header: 'Destino', key: 'destino', width: 15 },
      { header: 'Comentarios', key: 'comentarios', width: 20 }
    ];

    let rowIndex = 2;

    if (Object.keys(agrupadas).length > 0) {
      for (const baja of Object.values(agrupadas)) {
        const evidencias = baja.evidencias.length ? baja.evidencias : [null];

        for (const evi of evidencias) {
          worksheet.addRow({
            cantidad: 1,
            id_vacio: '',
            descripcion: baja.descripcion,
            evidencia: '',
            motivo: baja.motivo,
            numero_serie: baja.numero_serie,
            precio: '',
            importe: '',
            destino: 'BAJAS',
            comentarios: 'SIN REPARACION'
          });

          if (evi) {
            const imgPath = path.join(__dirname, '..', 'uploads', 'bajas', evi);

            if (fs.existsSync(imgPath)) {
              const imageId = workbook.addImage({
                filename: imgPath,
                extension: path.extname(evi).substring(1)
              });

              worksheet.addImage(imageId, {
                tl: { col: 3, row: rowIndex - 1 },
                ext: { width: 120, height: 120 }
              });

              worksheet.getRow(rowIndex).height = 90;
            }
          }

          rowIndex++;
        }
      }
    } else {
      worksheet.addRow({
        cantidad: 1,
        id_vacio: '',
        descripcion: '',
        evidencia: '',
        motivo: '',
        numero_serie: '',
        precio: '',
        importe: '',
        destino: '',
        comentarios: ''
      });
    }

    res.setHeader(
      'Content-Type',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );
    res.setHeader(
      'Content-Disposition',
      `attachment; filename=ControlBajas_${hotel || 'General'}.xlsx`
    );

    await workbook.xlsx.write(res);
    res.end();
  });
});


// ✅ Actualizar motivo y agregar evidencias nuevas
router.put('/:id_baja', upload.array('evidencias'), (req, res) => {
  const { id_baja } = req.params;
  const { motivo } = req.body;
  const archivos = req.files;

  if (!motivo) {
    return res.status(400).json({ mensaje: '❌ Motivo obligatorio.' });
  }

  // 1️⃣ Actualizar motivo
  const sqlUpdate = 'UPDATE bajas SET motivo = ? WHERE id_baja = ?';
  db.query(sqlUpdate, [motivo, id_baja], (err) => {
    if (err) {
      console.error(err);
      return res.status(500).json({ mensaje: '❌ Error actualizando motivo.' });
    }

    // 2️⃣ Si hay nuevas evidencias, insertarlas
    if (archivos && archivos.length) {
      const promises = archivos.map(archivo => {
        return new Promise((resolve, reject) => {
          const sqlEvi = 'INSERT INTO baja_evidencias (id_baja, nombre_archivo, ruta_archivo) VALUES (?, ?, ?)';
          db.query(sqlEvi, [id_baja, archivo.filename, archivo.path], (err) => {
            if (err) reject(err);
            else resolve();
          });
        });
      });

      Promise.all(promises)
        .then(() => {
          req.io.emit('baja_actualizada', { tipo: 'EDITADA', id_baja }); // ✅ EMIT AQUÍ
          res.json({ mensaje: '✅ Baja actualizada con nuevas evidencias.' });
        })
        .catch(err => {
          console.error(err);
          res.status(500).json({ mensaje: '❌ Error guardando evidencias.' });
        });

    } else {
      req.io.emit('baja_actualizada', { tipo: 'ACTUALIZADA', id_baja }); // ✅ EMIT AQUÍ
      res.json({ mensaje: '✅ Baja actualizada sin nuevas evidencias.' });
    }
  });
});


// ✅ Traer todas las bajas con su equipo y descripción
router.get('/control', (req, res) => {
  const sql = `
    SELECT 
      b.id_baja, 
      b.descripcion, 
      b.motivo, 
      b.hotel_origen,
      h.id_hotel,               -- ✅ YA ESTÁ EN EL SELECT
      h.estado AS estado_hotel,
      eq.id_equipo,
      eq.numero_serie,
      evi.id_evidencia, 
      evi.nombre_archivo, 
      evi.ruta_archivo
    FROM bajas b
    JOIN equipos eq ON b.id_equipo = eq.id_equipo
    JOIN inventario i ON eq.id_equipo = i.id_equipo
    JOIN hoteles h ON i.id_hotel = h.id_hotel
    LEFT JOIN baja_evidencias evi ON b.id_baja = evi.id_baja
    ORDER BY b.id_baja DESC
  `;

  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    const agrupadas = {};
    results.forEach(row => {
      if (!agrupadas[row.id_baja]) {
  agrupadas[row.id_baja] = {
    id_baja: row.id_baja,
    descripcion: row.descripcion,
    motivo: row.motivo,
    hotel_origen: row.hotel_origen,
    id_hotel: row.id_hotel,
    id_equipo: row.id_equipo,
    numero_serie: row.numero_serie,
    estado_hotel: row.estado_hotel, // 👈 AGREGA ESTO
    evidencias: []
  };
}
      if (row.id_evidencia) {
        agrupadas[row.id_baja].evidencias.push({
          id_evidencia: row.id_evidencia,
          nombre_archivo: row.nombre_archivo,
          ruta_archivo: row.ruta_archivo
        });
      }
    });

    res.json(Object.values(agrupadas));
  });
});


// ✅ Obtener detalles de una baja por ID
router.get('/:id_baja', (req, res) => {
  const { id_baja } = req.params;

  const sql = `
    SELECT 
      b.id_baja,
      b.motivo,
      b.descripcion,
      b.hotel_origen,
      eq.numero_serie,
      t.nombre_tipo AS tipo,
      m.nombre_marca AS marca,
      mo.nombre_modelo AS modelo,
      evi.id_evidencia,
      evi.nombre_archivo
    FROM bajas b
    JOIN equipos eq ON b.id_equipo = eq.id_equipo
    JOIN modelo mo ON eq.id_modelo = mo.id_modelo
    JOIN marca m ON mo.id_marca = m.id_marca
    JOIN tipo_equipo t ON mo.id_tipo_equipo = t.id_tipo_equipo
    LEFT JOIN baja_evidencias evi ON b.id_baja = evi.id_baja
    WHERE b.id_baja = ?
  `;

  db.query(sql, [id_baja], (err, rows) => {
    if (err) return res.status(500).json({ error: err.message });

    if (!rows.length) return res.status(404).json({ error: 'Baja no encontrada.' });

    const baja = {
      id_baja: rows[0].id_baja,
      descripcion: rows[0].descripcion,
      motivo: rows[0].motivo,
      hotel_origen: rows[0].hotel_origen,
      numero_serie: rows[0].numero_serie,
      tipo: rows[0].tipo,
      marca: rows[0].marca,
      modelo: rows[0].modelo,
      evidencias: []
    };

    rows.forEach(r => {
      if (r.id_evidencia) {
        baja.evidencias.push({
          id_evidencia: r.id_evidencia,
          nombre_archivo: r.nombre_archivo
        });
      }
    });

    res.json(baja);
  });
});


// ✅ Eliminar una evidencia específica
router.delete('/evidencia/:id_evidencia', (req, res) => {
  const { id_evidencia } = req.params;

  const sqlSel = 'SELECT nombre_archivo FROM baja_evidencias WHERE id_evidencia = ?';
  db.query(sqlSel, [id_evidencia], (err, rows) => {
    if (err) return res.status(500).json({ mensaje: '❌ Error buscando evidencia.' });

    if (!rows.length) return res.status(404).json({ mensaje: '❌ Evidencia no encontrada.' });

    const nombre_archivo = rows[0].nombre_archivo;

    const sqlDel = 'DELETE FROM baja_evidencias WHERE id_evidencia = ?';
    db.query(sqlDel, [id_evidencia], (err) => {
      if (err) return res.status(500).json({ mensaje: '❌ Error eliminando evidencia.' });

      const ruta = path.join(__dirname, '..', 'uploads', 'bajas', nombre_archivo);
      if (fs.existsSync(ruta)) fs.unlinkSync(ruta);

      return res.json({ mensaje: '✅ Evidencia eliminada.' });
    });
  });
});


// ✅ Restaurar equipo: eliminar baja y poner estado = ALTA
router.delete('/restaurar/:id_baja/:id_equipo', (req, res) => {
  const { id_baja, id_equipo } = req.params;

  // 1️⃣ Obtiene evidencias para saber qué archivos borrar
  const sqlEvidencias = 'SELECT nombre_archivo FROM baja_evidencias WHERE id_baja = ?';
  db.query(sqlEvidencias, [id_baja], (err, evidencias) => {
    if (err) {
      console.error(err);
      return res.status(500).json({ mensaje: '❌ Error obteniendo evidencias.' });
    }

    // 2️⃣ Elimina registros de evidencias
    const sqlDelEvidencias = 'DELETE FROM baja_evidencias WHERE id_baja = ?';
    db.query(sqlDelEvidencias, [id_baja], (err) => {
      if (err) {
        console.error(err);
        return res.status(500).json({ mensaje: '❌ Error eliminando evidencias.' });
      }

      // 3️⃣ Elimina la baja
      const sqlDelBaja = 'DELETE FROM bajas WHERE id_baja = ?';
      db.query(sqlDelBaja, [id_baja], (err) => {
        if (err) {
          console.error(err);
          return res.status(500).json({ mensaje: '❌ Error eliminando baja.' });
        }

        // 4️⃣ Cambia el equipo a ALTA
        const sqlAlta = 'UPDATE inventario SET id_estado = 1 WHERE id_equipo = ?';
        db.query(sqlAlta, [id_equipo], (err) => {
          if (err) {
            console.error(err);
            return res.status(500).json({ mensaje: '❌ Error actualizando estado del equipo.' });
          }

          // 5️⃣ Borra archivos del disco
          evidencias.forEach(evi => {
            const ruta = path.join(__dirname, '..', 'uploads', 'bajas', evi.nombre_archivo);
            if (fs.existsSync(ruta)) {
              fs.unlinkSync(ruta);
            }
          });

                // ✅ REGISTRO DE LOG
  const id_usuario = parseInt(req.headers['user-id'], 10);
  registrarLogEquipo({
    id_equipo: parseInt(id_equipo),
    accion: 'RESTAURADO DE BAJA',
    id_usuario,
    descripcion: `Equipo restaurado desde baja #${id_baja}`,
    datos_nuevos: {
      estado: 'ALTA',
      motivo: 'BAJA REVERTIDA'
    }
  });

          req.io.emit('baja_actualizada', { tipo: 'RESTAURADA', id_baja });
           req.io.emit('inventario_actualizado', { tipo: 'RESTAURADA', id_equipo });
           
          return res.json({ mensaje: '✅ Equipo restaurado.' });
        });
      });
    });
  });
});


module.exports = router;
