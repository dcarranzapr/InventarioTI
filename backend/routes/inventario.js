const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener equipos con joins
router.get('/', (req, res) => {
  const sql = `
  SELECT 
      i.id_inventario,
      e.id_equipo,
      i.id_hotel,
      i.id_estado AS id_estado,
      h.nombre_hotel AS hotel,
      h.estado AS estado_hotel,
      te.nombre_tipo AS tipo,
      ma.nombre_marca AS marca,
      m.nombre_modelo AS modelo,
      e.numero_serie AS numeroSerie,
      est.nombre_estado AS estado,
      CASE
        WHEN re.id_equipo IS NOT NULL THEN 'RESGUARDO'
        WHEN pe.id_equipo IS NOT NULL THEN 'PRESTAMO'
        ELSE 'NINGUNO'
      END AS asignado_a
    FROM inventario i
    JOIN equipos e ON i.id_equipo = e.id_equipo
    JOIN modelo m ON e.id_modelo = m.id_modelo
    JOIN tipo_equipo te ON m.id_tipo_equipo = te.id_tipo_equipo
    JOIN marca ma ON m.id_marca = ma.id_marca
    JOIN hoteles h ON i.id_hotel = h.id_hotel
    JOIN estado_equipo est ON i.id_estado = est.id_estado
    LEFT JOIN resguardo_equipos re ON re.id_equipo = e.id_equipo
    LEFT JOIN prestamo_equipos pe ON pe.id_equipo = e.id_equipo
    WHERE i.id_estado IN (1, 2) -- Solo ALTA o ASIGNADO
    ORDER BY i.id_inventario DESC
  `;

  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, equipos: results });
  });
});


// Obtener equipos disponibles
router.get('/disponibles', (req, res) => {
  const sql = `
    SELECT 
      e.id_equipo, 
      e.numero_serie, 
      t.nombre_tipo AS tipo, 
      m.nombre_marca AS marca, 
      mo.nombre_modelo AS modelo,
      i.id_hotel,
      i.id_estado AS estado_equipo,
      h.estado AS estado_hotel
    FROM inventario i
    JOIN equipos e ON i.id_equipo = e.id_equipo
    JOIN modelo mo ON e.id_modelo = mo.id_modelo
    JOIN marca m ON mo.id_marca = m.id_marca
    JOIN tipo_equipo t ON mo.id_tipo_equipo = t.id_tipo_equipo
    JOIN hoteles h ON i.id_hotel = h.id_hotel
    WHERE i.id_estado = 1 AND h.estado = 'ALTA'  -- ✅ Validación añadida
  `;

  db.query(sql, (err, results) => {
    if (err) {
      console.error('❌ Error en consulta /disponibles:', err.message);
      return res.status(500).json({ success: false, error: err.message });
    }
    res.json({ success: true, equipos: results });
  });
});


// Obtener datos de un equipo por ID para edición
router.get('/:id', (req, res) => {
  const id = req.params.id;
  console.log('🔍 Buscando equipo con ID:', id);

  const sqlVerificar = 'SELECT * FROM inventario WHERE id_inventario = ?';
  db.query(sqlVerificar, [id], (err, result) => {
    if (err) return res.status(500).json({ error: err.message });
    if (result.length === 0) return res.status(404).json({ mensaje: '❌ Inventario no encontrado' });

    const sql = `
      SELECT 
        i.id_inventario, 
        e.id_equipo,
        i.id_hotel, 
        i.id_estado AS id_estado,
        e.id_equipo, 
        e.numero_serie,
        m.id_modelo, 
        m.id_tipo_equipo AS id_tipo_equipo, 
        m.id_marca AS id_marca,
        e.id_sistema_operativo AS id_so, 
        e.id_memoria_ram, 
        e.id_procesador,
        e.id_tipo_disco_duro AS id_disco, 
        e.id_almacenamiento,
        a.id_proveedor, 
        a.numero_factura, 
        a.fecha_compra,
        CASE 
          WHEN a.numero_factura IS NULL THEN 1 
          ELSE 0 
        END AS mas_de_tres_anios
      FROM inventario i
      JOIN equipos e ON i.id_equipo = e.id_equipo
      JOIN modelo m ON e.id_modelo = m.id_modelo
      LEFT JOIN almacen a ON e.id_equipo = a.id_equipo
      WHERE i.id_inventario = ?
    `;

    db.query(sql, [id], (err, results) => {
      if (err) return res.status(500).json({ error: err.message });
      if (results.length === 0) return res.status(404).json({ mensaje: '❌ Equipo no encontrado o relaciones incompletas' });
      res.json({ equipo: results[0] });
    });
  });
});

