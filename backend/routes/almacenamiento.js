const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener todos los almacenamientos
router.get('/', (req, res) => {
  const sql = 'SELECT id_almacenamiento, capacidad FROM almacenamiento_interno ORDER BY id_almacenamiento DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, almacenamientos: results });
  });
});

// Crear nuevo almacenamiento
router.post('/', (req, res) => {
  const io = req.io;
  const { capacidad } = req.body;

  if (!capacidad || capacidad.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Capacidad inválida' });
  }

  const verificarSql = 'SELECT * FROM almacenamiento_interno WHERE capacidad = ?';
  db.query(verificarSql, [capacidad], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe esa capacidad' });
    }

    const insertarSql = 'INSERT INTO almacenamiento_interno (capacidad) VALUES (?)';
    db.query(insertarSql, [capacidad], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
      
       io.emit('catalogo_almacenamientos', { tipo: 'CREADO', id: result.insertId });
      
      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar almacenamiento
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { capacidad } = req.body;

  if (!capacidad || capacidad.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Capacidad inválida' });
  }

  const verificarSql = 'SELECT * FROM almacenamiento_interno WHERE capacidad = ? AND id_almacenamiento != ?';
  db.query(verificarSql, [capacidad, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe esa capacidad' });
    }

    const updateSql = 'UPDATE almacenamiento_interno SET capacidad = ? WHERE id_almacenamiento = ?';
    db.query(updateSql, [capacidad, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });
      
      io.emit('catalogo_almacenamientos', { tipo: 'ACTUALIZADO', id });
      
      
      res.json({ success: true });
    });
  });
});

// Eliminar almacenamiento
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM almacenamiento_interno WHERE id_almacenamiento = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
    
     io.emit('catalogo_almacenamientos', { tipo: 'ELIMINADO', id });
    
    res.json({ success: true });
  });
});

module.exports = router;
