const express = require('express');
const router = express.Router();
const db = require('../db');
const { registrarLogEquipo } = require('../middlewares/log'); // Ajusta ruta si es necesario


router.post('/api/insertar-equipo', (req, res) => {
 const io = req.io;
  const {
    tipo, marca, modelo, numeroSerie,
    hotel, sistemaOperativo, ram, procesador,
    discoDuro, almacenamiento,
    proveedor, factura, fechaCompra,
    masDeTresAnios
  } = req.body;

  // 🔎 Validar si el número de serie ya existe
  const checkSerieQuery = 'SELECT 1 FROM equipos WHERE numero_serie = ? LIMIT 1';
  db.query(checkSerieQuery, [numeroSerie], (err, results) => {
    if (err) return res.status(500).json({ error: 'Error al verificar el número de serie' });

    if (results.length > 0) {
      // Ya existe, cortar el flujo aquí
      return res.status(400).json({ error: '❌ Ya existe un equipo con ese número de serie' });
    }

    // 🚀 Si no existe, continuar con la transacción
    db.beginTransaction(err => {
      if (err) return res.status(500).json({ error: 'Error al iniciar transacción' });

      const insertEquipo = `
        INSERT INTO equipos (
          numero_serie, id_sistema_operativo, id_procesador, id_tipo_disco_duro,
          id_almacenamiento, id_memoria_ram, id_tipo_equipo,
          id_marca, id_modelo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
      `;

      const valuesEquipo = [
        numeroSerie,
        sistemaOperativo || null,
        procesador || null,
        discoDuro || null,
        almacenamiento || null,
        ram || null,
        tipo,
        marca,
        modelo
      ];

      db.query(insertEquipo, valuesEquipo, (err, result) => {
        if (err) return db.rollback(() => res.status(500).json({ error: 'Error al insertar equipo' }));

        const idEquipo = result.insertId;

        const insertAlmacen = `
          INSERT INTO almacen (id_equipo, numero_factura, id_proveedor, fecha_compra)
          VALUES (?, ?, ?, ?)
        `;
        const valuesAlmacen = [
          idEquipo,
          masDeTresAnios ? null : factura,
          masDeTresAnios ? null : proveedor,
          masDeTresAnios ? null : fechaCompra
        ];

        db.query(insertAlmacen, valuesAlmacen, (err) => {
          if (err) return db.rollback(() => res.status(500).json({ error: 'Error al insertar en almacen' }));

          const insertInventario = `
            INSERT INTO inventario (id_equipo, id_hotel, id_estado)
            VALUES (?, ?, 1)
          `;
          db.query(insertInventario, [idEquipo, hotel], (err) => {
            if (err) return db.rollback(() => res.status(500).json({ error: 'Error al insertar en inventario' }));

            db.commit(err => {
              if (err) return db.rollback(() => res.status(500).json({ error: 'Error al confirmar transacción' }));
            

              registrarLogEquipo({
  id_equipo: idEquipo,
  accion: 'ALTA',
  id_usuario: parseInt(req.headers['user-id'], 10),
  descripcion: 'Alta de equipo desde formulario',
  datos_nuevos: {
    numero_serie: numeroSerie,
    tipo,
    marca,
    modelo,
    hotel,
    estado: 'ALTA',
    masDeTresAnios
  }
});
            
              io.emit('inventario_actualizado');
            
            
              res.status(200).json({ mensaje: '✅ Equipo registrado correctamente' });
           
            });
          });
        });
      });
    });
  });
});

module.exports = router;
