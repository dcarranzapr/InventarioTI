const express = require('express');
const router = express.Router();
const db = require('../db');

// Obtener todos los hoteles (con estado)
router.get('/', (req, res) => {
  const sql = 'SELECT id_hotel, nombre_hotel, estado FROM hoteles ORDER BY nombre_hotel ASC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, hoteles: results });
  });
});

// Crear hotel con estado ALTA por defecto
router.post('/', (req, res) => {
  const io = req.io;
  const { nombre_hotel } = req.body;

  if (!nombre_hotel || nombre_hotel.trim() === '') {
    return res.status(400).json({ error: 'El nombre del hotel es obligatorio' });
  }

  const checkSql = 'SELECT * FROM hoteles WHERE nombre_hotel = ?';
  db.query(checkSql, [nombre_hotel.trim()], (err, rows) => {
    if (err) return res.status(500).json({ error: err.message });

    if (rows.length > 0) {
      return res.status(400).json({ error: 'El hotel ya existe' });
    }

    const insertSql = 'INSERT INTO hoteles (nombre_hotel, estado) VALUES (?, "ALTA")';
    db.query(insertSql, [nombre_hotel.trim()], (err, result) => {
      if (err) return res.status(500).json({ error: err.message });
            io.emit('catalogo_hoteles', { tipo: 'CREADO', id: result.insertId });
      res.json({ success: true, id: result.insertId });
    });
  });
});


// Cambiar estado del hotel (ALTA / BAJA)
router.put('/:id/estado', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { estado } = req.body;

  const sql = 'UPDATE hoteles SET estado = ? WHERE id_hotel = ?';
  db.query(sql, [estado, id], (err) => {
    if (err) return res.status(500).json({ error: err.message });
        io.emit('catalogo_hoteles', { tipo: 'ESTADO_CAMBIADO', id, estado });
    res.json({ success: true });
  });
});

// Eliminar un hotel por ID
router.delete('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;

  db.query('DELETE FROM hoteles WHERE id_hotel = ?', [id], (err, result) => {
    if (err) return res.status(500).json({ error: err.message });
    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'Hotel no encontrado' });
    }

    io.emit('catalogo_hoteles', { tipo: 'ELIMINADO', id });
    
    res.json({ success: true });
  });
});

// Obtener solo hoteles en estado ALTA
router.get('/activos', (req, res) => {
  const sql = 'SELECT id_hotel, nombre_hotel FROM hoteles WHERE estado = "ALTA" ORDER BY nombre_hotel ASC';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ success: true, hoteles: results });
  });
});

// Obtener hotel por ID
router.get('/:id', (req, res) => {
  const { id } = req.params;

  db.query('SELECT * FROM hoteles WHERE id_hotel = ?', [id], (err, rows) => {
    if (err) return res.status(500).json({ error: err.message });
    if (rows.length === 0) return res.status(404).json({ error: 'No encontrado' });
    res.json({ success: true, hotel: rows[0] });
  });
});

// Actualizar nombre de hotel por ID
router.put('/:id', (req, res) => {
  const io = req.io;
  const { id } = req.params;
  const { nombre_hotel } = req.body;

  if (!nombre_hotel || nombre_hotel.trim() === '') {
    return res.status(400).json({ error: 'El nombre del hotel es obligatorio' });
  }

  const updateSql = 'UPDATE hoteles SET nombre_hotel = ? WHERE id_hotel = ?';
  db.query(updateSql, [nombre_hotel.trim(), id], (err, result) => {
    if (err) return res.status(500).json({ error: err.message });
    
        io.emit('catalogo_hoteles', { tipo: 'ACTUALIZADO', id });

    res.json({ success: true });
  });
});


module.exports = router;
