// backend/routes/plataforma.js
const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener plataformas
router.get('/', (req, res) => {
  const sql = 'SELECT id_plataforma, nombre_plataforma FROM plataforma ORDER BY id_plataforma DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    const plataformas = results.map(p => ({
      id: p.id_plataforma,
      nombre: p.nombre_plataforma
    }));

    res.json({ success: true, plataformas });
  });
});

// Crear plataforma
router.post('/', (req, res) => {
  const io = req.io;
  const { nombre_plataforma } = req.body;

  if (!nombre_plataforma || nombre_plataforma.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM plataforma WHERE nombre_plataforma = ?';
  db.query(verificarSql, [nombre_plataforma], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Esa plataforma ya existe' });
    }

    const insertarSql = 'INSERT INTO plataforma (nombre_plataforma) VALUES (?)';
    db.query(insertarSql, [nombre_plataforma], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
     
      io.emit('catalogo_plataformas', { tipo: 'CREADA', id: result.insertId });

      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar plataforma
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { nombre_plataforma } = req.body;

  if (!nombre_plataforma || nombre_plataforma.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM plataforma WHERE nombre_plataforma = ? AND id_plataforma != ?';
  db.query(verificarSql, [nombre_plataforma, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Esa plataforma ya existe' });
    }

    const updateSql = 'UPDATE plataforma SET nombre_plataforma = ? WHERE id_plataforma = ?';
    db.query(updateSql, [nombre_plataforma, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });
      
      io.emit('catalogo_plataformas', { tipo: 'EDITADA', id });
      res.json({ success: true });
    });
  });
});

// Eliminar plataforma
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM plataforma WHERE id_plataforma = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
  
    io.emit('catalogo_plataformas', { tipo: 'ELIMINADA', id });

    res.json({ success: true });
  });
});

module.exports = router;
