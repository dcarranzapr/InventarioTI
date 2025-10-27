const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener todos los sistemas operativos
router.get('/', (req, res) => {
  const sql = 'SELECT id_so, nombre_so FROM sistema_operativo ORDER BY id_so DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, sistemas: results });
  });
});

// Crear nuevo sistema operativo
router.post('/', (req, res) => {
  const io = req.io;
  const { nombre_so } = req.body;

  if (!nombre_so || nombre_so.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM sistema_operativo WHERE nombre_so = ?';
  db.query(verificarSql, [nombre_so], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe ese sistema operativo' });
    }

    const insertarSql = 'INSERT INTO sistema_operativo (nombre_so) VALUES (?)';
    db.query(insertarSql, [nombre_so], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
    
    io.emit('catalogo_so', { tipo: 'CREADO', id: result.insertId });

      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar sistema operativo
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { nombre_so } = req.body;

  if (!nombre_so || nombre_so.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM sistema_operativo WHERE nombre_so = ? AND id_so != ?';
  db.query(verificarSql, [nombre_so, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe ese sistema operativo' });
    }

    const updateSql = 'UPDATE sistema_operativo SET nombre_so = ? WHERE id_so = ?';
    db.query(updateSql, [nombre_so, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });
      
      io.emit('catalogo_so', { tipo: 'EDITADO', id });
      res.json({ success: true });
    });
  });
});

// Eliminar sistema operativo
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM sistema_operativo WHERE id_so = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
 
 io.emit('catalogo_so', { tipo: 'ELIMINADO', id_so: id });
    res.json({ success: true });
  });
});

module.exports = router;