// Obtener todos los estados de equipo (ALTA, BAJA, etc.)
router.get('/estados/lista', (req, res) => {
  const sql = 'SELECT * FROM estado_equipo';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ estados: results });
  });
});


// Actualizar un equipo existente
router.put('/:id', (req, res) => {
  const io = req.io;
  const id_inventario = req.params.id;

  const {
    modelo, numeroSerie, hotel,
    sistemaOperativo, ram, procesador, discoDuro, almacenamiento,
    proveedor, factura, fechaCompra, masDeTresAnios, estado // 👈 este es id_estado
  } = req.body;

  // Verificar número de serie duplicado
  const verificarSerie = `
    SELECT e.id_equipo FROM equipos e
    JOIN inventario i ON i.id_equipo = e.id_equipo
    WHERE e.numero_serie = ? AND i.id_inventario != ?
  `;

  db.query(verificarSerie, [numeroSerie, id_inventario], (err, result) => {
    if (err) return res.status(500).json({ error: '❌ Error al verificar número de serie' });
    if (result.length > 0) {
      return res.status(400).json({ error: '❌ El número de serie ya está registrado en otro equipo' });
    }

    const actualizarEquipo = `
      UPDATE equipos SET 
        numero_serie = ?, 
        id_modelo = ?, 
        id_sistema_operativo = ?, 
        id_memoria_ram = ?, 
        id_procesador = ?, 
        id_tipo_disco_duro = ?, 
        id_almacenamiento = ?
      WHERE id_equipo = (
        SELECT id_equipo FROM inventario WHERE id_inventario = ?
      )
    `;

    const actualizarInventario = `
      UPDATE inventario SET 
        id_hotel = ?,
        id_estado = ?
      WHERE id_inventario = ?
    `;

    const actualizarAlmacen = `
      UPDATE almacen SET 
        numero_factura = ?, 
        id_proveedor = ?, 
        fecha_compra = ?
      WHERE id_equipo = (
        SELECT id_equipo FROM inventario WHERE id_inventario = ?
      )
    `;

    // Ejecutar actualizaciones
    db.query(actualizarEquipo, [
      numeroSerie, modelo, sistemaOperativo || null, ram || null, procesador || null, discoDuro || null, almacenamiento || null, id_inventario
    ], (err) => {
      if (err) return res.status(500).json({ error: 'Error actualizando equipo' });

      db.query(actualizarInventario, [hotel, estado, id_inventario], (err) => {
        if (err) return res.status(500).json({ error: 'Error actualizando inventario' });

        const actualizarAlmacenFn = (cb) => {
          if (masDeTresAnios) {
            db.query(actualizarAlmacen, [null, null, null, id_inventario], cb);
          } else {
            db.query(actualizarAlmacen, [factura, proveedor, fechaCompra, id_inventario], cb);
          }
        };

        // 👉 Si se puso en BAJA, crear baja si no existe
     const verificarYCrearBaja = (callback) => {
  if (parseInt(estado) === 3) {
    const sqlBuscarEquipo = `SELECT id_equipo FROM inventario WHERE id_inventario = ?`;
    db.query(sqlBuscarEquipo, [id_inventario], (err, result) => {
      if (err) return callback(err);
      const id_equipo = result[0].id_equipo;

      const sqlVerificar = 'SELECT COUNT(*) AS existe FROM bajas WHERE id_equipo = ?';
      db.query(sqlVerificar, [id_equipo], (err, rows) => {
        if (err) return callback(err);

        if (rows[0].existe === 0) {
          const sqlDescripcion = `
            SELECT t.nombre_tipo AS tipo, ma.nombre_marca AS marca, mo.nombre_modelo AS modelo, h.nombre_hotel AS hotel
            FROM equipos e
            JOIN modelo mo ON e.id_modelo = mo.id_modelo
            JOIN marca ma ON mo.id_marca = ma.id_marca
            JOIN tipo_equipo t ON mo.id_tipo_equipo = t.id_tipo_equipo
            JOIN inventario i ON i.id_equipo = e.id_equipo
            JOIN hoteles h ON i.id_hotel = h.id_hotel
            WHERE e.id_equipo = ?
          `;
          db.query(sqlDescripcion, [id_equipo], (err, rows2) => {
            if (err) return callback(err);

            const desc = `${rows2[0].tipo} ${rows2[0].marca} ${rows2[0].modelo}`;
            const hotelOrigen = rows2[0].hotel;

            const sqlInsert = `
              INSERT INTO bajas (id_equipo, descripcion, hotel_origen)
              VALUES (?, ?, ?)
            `;
            db.query(sqlInsert, [id_equipo, desc, hotelOrigen], (err) => {
              if (err) return callback(err);

              // 🔔 Emite actualización en tiempo real
              io.emit('baja_actualizada', { tipo: 'CREADA', id_equipo });

              return callback(); // Listo!
            });
          });
        } else {
          return callback(); // Ya existía, no hay error
        }
      });
    });
  } else {
    return callback(); // No es BAJA, no hace nada
  }
};


        actualizarAlmacenFn((err) => {
          if (err) return res.status(500).json({ error: 'Error actualizando datos de almacen' });

          verificarYCrearBaja((err) => {
  if (err) return res.status(500).json({ error: 'Error creando baja automáticamente' });

 const estadoNum = parseInt(estado);

if (estadoNum === 1 || estadoNum === 2) {
  io.emit('estado_equipo_actualizado', {
    id_inventario: parseInt(id_inventario),
    id_estado: estadoNum
  });
}

io.emit('inventario_actualizado'); // Se mantiene para otras vistas

res.json({ mensaje: 'Equipo actualizado correctamente.' });

});

});

      });
    });
  });
});


