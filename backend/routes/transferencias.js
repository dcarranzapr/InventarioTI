const express = require('express');
const router = express.Router();
const db = require('../db');

// Crear transferencia nueva
router.post('/', (req, res) => {
  const io = req.io;
  const { id_equipo, id_hotel_origen, id_hotel_destino, creado_por } = req.body;

  const sql = `
    INSERT INTO transferencias 
    (id_equipo, id_hotel_origen, id_hotel_destino, estado, fecha_solicitud, creado_por) 
    VALUES (?, ?, ?, 'PENDIENTE', NOW(), ?)
  `;

  db.query(sql, [id_equipo, id_hotel_origen, id_hotel_destino, creado_por], (err, result) => {
    if (err) {
      console.error('❌ Error SQL:', err.sqlMessage || err);
      return res.status(500).json({ success: false, error: err });
    }
 
  // 🚀 Notifica a todos
    io.emit('transferencia_creada', { id_transferencia: result.insertId });

 
    res.json({ success: true, id_transferencia: result.insertId }); // ✅ <- IMPORTANTE
});
});

// Obtener transferencias pendientes creadas por el usuario actual
router.get('/pendientes/creadas/:id_usuario', (req, res) => {
  const sql = `
    SELECT id_equipo, id_transferencia, creado_por, id_hotel_destino
    FROM transferencias
    WHERE estado = 'PENDIENTE'
  `;
  db.query(sql, (err, result) => {
    if (err) return res.status(500).json({ success: false, error: err });

    const pendientes = {};
    result.forEach(row => {
      pendientes[row.id_equipo] = {
        id_transferencia: row.id_transferencia,
        creado_por: row.creado_por,
        id_hotel_destino: row.id_hotel_destino
      };
    });

    res.json({ success: true, pendientes });
  });
});


// Aceptar transferencia
router.put('/aceptar/:id', (req, res) => {
  const io = req.io;
  const id_transferencia = req.params.id;

  // 1. Obtener detalles de la transferencia
  const sqlDetalle = `
    SELECT t.id_equipo, t.id_hotel_destino
    FROM transferencias t
    WHERE t.id_transferencia = ?
  `;

  db.query(sqlDetalle, [id_transferencia], (err, resultado) => {
    if (err) return res.status(500).json({ success: false, error: err });
    if (resultado.length === 0) return res.status(404).json({ success: false, mensaje: 'Transferencia no encontrada' });

    const { id_equipo, id_hotel_destino } = resultado[0];

    // 2. Actualizar el hotel del equipo en la tabla inventario
    const sqlUpdateInventario = `
      UPDATE inventario
      SET id_hotel = ?
      WHERE id_equipo = ?
    `;

    db.query(sqlUpdateInventario, [id_hotel_destino, id_equipo], (err2) => {
      if (err2) return res.status(500).json({ success: false, error: err2 });

      // 3. Cambiar estado de la transferencia a ACEPTADA
      const sqlAceptar = `
        UPDATE transferencias
        SET estado = 'ACEPTADA', fecha_respuesta = NOW()
        WHERE id_transferencia = ?
      `;

      db.query(sqlAceptar, [id_transferencia], (err3) => {
        if (err3) return res.status(500).json({ success: false, error: err3 });

       // 🚀 Notifica a todos
        io.emit('transferencia_actualizada', { id_transferencia, estado: 'ACEPTADA' });
        io.emit('inventario_actualizado');
    
        res.json({ success: true, mensaje: 'Transferencia aceptada y hotel actualizado.' });
      });
    });
  });
});

// Cancelar transferencia (permite cancelar si tiene permiso sobre hotel de origen)
router.put('/cancelar/:id', (req, res) => {
  const io = req.io;
  const id_transferencia = req.params.id;
  const id_usuario = req.body.id_usuario;

  const verificarSql = `
    SELECT creado_por, id_hotel_origen
    FROM transferencias
    WHERE id_transferencia = ?
  `;

  db.query(verificarSql, [id_transferencia], (err, result) => {
    if (err) return res.status(500).json({ success: false, error: err });
    if (result.length === 0) return res.status(404).json({ success: false, mensaje: 'Transferencia no encontrada' });

    const { id_hotel_origen } = result[0];

    // Revisar hoteles asignados del usuario
  const hotelesSql = `
  SELECT uh.id_hotel
  FROM usuario_hoteles uh
  WHERE uh.id_user = ?
`;

    db.query(hotelesSql, [id_usuario], (err2, hoteles) => {
      if (err2) return res.status(500).json({ success: false, error: err2 });

      const hotelesAsignados = hoteles.map(h => h.id_hotel);

      if (!hotelesAsignados.includes(id_hotel_origen)) {
        return res.status(403).json({ success: false, mensaje: 'No tienes permiso para cancelar esta transferencia' });
      }

      // Si pasa la validación, eliminar
      const deleteSql = `DELETE FROM transferencias WHERE id_transferencia = ?`;
      db.query(deleteSql, [id_transferencia], (err3) => {
        if (err3) return res.status(500).json({ success: false, error: err3 });
      
       // 🚀 Notifica a todos
        io.emit('transferencia_actualizada', { id_transferencia, estado: 'CANCELADA' });
        
        res.json({ success: true, mensaje: '✅ Transferencia cancelada correctamente' });
      });
    });
  });
});


// Obtener todas las transferencias pendientes destinadas al hotel del usuario
router.get('/pendientes', (req, res) => {
  const sql = `
    SELECT 
      t.id_transferencia, t.id_equipo, t.id_hotel_origen, t.id_hotel_destino,
      e.numero_serie, m.nombre_modelo AS modelo,
      te.nombre_tipo AS tipo, ma.nombre_marca AS marca,
      ho.nombre_hotel AS hotel_origen, hd.nombre_hotel AS nombre_hotel_destino
    FROM transferencias t
    JOIN equipos e ON t.id_equipo = e.id_equipo
    JOIN modelo m ON e.id_modelo = m.id_modelo
    JOIN tipo_equipo te ON m.id_tipo_equipo = te.id_tipo_equipo
    JOIN marca ma ON m.id_marca = ma.id_marca
    JOIN hoteles ho ON t.id_hotel_origen = ho.id_hotel
    JOIN hoteles hd ON t.id_hotel_destino = hd.id_hotel
    WHERE t.estado = 'PENDIENTE'
  `;
  db.query(sql, (err, result) => {
    if (err) return res.status(500).json({ success: false, error: err });
    res.json({ success: true, transferencias: result });
  });
});


module.exports = router;
