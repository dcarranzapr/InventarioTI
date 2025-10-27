// backend/routes/procesadores.js
const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener todos los procesadores
router.get('/', (req, res) => {
  const sql = 'SELECT id_procesador, nombre_procesador FROM procesador ORDER BY id_procesador DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, procesadores: results });
  });
});

// Crear nuevo procesador
router.post('/', (req, res) => {
  const io = req.io;
  const { nombre_procesador } = req.body;

  if (!nombre_procesador || nombre_procesador.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM procesador WHERE nombre_procesador = ?';
  db.query(verificarSql, [nombre_procesador], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe ese procesador' });
    }

    const insertarSql = 'INSERT INTO procesador (nombre_procesador) VALUES (?)';
    db.query(insertarSql, [nombre_procesador], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
     
          // 📡 Emite evento de creación
 io.emit('catalogo_procesadores', {tipo: 'CREADO',id_procesador: result.insertId, nombre_procesador  });

      res.json({ success: true, id: result.insertId });});
  });
});

// Actualizar procesador
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { nombre_procesador } = req.body;

  if (!nombre_procesador || nombre_procesador.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM procesador WHERE nombre_procesador = ? AND id_procesador != ?';
  db.query(verificarSql, [nombre_procesador, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe ese procesador' });
    }

    const updateSql = 'UPDATE procesador SET nombre_procesador = ? WHERE id_procesador = ?';
    db.query(updateSql, [nombre_procesador, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });
      
           // 📡 Emite evento de actualización
   io.emit('catalogo_procesadores', {tipo: 'ACTUALIZADO',id_procesador: id, nombre_procesador});
      
      res.json({ success: true });
    });
  });
});

// Eliminar procesador
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM procesador WHERE id_procesador = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
   
 io.emit('catalogo_procesadores', { tipo: 'ELIMINADO', id_procesador: id });


    res.json({ success: true });
  });
});

module.exports = router;
