const express = require('express');
const router = express.Router();
const db = require('../db');

// 📌 Crear nueva transferencia de RESGUARDO
router.post('/', (req, res) => {
  const io = req.io; 
  const { id_resguardo, id_hotel_destino, creado_por } = req.body;

  // Obtener hotel origen real del resguardo
  const sqlOrigen = `
    SELECT id_hotel_origen FROM resguardos WHERE id_resguardo = ?
  `;

  db.query(sqlOrigen, [id_resguardo], (err, rows) => {
    if (err) return res.status(500).json({ success: false, error: err });
    if (!rows.length) return res.status(404).json({ success: false, mensaje: 'Resguardo no encontrado.' });

    const id_hotel_origen = rows[0].id_hotel_origen;

    const sqlInsert = `
      INSERT INTO transferencias_resguardos
      (id_resguardo, id_hotel_origen, id_hotel_destino, estado, fecha_solicitud, creado_por)
      VALUES (?, ?, ?, 'PENDIENTE', NOW(), ?)
    `;

    db.query(sqlInsert, [id_resguardo, id_hotel_origen, id_hotel_destino, creado_por], (err2, result) => {
      if (err2) return res.status(500).json({ success: false, error: err2 });


    io.emit('transferencia_resguardo_creada', { id_resguardo });
      res.json({
        success: true,
        id_transferencia: result.insertId
      });
    });
  });
});

// 📌 Obtener transferencias PENDIENTES (solo las que el usuario puede ver)
router.get('/pendientes/creadas/:id_usuario', (req, res) => {
  const id_usuario = req.params.id_usuario;

  const hotelesSql = `SELECT id_hotel FROM usuario_hoteles WHERE id_user = ?`;

  db.query(hotelesSql, [id_usuario], (err, hoteles) => {
    if (err) return res.status(500).json({ success: false, error: err });
    if (!hoteles.length) return res.json({ success: true, pendientes: {} });

    const hotelesAsignados = hoteles.map(h => h.id_hotel);

    const sql = `
      SELECT 
        tr.id_resguardo,
        tr.id_transferencia,
        tr.creado_por,
        tr.id_hotel_origen,
        ho.nombre_hotel AS nombre_hotel_origen,
        tr.id_hotel_destino,
        hd.nombre_hotel AS nombre_hotel_destino
      FROM transferencias_resguardos tr
      JOIN hoteles hd ON tr.id_hotel_destino = hd.id_hotel
      JOIN hoteles ho ON tr.id_hotel_origen = ho.id_hotel
      WHERE tr.estado = 'PENDIENTE'
    `;

    db.query(sql, (err2, result) => {
      if (err2) return res.status(500).json({ success: false, error: err2 });

      const pendientes = {};
      result.forEach(row => {
        const tienePermiso = hotelesAsignados.includes(row.id_hotel_origen);
        pendientes[row.id_resguardo] = {
          id_transferencia: row.id_transferencia,
          creado_por: row.creado_por,
          id_hotel_origen: row.id_hotel_origen,
          nombre_hotel_origen: row.nombre_hotel_origen,
          id_hotel_destino: row.id_hotel_destino,
          nombre_hotel_destino: row.nombre_hotel_destino,
          puedeCancelar: tienePermiso
        };
      });

      res.json({ success: true, pendientes });
    });
  });
});

