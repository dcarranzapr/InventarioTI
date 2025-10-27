const express = require('express');
const bcrypt = require('bcrypt'); // ✅ SOLO AGREGADO
const router = express.Router();
const db = require('../db');

// Obtener roles para el select dinámico
router.get('/roles', (req, res) => {
  const query = 'SELECT id_rol, nombre_rol FROM roles';
  db.query(query, (err, results) => {
    if (err) return res.status(500).json({ error: 'Error al obtener roles' });
    res.json(results);
  });
});

// Registrar nuevo ingeniero
router.post('/registrar', async (req, res) => {
  const io = req.io; 
  const { username, nombre, password, rol_id, hoteles } = req.body;

  if (!username || !nombre || !password || !rol_id || !Array.isArray(hoteles)) {
    return res.status(400).json({ error: 'Todos los campos son obligatorios, incluyendo hoteles' });
  }

  const verificarSql = 'SELECT * FROM usuarios WHERE username = ?';
  db.query(verificarSql, [username], async (err, result) => {
    if (err) return res.status(500).json({ error: 'Error al verificar usuario' });

    if (result.length > 0) {
      return res.status(409).json({ error: 'Este nombre de usuario ya está registrado' });
    }

    const hashedPassword = await bcrypt.hash(password, 10); // ✅ ENCRIPTAR

    const insertSql = 'INSERT INTO usuarios (username, nombre, password, rol_id, estado) VALUES (?, ?, ?, ?, "ALTA")';
    db.query(insertSql, [username, nombre, hashedPassword, rol_id], (err2, result2) => {
      if (err2) return res.status(500).json({ error: 'Error al registrar el usuario' });

      const id_user = result2.insertId;

      if (hoteles.length > 0) {
        const values = hoteles.map(id_hotel => [id_user, id_hotel]);
        const insertHotelesSql = 'INSERT INTO usuario_hoteles (id_user, id_hotel) VALUES ?';

        db.query(insertHotelesSql, [values], (err3) => {
          if (err3) return res.status(500).json({ error: 'Error al asignar hoteles' });

          io.emit('catalogo_usuarios', { tipo: 'CREADO', id_user });
          io.emit('usuario_hoteles_actualizados', { id_usuario: id_user });

          
          return res.json({ success: true, message: 'Usuario y hoteles registrados' });
        });
      } else {
        io.emit('catalogo_usuarios', { tipo: 'CREADO', id_user });
        io.emit('usuario_hoteles_actualizados', { id_usuario: id_user });
        
        return res.json({ success: true, message: 'Usuario registrado sin hoteles' });
      }
    });
  });
});


// Login de usuario
router.post('/login', (req, res) => {
  const { username, password } = req.body;

  if (!username || !password) {
    return res.status(400).json({ error: 'Usuario y contraseña son obligatorios' });
  }

  const sql = `
    SELECT * FROM usuarios 
    WHERE BINARY username = ? 
      AND estado = "ALTA"
  `;

  db.query(sql, [username], async (err, results) => {
    if (err) {
      console.error('❌ Error al verificar usuario:', err.sqlMessage || err);
      return res.status(500).json({ error: 'Error en el servidor' });
    }

    if (results.length === 0) {
      return res.status(401).json({ error: 'Credenciales incorrectas o usuario inactivo' });
    }

    const user = results[0];

    const match = await bcrypt.compare(password, user.password); // ✅ COMPARA HASH

    if (!match) {
      return res.status(401).json({ error: 'Contraseña incorrecta' });
    }

    // Obtener hoteles asignados
    db.query('SELECT id_hotel FROM usuario_hoteles WHERE id_user = ?', [user.id_user], (err2, hoteles) => {
      if (err2) return res.status(500).json({ error: 'Error al obtener hoteles asignados' });

      res.json({
        success: true,
        message: 'Login exitoso',
        usuario: user.username,
        nombre: user.nombre,
        rol: user.rol_id,
        id_user: user.id_user,
        hotelesAsignados: hoteles.map(h => h.id_hotel)
      });
    });
  });
});

// Cambiar contraseña
router.put('/cambiar-contrasena', (req, res) => {
  const { username, actual, nueva } = req.body;

  if (!username || !actual || !nueva) {
    return res.status(400).json({ error: 'Faltan datos obligatorios' });
  }

  const sqlBuscar = `SELECT id_user, password FROM usuarios WHERE BINARY username = ?`;
  db.query(sqlBuscar, [username.trim()], async (err, result) => {
    if (err) return res.status(500).json({ error: 'Error al verificar contraseña' });
    if (result.length === 0) return res.status(401).json({ error: 'Usuario no encontrado' });

    const match = await bcrypt.compare(actual.trim(), result[0].password); // ✅ COMPARA HASH

    if (!match) return res.status(401).json({ error: 'Contraseña actual incorrecta' });

    const hashedNueva = await bcrypt.hash(nueva.trim(), 10); // ✅ NUEVO HASH

    const sqlActualizar = 'UPDATE usuarios SET password = ? WHERE id_user = ?';
    db.query(sqlActualizar, [hashedNueva, result[0].id_user], (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al actualizar la contraseña' });
      res.json({ success: true, message: 'Contraseña actualizada correctamente' });
    });
  });
});

