const express = require('express');
const cors = require('cors');
const db = require('./db'); // <-- importas el archivo anterior

const app = express();
app.use(cors());
app.use(express.json());

app.get('/equipos', (req, res) => {
  db.query('SELECT * FROM equipos', (err, results) => {
    if (err) {
      res.status(500).json({ error: 'Error al obtener equipos' });
    } else {
      res.json(results);
    }
  });
});

app.listen(3001, () => {
  console.log('🚀 Servidor backend corriendo en http://0.0.0.0:3001');
});
