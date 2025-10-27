// db.js
const mysql = require('mysql2');

const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'palace_resorts',
});

db.connect((err) => {
  if (err) {
    console.error('❌ Error al conectar al pool de la base de datos:', err.message);
  return;
    console.log('✅ conectado a la base de datos Mysql');
    
  }
});

module.exports = db;
