const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener todos los modelos (con tipo y marca)
router.get('/', (req, res) => {
  const sql = `
    SELECT 
      m.id_modelo, m.nombre_modelo, m.estado, 
      t.nombre_tipo AS tipo, 
      ma.nombre_marca AS marca 
    FROM modelo m
    JOIN tipo_equipo t ON m.id_tipo_equipo = t.id_tipo_equipo
    JOIN marca ma ON m.id_marca = ma.id_marca
    ORDER BY m.id_modelo DESC
  `;

  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    const modelos = results.map((row) => ({
      id: row.id_modelo,
      nombre: row.nombre_modelo,
      tipo: row.tipo,
      marca: row.marca,
      activo: row.estado === 'ALTA'
    }));

    res.json({ success: true, modelos });
  });
});

// Obtener modelos en ALTA filtrados por tipo y marca
router.get('/activos', (req, res) => {
  const { tipo, marca } = req.query;

  if (!tipo || !marca) {
    return res.status(400).json({ success: false, mensaje: 'Faltan parámetros de tipo o marca' });
  }

  const sql = `
    SELECT m.id_modelo, m.nombre_modelo 
    FROM modelo m
    WHERE m.estado = 'ALTA' AND m.id_tipo_equipo = ? AND m.id_marca = ?
    ORDER BY m.nombre_modelo ASC
  `;

  db.query(sql, [tipo, marca], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, modelos: results });
  });
});

// Crear modelo (ahora con tipo y marca)
router.post('/', (req, res) => {
  const io = req.io;
  const { nombre_modelo, id_tipo_equipo, id_marca } = req.body;

  if (!nombre_modelo || !id_tipo_equipo || !id_marca) {
    return res.status(400).json({ success: false, mensaje: '❌ Faltan datos requeridos' });
  }

  const verificarSql = 'SELECT * FROM modelo WHERE nombre_modelo = ?';
  db.query(verificarSql, [nombre_modelo], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ El modelo ya existe' });
    }

    const insertarSql = `
      INSERT INTO modelo (nombre_modelo, estado, id_tipo_equipo, id_marca)
      VALUES (?, "ALTA", ?, ?)
    `;
    db.query(insertarSql, [nombre_modelo, id_tipo_equipo, id_marca], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
      
        io.emit('catalogo_modelos', { tipo: 'CREADO', id: result.insertId });

      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar nombre de modelo
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { nombre_modelo } = req.body;

  if (!nombre_modelo || nombre_modelo.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre de modelo inválido' });
  }

  const verificarSql = 'SELECT * FROM modelo WHERE nombre_modelo = ? AND id_modelo != ?';
  db.query(verificarSql, [nombre_modelo, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ El modelo ya existe' });
    }

    const actualizarSql = 'UPDATE modelo SET nombre_modelo = ? WHERE id_modelo = ?';
    db.query(actualizarSql, [nombre_modelo, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });

 io.emit('catalogo_modelos', { tipo: 'ACTUALIZADO', id });

      res.json({ success: true });
    });
  });
});

// Cambiar estado (ALTA/BAJA)
router.put('/:id/estado', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { estado } = req.body;

  const sql = 'UPDATE modelo SET estado = ? WHERE id_modelo = ?';
  db.query(sql, [estado, id], (err) => {
    if (err) return res.status(500).json({ error: err.message });

io.emit('catalogo_modelos', { tipo: 'ACTUALIZADO', id });

    res.json({ success: true });
  });
});

// Eliminar modelo
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM modelo WHERE id_modelo = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });

     io.emit('catalogo_modelos', { tipo: 'ELIMINADO', id_modelo: id });

    res.json({ success: true });
  });
});

module.exports = router;