// 📌 Aceptar transferencia de RESGUARDO → Actualiza TODOS los equipos y el resguardo
router.put('/aceptar/:id', (req, res) => {
  const io = req.io;
  const id_transferencia = req.params.id;

  const sqlDetalle = `
    SELECT t.id_resguardo, t.id_hotel_destino
    FROM transferencias_resguardos t
    WHERE t.id_transferencia = ?
  `;

  db.query(sqlDetalle, [id_transferencia], (err, resultado) => {
    if (err) return res.status(500).json({ success: false, error: err });
    if (!resultado.length) return res.status(404).json({ success: false, mensaje: 'Transferencia no encontrada' });

    const { id_resguardo, id_hotel_destino } = resultado[0];

    const sqlUpdateEquipos = `
      UPDATE inventario i
      JOIN resguardo_equipos re ON i.id_equipo = re.id_equipo
      SET i.id_hotel = ?
      WHERE re.id_resguardo = ?
    `;
    db.query(sqlUpdateEquipos, [id_hotel_destino, id_resguardo], (err2) => {
      if (err2) return res.status(500).json({ success: false, error: err2 });

      const sqlUpdateResguardo = `
        UPDATE resguardos
        SET id_hotel_origen = ?
        WHERE id_resguardo = ?
      `;
      db.query(sqlUpdateResguardo, [id_hotel_destino, id_resguardo], (err3) => {
        if (err3) return res.status(500).json({ success: false, error: err3 });

        const sqlAceptar = `
          UPDATE transferencias_resguardos
          SET estado = 'ACEPTADA', fecha_respuesta = NOW()
          WHERE id_transferencia = ?
        `;
        db.query(sqlAceptar, [id_transferencia], (err4) => {
          if (err4) return res.status(500).json({ success: false, error: err4 });
         
           // ✅ Notificación en tiempo real
      io.emit('transferencia_resguardo_aceptada', { id_transferencia, estado: 'ACEPTADA' });
      io.emit('inventario_actualizado'); // Opcional si se desea refrescar en control

          res.json({ success: true, mensaje: '✅ Equipos y resguardo actualizados, transferencia aceptada.' });
        });
      });
    });
  });
});


// 📌 Cancelar transferencia de RESGUARDO (requiere permiso)
router.put('/cancelar/:id', (req, res) => {
  const io = req.io;
  const id_transferencia = Number(req.params.id);
  const id_usuario = Number(req.body.id_usuario);

  if (!id_transferencia || !id_usuario) {
    return res.status(400).json({ success: false, mensaje: 'ID inválido' });
  }

  const verificarSql = `SELECT id_hotel_origen FROM transferencias_resguardos WHERE id_transferencia = ?`;

  db.query(verificarSql, [id_transferencia], (err, result) => {
    if (err) return res.status(500).json({ success: false, error: err });
    if (result.length === 0) return res.status(404).json({ success: false, mensaje: 'Transferencia no encontrada.' });

    const id_resguardo = result[0].id_resguardo;
    const id_hotel_origen = result[0].id_hotel_origen;

    const hotelesSql = `SELECT id_hotel FROM usuario_hoteles WHERE id_user = ?`;

    db.query(hotelesSql, [id_usuario], (err2, hoteles) => {
      if (err2) return res.status(500).json({ success: false, error: err2 });

      const hotelesAsignados = hoteles.map(h => h.id_hotel);

      if (!hotelesAsignados.includes(id_hotel_origen)) {
        return res.status(403).json({ success: false, mensaje: '🚫 No tienes permiso para cancelar esta transferencia.' });
      }

      const sqlDelete = `
        DELETE FROM transferencias_resguardos
        WHERE id_transferencia = ?
      `;
      db.query(sqlDelete, [id_transferencia], (err3) => {
        if (err3) return res.status(500).json({ success: false, error: err3 });
       
        io.emit('transferencia_resguardo_cancelada', { id_resguardo });
        res.json({ success: true, mensaje: '✅ Transferencia de resguardo eliminada correctamente.' });
      });
    });
  });
});

// 📌 Opcional: Listar todas las transferencias pendientes (resumen)
router.get('/pendientes', (req, res) => {
  const sql = `
    SELECT 
      t.id_transferencia, t.id_resguardo, t.id_hotel_origen, t.id_hotel_destino,
      r.nombre_colaborador, r.nombre_equipo,
      ho.nombre_hotel AS hotel_origen, hd.nombre_hotel AS hotel_destino
    FROM transferencias_resguardos t
    JOIN resguardos r ON t.id_resguardo = r.id_resguardo
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