// Obtener usuarios
router.get('/', (req, res) => {
  const sql = `
    SELECT u.id_user, u.username, u.nombre, u.rol_id, r.nombre_rol, u.estado
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id_rol
    ORDER BY u.id_user ASC
  `;
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: 'Error al obtener usuarios' });
    res.json({ success: true, usuarios: results });
  });
});

// Obtener usuario por ID
router.get('/:id', (req, res) => {
  const id = req.params.id;
  const sql = `SELECT id_user, username, nombre, password, rol_id FROM usuarios WHERE id_user = ?`;
  db.query(sql, [id], (err, results) => {
    if (err || results.length === 0) return res.status(500).json({ error: 'No se pudo obtener el usuario' });
    res.json({ success: true, usuario: results[0] });
  });
});

// Obtener hoteles asignados a un usuario
router.get('/:id/hoteles', (req, res) => {
  const id = req.params.id;
  const sql = `SELECT id_hotel FROM usuario_hoteles WHERE id_user = ?`;
  db.query(sql, [id], (err, results) => {
    if (err) return res.status(500).json({ error: 'Error al obtener hoteles asignados' });
    const ids = results.map(r => r.id_hotel);
    res.json({ success: true, hotelesAsignados: ids });
  });
});

// Actualizar usuario y hoteles asignados
router.put('/:id', async (req, res) => {
  const io = req.io;
  const id = req.params.id;
  const { username, nombre, password, rol_id, hotelesAsignados } = req.body;

  if (!username || !nombre || !rol_id || !Array.isArray(hotelesAsignados)) {
    return res.status(400).json({ error: 'Todos los campos son obligatorios, incluyendo hoteles' });
  }

  let sql = `UPDATE usuarios SET username = ?, nombre = ?, rol_id = ?`;
  const params = [username, nombre, rol_id];

  if (password && password.trim() !== '') {
    const hashedPassword = await bcrypt.hash(password.trim(), 10);
    sql += `, password = ?`;
    params.push(hashedPassword);
  }

  sql += ` WHERE id_user = ?`;
  params.push(id);

  db.query(sql, params, (err) => {
    if (err) return res.status(500).json({ error: 'Error al actualizar el usuario' });

    const deleteSql = 'DELETE FROM usuario_hoteles WHERE id_user = ?';
    db.query(deleteSql, [id], (err2) => {
      if (err2) return res.status(500).json({ error: 'Error al limpiar hoteles previos' });

      if (hotelesAsignados.length === 0) {
        return res.json({ success: true, message: 'Usuario actualizado sin hoteles' });
      }

      const values = hotelesAsignados.map(id_hotel => [id, id_hotel]);
      const insertSql = 'INSERT INTO usuario_hoteles (id_user, id_hotel) VALUES ?';

      db.query(insertSql, [values], (err3) => {
        if (err3) return res.status(500).json({ error: 'Error al asignar nuevos hoteles' });

        io.emit('catalogo_usuarios', { tipo: 'ACTUALIZADO', id });
        io.emit('usuario_hoteles_actualizados', { id_usuario: id });

        return res.json({ success: true, message: 'Usuario y hoteles actualizados' });
      });
    });
  });
});

// Eliminar usuario
router.delete('/eliminar/:id', (req, res) => {
  const io = req.io;
  const id = req.params.id;
  const sql = 'DELETE FROM usuarios WHERE id_user = ?';

  db.query(sql, [id], (err) => {
    if (err) return res.status(500).json({ error: 'Error al eliminar usuario' });
    io.emit('catalogo_usuarios', { tipo: 'ELIMINADO', id });
    res.json({ success: true, message: 'Usuario eliminado correctamente' });
  });
});

// Cambiar estado del usuario
router.put('/:id/estado', (req, res) => {
  const io = req.io;
  const id = req.params.id;
  const { estado } = req.body;

  if (!estado || (estado !== 'ALTA' && estado !== 'BAJA')) {
    return res.status(400).json({ error: 'Estado inválido' });
  }

  const sql = 'UPDATE usuarios SET estado = ? WHERE id_user = ?';
  db.query(sql, [estado, id], (err) => {
    if (err) return res.status(500).json({ error: 'Error al actualizar estado' });
    
    io.emit('catalogo_usuarios', { tipo: 'ACTUALIZADO', id });
  
    res.json({ success: true, message: 'Estado actualizado correctamente' });
  });
});

module.exports = router;
