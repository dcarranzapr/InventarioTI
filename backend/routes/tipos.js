// routes/tipos.js
const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener todos los tipos de equipo
router.get('/', (req, res) => {
  const sql = 'SELECT id_tipo_equipo, nombre_tipo FROM tipo_equipo ORDER BY id_tipo_equipo DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, tipos: results });
  });
});

// Crear nuevo tipo de equipo
router.post('/', (req, res) => {
  const io = req.io;
  const { nombre_tipo } = req.body;

  if (!nombre_tipo || nombre_tipo.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM tipo_equipo WHERE nombre_tipo = ?';
  db.query(verificarSql, [nombre_tipo], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe ese tipo' });
    }

    const insertarSql = 'INSERT INTO tipo_equipo (nombre_tipo) VALUES (?)';
    db.query(insertarSql, [nombre_tipo], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
     
      io.emit('catalogo_tipos', { tipo: 'CREADO', id: result.insertId }); // 🚀 emite cambio

      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar tipo de equipo
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { nombre_tipo } = req.body;

  if (!nombre_tipo || nombre_tipo.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM tipo_equipo WHERE nombre_tipo = ? AND id_tipo_equipo != ?';
  db.query(verificarSql, [nombre_tipo, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe ese tipo' });
    }

    const updateSql = 'UPDATE tipo_equipo SET nombre_tipo = ? WHERE id_tipo_equipo = ?';
    db.query(updateSql, [nombre_tipo, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });
     
       io.emit('catalogo_tipos', { tipo: 'ACTUALIZADO', id }); // 🚀 emite cambio

      res.json({ success: true });
    });
  });
});

// Eliminar tipo de equipo
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM tipo_equipo WHERE id_tipo_equipo = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
 
  io.emit('catalogo_tipos', { tipo: 'ELIMINADO', id_tipo: id });

    res.json({ success: true });
  });
});

module.exports = router;