// Eliminar un equipo
router.delete('/:id', (req, res) => {
  const io = req.io;
  const id_inventario = req.params.id;

  const sqlBuscarEquipo = `SELECT id_equipo FROM inventario WHERE id_inventario = ?`;

  db.query(sqlBuscarEquipo, [id_inventario], (err, result) => {
    if (err) return res.status(500).json({ error: err.message });
    if (result.length === 0) return res.status(404).json({ mensaje: 'Inventario no encontrado' });

    const id_equipo = result[0].id_equipo;

    const sqlEliminarAlmacen = `DELETE FROM almacen WHERE id_equipo = ?`;
    db.query(sqlEliminarAlmacen, [id_equipo], (err) => {
      if (err) return res.status(500).json({ error: err.message });

      const sqlEliminarInventario = `DELETE FROM inventario WHERE id_inventario = ?`;
      db.query(sqlEliminarInventario, [id_inventario], (err) => {
        if (err) return res.status(500).json({ error: err.message });

        const sqlEliminarEquipo = `DELETE FROM equipos WHERE id_equipo = ?`;
        db.query(sqlEliminarEquipo, [id_equipo], (err) => {
          if (err) return res.status(500).json({ error: err.message });

         io.emit('inventario_actualizado');
        
          res.json({ mensaje: 'Equipo eliminado correctamente de todas las tablas' });
        });
      });
    });
  });
});


module.exports = router;
