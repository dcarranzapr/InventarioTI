// backend/routes/RAMserver.js
const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener todas las memorias RAM
router.get('/', (req, res) => {
  const sql = 'SELECT id_memoria_ram, capacidad FROM memoria_ram ORDER BY id_memoria_ram DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, rams: results });
  });
});

// Crear nueva memoria RAM
router.post('/', (req, res) => {
  const io = req.io;
  const { capacidad } = req.body;

  if (!capacidad || capacidad.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Capacidad inválida' });
  }

  const verificarSql = 'SELECT * FROM memoria_ram WHERE capacidad = ?';
  db.query(verificarSql, [capacidad], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe esa capacidad' });
    }

    const insertarSql = 'INSERT INTO memoria_ram (capacidad) VALUES (?)';
    db.query(insertarSql, [capacidad], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
      
      io.emit('catalogo_ram', { tipo: 'CREADO', id_ram: result.insertId, capacidad });
      

      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar memoria RAM
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { capacidad } = req.body;

  if (!capacidad || capacidad.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Capacidad inválida' });
  }

  const verificarSql = 'SELECT * FROM memoria_ram WHERE capacidad = ? AND id_memoria_ram != ?';
  db.query(verificarSql, [capacidad, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe esa capacidad' });
    }

    const updateSql = 'UPDATE memoria_ram SET capacidad = ? WHERE id_memoria_ram = ?';
    db.query(updateSql, [capacidad, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });
      
      io.emit('catalogo_ram', { tipo: 'ACTUALIZADO', id_ram: id, capacidad });
      
      res.json({ success: true });
    });
  });
});

// Eliminar memoria RAM
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM memoria_ram WHERE id_memoria_ram = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
    
        io.emit('catalogo_ram', { tipo: 'ELIMINADO', id_ram: id });
    

    res.json({ success: true });
  });
});

module.exports = router;
