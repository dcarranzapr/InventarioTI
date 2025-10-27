const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener todos los tipos de disco duro
router.get('/', (req, res) => {
  const sql = 'SELECT id_disco, tipo_disco FROM tipo_disco_duro ORDER BY id_disco DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, discos: results });
  });
});

// Crear nuevo tipo de disco
router.post('/', (req, res) => {
  const io = req.io;
  const { tipo_disco } = req.body;

  if (!tipo_disco || tipo_disco.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Tipo de disco inválido' });
  }

  const verificarSql = 'SELECT * FROM tipo_disco_duro WHERE tipo_disco = ?';
  db.query(verificarSql, [tipo_disco], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe ese tipo de disco' });
    }

    const insertarSql = 'INSERT INTO tipo_disco_duro (tipo_disco) VALUES (?)';
    db.query(insertarSql, [tipo_disco], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
     
       io.emit('catalogo_discos', { tipo: 'CREADO', id_disco: result.insertId, tipo_disco });
     
     
      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar tipo de disco
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { tipo_disco } = req.body;

  if (!tipo_disco || tipo_disco.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Tipo de disco inválido' });
  }

  const verificarSql = 'SELECT * FROM tipo_disco_duro WHERE tipo_disco = ? AND id_disco != ?';
  db.query(verificarSql, [tipo_disco, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ Ya existe ese tipo de disco' });
    }

    const updateSql = 'UPDATE tipo_disco_duro SET tipo_disco = ? WHERE id_disco = ?';
    db.query(updateSql, [tipo_disco, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });
      
       io.emit('catalogo_discos', { tipo: 'ACTUALIZADO', id_disco: id, tipo_disco });
      
      res.json({ success: true });
    });
  });
});

// Eliminar tipo de disco
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM tipo_disco_duro WHERE id_disco = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
   
   
     io.emit('catalogo_discos', { tipo: 'ELIMINADO', id_disco: id });
   
    res.json({ success: true });
  });
});

module.exports = router;
