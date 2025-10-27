const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const http = require('http');
const { Server } = require('socket.io');
const path = require('path');
const fs = require('fs');

const app = express();
const server = http.createServer(app);

const io = new Server(server, {
  cors: {
    origin: '*',
  },
});

// Inyectar io en todas las rutas
app.use((req, res, next) => {
  req.io = io;
  next();
});

// CORS para frontend local y de red
app.use(cors({
  origin: [
    'http://localhost:3000',
    'http://10.1.201.54:3000'
  ],
  credentials: true
}));

io.on('connection', (socket) => {
  console.log('📡 Cliente conectado vía Socket.IO');
  socket.on('disconnect', () => {
    console.log('🔌 Cliente desconectado');
  });
});

app.get('/', (req, res) => {
  res.send('✅ API Inventario TI funcionando correctamente');
});

app.use(express.json());
app.use(helmet());

// 📁 Rutas API
app.use('/api/modelos', require('./routes/modelos'));
app.use('/api/tipos', require('./routes/tipos'));
app.use('/api/marcas', require('./routes/marca'));
app.use('/api/plataformas', require('./routes/plataforma'));
app.use('/api/sistemas-operativos', require('./routes/sistemaoperativo'));
app.use('/api/procesadores', require('./routes/procesadores'));
app.use('/api/proveedores', require('./routes/proveedores'));
app.use('/api/ram', require('./routes/RAM'));
app.use('/api/discos', require('./routes/discos'));
app.use('/api/almacenamientos', require('./routes/almacenamiento'));
app.use('/api/hoteles', require('./routes/hoteles'));
app.use(require('./routes/insertar-equipo'));
app.use('/api/inventario', require('./routes/inventario'));
app.use('/api/usuarios', require('./routes/Usuarios'));
app.use('/api/transferencias', require('./routes/transferencias'));
app.use('/api/resguardos', require('./routes/resguardos'));
app.use('/api/prestamos', require('./routes/prestamos'));
app.use('/api/transferencias_resguardos', require('./routes/transferencias_resguardos'));
app.use('/api/bajas', require('./routes/bajas'));
app.use('/api/prestamos', require('./routes/prestamos'));
app.use('/api/reportes', require('./routes/reportes')); // 👈 Ruta para reportes de resguardo

// === Endpoints para imágenes con headers especiales (evita errores de CORS y Resource Policy) ===

// Firmas
app.get('/uploads/firmas/:file', (req, res) => {
  const filePath = path.join(__dirname, 'uploads', 'firmas', req.params.file);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
  res.setHeader('Cross-Origin-Resource-Policy', 'cross-origin');
  fs.access(filePath, fs.constants.F_OK, (err) => {
    if (err) {
      res.status(404).send('Not found');
    } else {
      res.sendFile(filePath);
    }
  });
});

// Bajas
app.get('/uploads/bajas/:file', (req, res) => {
  const filePath = path.join(__dirname, 'uploads', 'bajas', req.params.file);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
  res.setHeader('Cross-Origin-Resource-Policy', 'cross-origin');
  fs.access(filePath, fs.constants.F_OK, (err) => {
    if (err) {
      res.status(404).send('Not found');
    } else {
      res.sendFile(filePath);
    }
  });
});

// Altas (si usas esta carpeta)
app.get('/uploads/altas/:file', (req, res) => {
  const filePath = path.join(__dirname, 'uploads', 'altas', req.params.file);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
  res.setHeader('Cross-Origin-Resource-Policy', 'cross-origin');
  fs.access(filePath, fs.constants.F_OK, (err) => {
    if (err) {
      res.status(404).send('Not found');
    } else {
      res.sendFile(filePath);
    }
  });
});

// Puedes agregar más carpetas (ejemplo: imagenes generales) siguiendo el mismo patrón

// Servir archivos estáticos (si acceden directamente a /uploads/xxxx)
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

server.listen(3001, () => {
  console.log('🚀 Servidor backend con Socket.IO en http://localhost:3001');
});
