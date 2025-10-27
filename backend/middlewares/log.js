const db = require('../db');

function registrarLogEquipo({
  id_equipo,
  accion,
  id_usuario,
  descripcion = '',
  datos_anteriores = null,
  datos_nuevos = null
}) {
  const sql = `
    INSERT INTO log_equipos (id_equipo, accion, id_usuario, descripcion, datos_anteriores, datos_nuevos)
    VALUES (?, ?, ?, ?, ?, ?)
  `;

  db.query(
    sql,
    [
      id_equipo,
      accion,
      id_usuario,
      descripcion,
      datos_anteriores ? JSON.stringify(datos_anteriores) : null,
      datos_nuevos ? JSON.stringify(datos_nuevos) : null
    ],
    (err) => {
      if (err) console.error('❌ Error al registrar log del equipo:', err);
    }
  );
}

module.exports = { registrarLogEquipo };
