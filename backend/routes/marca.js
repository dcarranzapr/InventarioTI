// backend/routes/marca.js
const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener marcas
router.get('/', (req, res) => {
  const sql = 'SELECT id_marca, nombre_marca FROM marca ORDER BY id_marca DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, marcas: results });
  });
});

// Crear marca
router.post('/', (req, res) => {
  const io = req.io;
  const { nombre_marca } = req.body;
  if (!nombre_marca || nombre_marca.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificar = 'SELECT * FROM marca WHERE nombre_marca = ?';
  db.query(verificar, [nombre_marca], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    if (results.length > 0) {
     
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe esa marca' });
    }

    const insert = 'INSERT INTO marca (nombre_marca) VALUES (?)';
    db.query(insert, [nombre_marca], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
      
      io.emit('catalogo_marcas', { tipo: 'CREADA', id: result.insertId }); // 🔔

      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar marca
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { nombre_marca } = req.body;

  if (!nombre_marca || nombre_marca.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificar = 'SELECT * FROM marca WHERE nombre_marca = ? AND id_marca != ?';
  db.query(verificar, [nombre_marca, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe esa marca' });
    }

    const update = 'UPDATE marca SET nombre_marca = ? WHERE id_marca = ?';
    db.query(update, [nombre_marca, id], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
    
       io.emit('catalogo_marcas', { tipo: 'EDITADA', id: id }); // 🔔

      res.json({ success: true });
    });
  });
});

// Eliminar marca
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM marca WHERE id_marca = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
    
   io.emit('catalogo_marcas', { tipo: 'ELIMINADA', id_marca: id }); // 🔔

    res.json({ success: true });
  });
});

module.exports = router;
