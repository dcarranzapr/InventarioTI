const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener proveedores
router.get('/', (req, res) => {
  const sql = 'SELECT id_proveedor, nombre_proveedor FROM proveedor ORDER BY id_proveedor DESC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    const proveedores = results.map(p => ({
      id: p.id_proveedor,
      nombre: p.nombre_proveedor
    }));

    res.json({ success: true, proveedores });
  });
});

// Crear proveedor
router.post('/', (req, res) => {
  const io = req.io;
  const { nombre_proveedor } = req.body;

  if (!nombre_proveedor || nombre_proveedor.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM proveedor WHERE nombre_proveedor = ?';
  db.query(verificarSql, [nombre_proveedor], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ El proveedor ya existe' });
    }

    const insertarSql = 'INSERT INTO proveedor (nombre_proveedor) VALUES (?)';
    db.query(insertarSql, [nombre_proveedor], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
      
  io.emit('catalogo_proveedores', { tipo: 'CREADO', id_proveedor: result.insertId, nombre_proveedor });
      
      res.json({ success: true, id: result.insertId });
    });
  });
});

// Actualizar proveedor
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { nombre_proveedor } = req.body;

  if (!nombre_proveedor || nombre_proveedor.trim() === '') {
    return res.status(400).json({ success: false, mensaje: '❌ Nombre inválido' });
  }

  const verificarSql = 'SELECT * FROM proveedor WHERE nombre_proveedor = ? AND id_proveedor != ?';
  db.query(verificarSql, [nombre_proveedor, id], (err, results) => {
    if (err) return res.status(500).json({ error: err.message });

    if (results.length > 0) {
      return res.status(400).json({ success: false, mensaje: '❌ El proveedor ya existe' });
    }

    const updateSql = 'UPDATE proveedor SET nombre_proveedor = ? WHERE id_proveedor = ?';
    db.query(updateSql, [nombre_proveedor, id], (err) => {
      if (err) return res.status(500).json({ error: err.message });
     
       io.emit('catalogo_proveedores', { tipo: 'ACTUALIZADO', id_proveedor: id, nombre_proveedor });
     
      res.json({ success: true });
    });
  });
});

// Eliminar proveedor
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const sql = 'DELETE FROM proveedor WHERE id_proveedor = ?';
  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
   
   io.emit('catalogo_proveedores', { tipo: 'ELIMINADO', id_proveedor: id });
   
    res.json({ success: true });
  });
});

module.exports = router;
