const express = require('express');
const router = express.Router();
const db = require('../db');
const path = require('path');
const fs = require('fs');
const ExcelJS = require('exceljs');

router.get('/excel', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
  SELECT 
      e.id_equipo,
      e.numero_serie,
      e.fecha_registro,
      tipo.nombre_tipo AS tipo,
      marca.nombre_marca AS marca,
      modelo.nombre_modelo AS modelo,
      so.nombre_so AS sistema_operativo,
      ram.capacidad AS ram,
      proc.nombre_procesador AS procesador,
      dd.tipo_disco AS disco_duro,
      ai.capacidad AS almacenamiento,
      al.numero_factura,
      al.fecha_compra AS fecha_factura,
      prov.nombre_proveedor AS proveedor,
      h.nombre_hotel AS hotel,
      
        -- 🟢 ESTADO CALCULADO
      CASE
        WHEN est.nombre_estado = 'BAJA' THEN 'BAJA'
        WHEN asignaciones.tipo_asignacion = 'Préstamo' THEN 'PRÉSTAMO'
        WHEN asignaciones.tipo_asignacion = 'Resguardo' THEN 'RESGUARDO'
        ELSE 'ALTA'
      END AS estado,

      -- 🟢 Usuario que lo asignó
      u.username AS asignado_por_username,
      u.nombre AS asignado_por_nombre,

      -- 🟢 Usuario que lo creó
      creador.username AS creado_por_username,
      creador.nombre AS creado_por_nombre,

      -- 🔍 Tipo de asignación
      asignaciones.tipo_asignacion,

      -- 🔴 Usuario que dio de baja
      baja_user.username AS dado_de_baja_por_username,
      baja_user.nombre AS dado_de_baja_por_nombre,
      b.motivo,
      b.fecha_baja,

      -- ✅ Datos de colaborador (resguardo o préstamo)
      datos_colaborador.nombre_colaborador,
      datos_colaborador.direccion,
      datos_colaborador.gerencia,
      datos_colaborador.nombre_equipo,
      datos_colaborador.plataforma,
      asignaciones.fecha_asignacion,

      h_origen.nombre_hotel AS hotel_origen

    FROM equipos e
    JOIN inventario inv ON e.id_equipo = inv.id_equipo
    LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
    LEFT JOIN marca ON e.id_marca = marca.id_marca
    LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
    LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
    LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
    LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
    LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
    LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
    LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
    LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
    LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
    LEFT JOIN estado_equipo est ON inv.id_estado = est.id_estado

    -- 🔗 Asignaciones (resguardo o préstamo)
    LEFT JOIN (
      SELECT id_equipo, asignado_por, fecha_asignacion, 'Resguardo' AS tipo_asignacion FROM resguardo_equipos
      UNION
      SELECT id_equipo, asignado_por, fecha_asignacion, 'Préstamo' AS tipo_asignacion FROM prestamo_equipos
    ) asignaciones ON asignaciones.id_equipo = e.id_equipo

    LEFT JOIN usuarios u ON asignaciones.asignado_por = u.id_user
    LEFT JOIN usuarios creador ON e.id_user = creador.id_user
    LEFT JOIN bajas b ON b.id_equipo = e.id_equipo
    LEFT JOIN usuarios baja_user ON b.id_user = baja_user.id_user

    -- ✅ Unir datos del colaborador (resguardo o préstamo)
    LEFT JOIN (
      SELECT 
        re.id_equipo,
        r.nombre_colaborador,
        r.direccion,
        r.gerencia,
        r.nombre_equipo,
        r.plataforma,
        r.id_hotel_origen
      FROM resguardos r
      JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
      UNION
      SELECT 
        pe.id_equipo,
        p.nombre_colaborador,
        p.direccion,
        p.gerencia,
        p.nombre_equipo,
        p.plataforma,
        p.id_hotel_origen
      FROM prestamos p
      JOIN prestamo_equipos pe ON p.id_prestamo = pe.id_prestamo
    ) datos_colaborador ON datos_colaborador.id_equipo = e.id_equipo

    -- ✅ Hotel origen del colaborador
    LEFT JOIN hoteles h_origen ON datos_colaborador.id_hotel_origen = h_origen.id_hotel

    ORDER BY e.id_equipo DESC;
    `);

    if (!Array.isArray(rows)) {
      return res.status(400).json({ error: 'La consulta no devolvió un arreglo' });
    }

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Reporte General');

worksheet.columns = [
  { header: 'Hotel', key: 'hotel', width: 25 },
  { header: 'Fecha Alta Sistema', key: 'fecha_registro', width: 20 },
  { header: 'Ing ALTA', key: 'creado_por_nombre', width: 25 },
  { header: 'Fecha de factura', key: 'fecha_factura', width: 20 },
  { header: 'No. Factura', key: 'numero_factura', width: 20 },
  { header: 'Proveedor', key: 'proveedor', width: 20 },
  { header: 'Tipo equipo', key: 'tipo', width: 20 },
  { header: 'Marca', key: 'marca', width: 15 },
  { header: 'Modelo', key: 'modelo', width: 20 },
  { header: 'No. Serie', key: 'numero_serie', width: 20 },
  { header: 'status', key: 'estado', width: 15 },
  { header: 'Ing BAJA', key: 'dado_de_baja_por_nombre', width: 25 },
  { header: 'Motivo de baja', key: 'motivo', width: 30 },
  { header: 'Fecha de baja', key: 'fecha_baja', width: 20 },
  { header: 'Sistema operativo', key: 'sistema_operativo', width: 20 },
  { header: 'RAM', key: 'ram', width: 10 },
  { header: 'Procesador', key: 'procesador', width: 20 },
  { header: 'Disco duro', key: 'disco_duro', width: 15 },
  { header: 'Almacenamiento interno', key: 'almacenamiento', width: 20 },
  { header: 'Colaborador asignado', key: 'nombre_colaborador', width: 25 },
  { header: 'Dirección del colaborador', key: 'direccion', width: 25 },
  { header: 'Gerencia del colaborador', key: 'gerencia', width: 20 },
  { header: 'Nombre del equipo', key: 'nombre_equipo', width: 25 },
  { header: 'Plataforma', key: 'plataforma', width: 20 },
  { header: 'Fecha de asignacion', key: 'fecha_asignacion', width: 20 },
  { header: 'Hotel de asignacion', key: 'hotel_origen', width: 25 },
  { header: 'Ing que Asigno', key: 'asignado_por_nombre', width: 25 }
];




    rows.forEach(row => {
      Object.keys(row).forEach(key => {
        if (row[key] === null) row[key] = 'Sin registrar';
      });
      worksheet.addRow(row);
    });

    res.setHeader(
      'Content-Type',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );
    res.setHeader(
      'Content-Disposition',
      'attachment; filename=Reporte_General_Equipos.xlsx'
    );

    await workbook.xlsx.write(res);
    res.end();
  } catch (error) {
    console.error('Error al generar Excel:', error);
    res.status(500).send('Error al generar el archivo Excel.');
  }
});

router.get('/excel-bajas', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
      SELECT 
        b.id_baja, 
        b.descripcion, 
        b.motivo, 
        b.hotel_origen,
        eq.numero_serie,
        evi.nombre_archivo
      FROM bajas b
      JOIN equipos eq ON b.id_equipo = eq.id_equipo
      LEFT JOIN baja_evidencias evi ON b.id_baja = evi.id_baja
    `);

    // Agrupar por ID de baja
    const agrupadas = {};
    rows.forEach(row => {
      if (!agrupadas[row.id_baja]) {
        agrupadas[row.id_baja] = {
          id_baja: row.id_baja,
          descripcion: row.descripcion,
          motivo: row.motivo,
          hotel_origen: row.hotel_origen,
          numero_serie: row.numero_serie,
          evidencias: []
        };
      }
      if (row.nombre_archivo) {
        agrupadas[row.id_baja].evidencias.push(row.nombre_archivo);
      }
    });

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Reporte Bajas');

    worksheet.columns = [
      { header: 'CANTIDAD', key: 'cantidad', width: 10 },
      { header: 'ID', key: 'id_vacio', width: 10 },
      { header: 'HOTEL ORIGEN', key: 'hotel_origen', width: 25 },
      { header: 'DESCRIPCIÓN', key: 'descripcion', width: 30 },
      { header: 'FOTO', key: 'evidencia', width: 30 },
      { header: 'MOTIVO DE BAJA', key: 'motivo', width: 20 },
      { header: '# SERIE', key: 'numero_serie', width: 25 },
      { header: 'Precio', key: 'precio', width: 15 },
      { header: 'Importe', key: 'importe', width: 15 },
      { header: 'Destino', key: 'destino', width: 15 },
      { header: 'Comentarios', key: 'comentarios', width: 20 }
    ];

    let rowIndex = 2;

    if (Object.keys(agrupadas).length > 0) {
      for (const baja of Object.values(agrupadas)) {
        const evidencias = baja.evidencias.length ? baja.evidencias : [null];

        for (const evi of evidencias) {
          worksheet.addRow({
            cantidad: 1,
            id_vacio: '',
            hotel_origen: baja.hotel_origen,
            descripcion: baja.descripcion,
            evidencia: '',
            motivo: baja.motivo,
            numero_serie: baja.numero_serie,
            precio: '',
            importe: '',
            destino: 'BAJAS',
            comentarios: 'SIN REPARACION'
          });

          if (evi) {
            const imgPath = path.join(__dirname, '..', 'uploads', 'bajas', evi);

            if (fs.existsSync(imgPath)) {
              const imageId = workbook.addImage({
                filename: imgPath,
                extension: path.extname(evi).substring(1)
              });

              worksheet.addImage(imageId, {
                tl: { col: 4, row: rowIndex - 1 },
                ext: { width: 120, height: 120 }
              });

              worksheet.getRow(rowIndex).height = 90;
            }
          }

          rowIndex++;
        }
      }
    } else {
      worksheet.addRow({
        cantidad: 1,
        id_vacio: '',
        descripcion: '',
        evidencia: '',
        motivo: '',
        numero_serie: '',
        precio: '',
        importe: '',
        destino: '',
        comentarios: ''
      });
    }

    res.setHeader(
      'Content-Type',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );
    res.setHeader(
      'Content-Disposition',
      `attachment; filename=Reporte_Bajas_Equipos.xlsx`
    );

    await workbook.xlsx.write(res);
    res.end();
  } catch (error) {
    console.error('❌ Error generando Excel de bajas:', error);
    res.status(500).send('Error al generar el archivo Excel de bajas.');
  }
});

router.get('/sinasignar/excel', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
     SELECT 
        h.nombre_hotel AS hotel,
        e.fecha_registro,
        creador.nombre AS creado_por,
        al.fecha_compra,
        al.numero_factura,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        est.nombre_estado AS estado,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno
      FROM equipos e
      JOIN inventario inv ON e.id_equipo = inv.id_equipo
      JOIN estado_equipo est ON inv.id_estado = est.id_estado
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
      LEFT JOIN usuarios creador ON e.id_user = creador.id_user
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      WHERE est.nombre_estado = 'ALTA'
      ORDER BY e.fecha_registro DESC
    `);

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Equipos Sin Asignar');

    worksheet.columns = [
      { header: 'Hotel', key: 'hotel', width: 20 },
      { header: 'Fecha Alta Sistema', key: 'fecha_registro', width: 20 },
      { header: 'Ing ALTA', key: 'creado_por', width: 20 },
      { header: 'Fecha de factura', key: 'fecha_compra', width: 20 },
      { header: 'No. Factura', key: 'numero_factura', width: 20 },
      { header: 'Proveedor', key: 'proveedor', width: 20 },
      { header: 'Tipo equipo', key: 'tipo_equipo', width: 20 },
      { header: 'Marca', key: 'marca', width: 20 },
      { header: 'Modelo', key: 'modelo', width: 20 },
      { header: 'No. Serie', key: 'numero_serie', width: 20 },
      { header: 'Estado', key: 'estado', width: 15 },
      { header: 'Sistema Operativo', key: 'sistema_operativo', width: 20 },
      { header: 'RAM', key: 'ram', width: 15 },
      { header: 'Procesador', key: 'procesador', width: 20 },
      { header: 'Disco Duro', key: 'disco_duro', width: 20 },
      { header: 'Almacenamiento Interno', key: 'almacenamiento_interno', width: 20 },
    ];

    // Formatear fechas
    rows.forEach((row) => {
      row.fecha_registro = row.fecha_registro ? new Date(row.fecha_registro).toLocaleDateString() : '';
      row.fecha_compra = row.fecha_compra ? new Date(row.fecha_compra).toLocaleDateString() : '';
      worksheet.addRow(row);
    });

    res.setHeader(
      'Content-Type',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );
    res.setHeader(
      'Content-Disposition',
      'attachment; filename=Reporte_Equipos_Sin_Asignar.xlsx'
    );

    await workbook.xlsx.write(res);
    res.end();
  } catch (error) {
    console.error('❌ Error al generar Excel de sin asignar:', error);
    res.status(500).json({ success: false, error: 'Error al generar Excel' });
  }
});

router.get('/resguardos/excel', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
      SELECT 
        h.nombre_hotel AS hotel,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        'RESGUARDO' AS status,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno,
        datos_colaborador.nombre_colaborador AS colaborador_asignado,
        datos_colaborador.direccion AS direccion_colaborador,
        datos_colaborador.gerencia AS gerencia_colaborador,
        datos_colaborador.nombre_equipo,
        datos_colaborador.plataforma,
        asignaciones.fecha_asignacion,
        h_origen.nombre_hotel AS hotel_asignacion,
        u.nombre AS ing_asigno
      FROM equipos e
      JOIN inventario inv ON e.id_equipo = inv.id_equipo
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
      LEFT JOIN (
        SELECT id_equipo, asignado_por, fecha_asignacion FROM resguardo_equipos
      ) asignaciones ON asignaciones.id_equipo = e.id_equipo
      LEFT JOIN usuarios u ON asignaciones.asignado_por = u.id_user
      LEFT JOIN (
        SELECT 
          re.id_equipo,
          r.nombre_colaborador,
          r.direccion,
          r.gerencia,
          r.nombre_equipo,
          r.plataforma,
          r.id_hotel_origen
        FROM resguardos r
        JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
      ) datos_colaborador ON datos_colaborador.id_equipo = e.id_equipo
      LEFT JOIN hoteles h_origen ON datos_colaborador.id_hotel_origen = h_origen.id_hotel
      WHERE asignaciones.id_equipo IS NOT NULL
      ORDER BY asignaciones.fecha_asignacion DESC
    `);

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Resguardos');

    // Encabezados
    worksheet.columns = [
      { header: 'Hotel', key: 'hotel' },
      { header: 'Proveedor', key: 'proveedor' },
      { header: 'Tipo equipo', key: 'tipo_equipo' },
      { header: 'Marca', key: 'marca' },
      { header: 'Modelo', key: 'modelo' },
      { header: 'Número de serie', key: 'numero_serie' },
      { header: 'Status', key: 'status' },
      { header: 'Sistema operativo', key: 'sistema_operativo' },
      { header: 'RAM', key: 'ram' },
      { header: 'Procesador', key: 'procesador' },
      { header: 'Disco duro', key: 'disco_duro' },
      { header: 'Almacenamiento interno', key: 'almacenamiento_interno' },
      { header: 'Colaborador asignado', key: 'colaborador_asignado' },
      { header: 'Dirección del colaborador', key: 'direccion_colaborador' },
      { header: 'Gerencia del colaborador', key: 'gerencia_colaborador' },
      { header: 'Nombre del equipo', key: 'nombre_equipo' },
      { header: 'Plataforma', key: 'plataforma' },
      { header: 'Fecha de asignación', key: 'fecha_asignacion' },
      { header: 'Hotel de asignación', key: 'hotel_asignacion' },
      { header: 'Ing que asignó', key: 'ing_asigno' }
    ];

    // Agregar filas
    rows.forEach(row => {
      worksheet.addRow(row);
    });

    // Enviar archivo
    res.setHeader(
      'Content-Disposition',
      'attachment; filename="Reporte_Resguardos_General.xlsx"'
    );
    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    await workbook.xlsx.write(res);
    res.end();
  } catch (err) {
    console.error('❌ Error al generar Excel:', err);
    res.status(500).json({ error: 'Error al generar Excel de resguardos' });
  }
});

// 📥 Con colaborador
router.get('/resguardos/colaborador/excel', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
      SELECT 
        h.nombre_hotel AS hotel,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        'RESGUARDO' AS status,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno,
        r.num_colaborador,
        r.nombre_colaborador AS colaborador_asignado,
        r.direccion AS direccion_colaborador,
        r.gerencia AS gerencia_colaborador,
        r.nombre_equipo,
        r.plataforma,
        re.fecha_asignacion,
        h_origen.nombre_hotel AS hotel_asignacion,
        u.nombre AS ing_asigno
      FROM resguardos r
      JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
      JOIN equipos e ON re.id_equipo = e.id_equipo
      LEFT JOIN inventario inv ON e.id_equipo = inv.id_equipo
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
      LEFT JOIN hoteles h_origen ON r.id_hotel_origen = h_origen.id_hotel
      LEFT JOIN usuarios u ON re.asignado_por = u.id_user
      WHERE r.num_colaborador IS NOT NULL
      ORDER BY re.fecha_asignacion DESC
    `);

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Con Colaborador');

    worksheet.columns = [
      { header: 'Hotel', key: 'hotel' },
      { header: 'Proveedor', key: 'proveedor' },
      { header: 'Tipo equipo', key: 'tipo_equipo' },
      { header: 'Marca', key: 'marca' },
      { header: 'Modelo', key: 'modelo' },
      { header: 'No. Serie', key: 'numero_serie' },
      { header: 'Status', key: 'status' },
      { header: 'Sistema operativo', key: 'sistema_operativo' },
      { header: 'RAM', key: 'ram' },
      { header: 'Procesador', key: 'procesador' },
      { header: 'Disco duro', key: 'disco_duro' },
      { header: 'Almacenamiento interno', key: 'almacenamiento_interno' },
      { header: 'Número colaborador', key: 'num_colaborador' },
      { header: 'Colaborador asignado', key: 'colaborador_asignado' },
      { header: 'Dirección', key: 'direccion_colaborador' },
      { header: 'Gerencia', key: 'gerencia_colaborador' },
      { header: 'Nombre del equipo', key: 'nombre_equipo' },
      { header: 'Plataforma', key: 'plataforma' },
      { header: 'Fecha de asignación', key: 'fecha_asignacion' },
      { header: 'Hotel asignación', key: 'hotel_asignacion' },
      { header: 'Ing. que asignó', key: 'ing_asigno' }
    ];

    worksheet.addRows(rows);
    res.setHeader('Content-Disposition', 'attachment; filename=Resguardos_Con_No.Colaborador.xlsx');
    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    await workbook.xlsx.write(res);
    res.end();
  } catch (error) {
    console.error('❌ Error al generar Excel con colaborador:', error);
    res.status(500).send('Error al generar Excel');
  }
});

// 📥 Sin colaborador
router.get('/resguardos/sincolaborador/excel', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
         SELECT 
        h.nombre_hotel AS hotel,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        'RESGUARDO' AS status,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno,
        r.nombre_colaborador AS colaborador_asignado,
        r.direccion AS direccion_colaborador,
        r.gerencia AS gerencia_colaborador,
        r.nombre_equipo,
        r.plataforma,
        re.fecha_asignacion,
        h_origen.nombre_hotel AS hotel_asignacion,
        u.nombre AS ing_asigno
      FROM resguardos r
      JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
      JOIN equipos e ON re.id_equipo = e.id_equipo
      LEFT JOIN inventario inv ON e.id_equipo = inv.id_equipo
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
      LEFT JOIN hoteles h_origen ON r.id_hotel_origen = h_origen.id_hotel
      LEFT JOIN usuarios u ON re.asignado_por = u.id_user
      WHERE r.num_colaborador IS NULL
      ORDER BY re.fecha_asignacion DESC
    `);

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Sin Colaborador');

    worksheet.columns = [
      { header: 'Hotel', key: 'hotel' },
      { header: 'Proveedor', key: 'proveedor' },
      { header: 'Tipo equipo', key: 'tipo_equipo' },
      { header: 'Marca', key: 'marca' },
      { header: 'Modelo', key: 'modelo' },
      { header: 'No. Serie', key: 'numero_serie' },
      { header: 'Status', key: 'status' },
      { header: 'Sistema operativo', key: 'sistema_operativo' },
      { header: 'RAM', key: 'ram' },
      { header: 'Procesador', key: 'procesador' },
      { header: 'Disco duro', key: 'disco_duro' },
      { header: 'Almacenamiento interno', key: 'almacenamiento_interno' },
      { header: 'Colaborador asignado', key: 'colaborador_asignado' },
      { header: 'Dirección', key: 'direccion_colaborador' },
      { header: 'Gerencia', key: 'gerencia_colaborador' },
      { header: 'Nombre del equipo', key: 'nombre_equipo' },
      { header: 'Plataforma', key: 'plataforma' },
      { header: 'Fecha de asignación', key: 'fecha_asignacion' },
      { header: 'Hotel asignación', key: 'hotel_asignacion' },
      { header: 'Ing. que asignó', key: 'ing_asigno' }
    ];

    worksheet.addRows(rows);
    res.setHeader('Content-Disposition', 'attachment; filename=Resguardos_Sin_No.Colaborador.xlsx');
    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    await workbook.xlsx.write(res);
    res.end();
  } catch (error) {
    console.error('❌ Error al generar Excel sin colaborador:', error);
    res.status(500).send('Error al generar Excel');
  }
});

router.get('/prestamos/excel', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
  SELECT 
        h.nombre_hotel AS hotel,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        'PRÉSTAMO' AS status,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno,
        datos_colaborador.nombre_colaborador AS colaborador_asignado,
        datos_colaborador.direccion AS direccion_colaborador,
        datos_colaborador.gerencia AS gerencia_colaborador,
        datos_colaborador.nombre_equipo,
        datos_colaborador.plataforma,
        datos_colaborador.fecha_prestamo,           -- ✅ FALTA
        datos_colaborador.fecha_devolucion,         -- ✅ FALTA
        asignaciones.fecha_asignacion,
        h_origen.nombre_hotel AS hotel_asignacion,
        u.nombre AS ing_asigno
      FROM equipos e
      JOIN inventario inv ON e.id_equipo = inv.id_equipo
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel

      LEFT JOIN (
        SELECT id_equipo, asignado_por, fecha_asignacion FROM prestamo_equipos
      ) asignaciones ON asignaciones.id_equipo = e.id_equipo

      LEFT JOIN usuarios u ON asignaciones.asignado_por = u.id_user

      LEFT JOIN (
        SELECT 
          pe.id_equipo,
          p.nombre_colaborador,
          p.direccion,
          p.gerencia,
          p.nombre_equipo,
          p.plataforma,
          p.id_hotel_origen,
          p.fecha_prestamo,
          p.fecha_devolucion
        FROM prestamos p
        JOIN prestamo_equipos pe ON p.id_prestamo = pe.id_prestamo
      ) datos_colaborador ON datos_colaborador.id_equipo = e.id_equipo

      LEFT JOIN hoteles h_origen ON datos_colaborador.id_hotel_origen = h_origen.id_hotel

      WHERE asignaciones.id_equipo IS NOT NULL
      ORDER BY asignaciones.fecha_asignacion DESC
    `);

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Préstamos');

    worksheet.columns = [
      { header: 'Hotel', key: 'hotel' },
      { header: 'Proveedor', key: 'proveedor' },
      { header: 'Tipo equipo', key: 'tipo_equipo' },
      { header: 'Marca', key: 'marca' },
      { header: 'Modelo', key: 'modelo' },
      { header: 'Número de serie', key: 'numero_serie' },
      { header: 'Status', key: 'status' },
      { header: 'Sistema operativo', key: 'sistema_operativo' },
      { header: 'RAM', key: 'ram' },
      { header: 'Procesador', key: 'procesador' },
      { header: 'Disco duro', key: 'disco_duro' },
      { header: 'Almacenamiento interno', key: 'almacenamiento_interno' },
      { header: 'Colaborador asignado', key: 'colaborador_asignado' },
      { header: 'Dirección del colaborador', key: 'direccion_colaborador' },
      { header: 'Gerencia del colaborador', key: 'gerencia_colaborador' },
      { header: 'Nombre del equipo', key: 'nombre_equipo' },
      { header: 'Plataforma', key: 'plataforma' },
      { header: 'Fecha de préstamo', key: 'fecha_prestamo' },
      { header: 'Fecha de devolución', key: 'fecha_devolucion' },
      { header: 'Hotel asignación', key: 'hotel_asignacion' },
      { header: 'Ing que asignó', key: 'ing_asigno' }
    ];
    
    rows.forEach(row => {
      worksheet.addRow(row);
    });

    res.setHeader('Content-Disposition', 'attachment; filename="Reporte_Prestamos_General.xlsx"');
    res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    await workbook.xlsx.write(res);
    res.end();
  } catch (err) {
    console.error('❌ Error al generar Excel de préstamos:', err);
    res.status(500).json({ error: 'Error al generar Excel de préstamos' });
  }
});






router.get('/general', (req, res) => {
  const sql = `
    SELECT 
      e.id_equipo,
      e.numero_serie,
      e.fecha_registro,
      tipo.nombre_tipo AS tipo,
      marca.nombre_marca AS marca,
      modelo.nombre_modelo AS modelo,
      so.nombre_so AS sistema_operativo,
      ram.capacidad AS ram,
      proc.nombre_procesador AS procesador,
      dd.tipo_disco AS disco_duro,
      ai.capacidad AS almacenamiento,
      al.numero_factura,
      al.fecha_compra AS fecha_factura,
      prov.nombre_proveedor AS proveedor,
      h.nombre_hotel AS hotel,
      
        -- 🟢 ESTADO CALCULADO
      CASE
        WHEN est.nombre_estado = 'BAJA' THEN 'BAJA'
        WHEN asignaciones.tipo_asignacion = 'Préstamo' THEN 'PRÉSTAMO'
        WHEN asignaciones.tipo_asignacion = 'Resguardo' THEN 'RESGUARDO'
        ELSE 'ALTA'
      END AS estado,

      -- 🟢 Usuario que lo asignó
      u.username AS asignado_por_username,
      u.nombre AS asignado_por_nombre,

      -- 🟢 Usuario que lo creó
      creador.username AS creado_por_username,
      creador.nombre AS creado_por_nombre,

      -- 🔍 Tipo de asignación
      asignaciones.tipo_asignacion,

      -- 🔴 Usuario que dio de baja
      baja_user.username AS dado_de_baja_por_username,
      baja_user.nombre AS dado_de_baja_por_nombre,
      b.motivo,
      b.fecha_baja,

      -- ✅ Datos de colaborador (resguardo o préstamo)
      datos_colaborador.nombre_colaborador,
      datos_colaborador.direccion,
      datos_colaborador.gerencia,
      datos_colaborador.nombre_equipo,
      datos_colaborador.plataforma,
      asignaciones.fecha_asignacion,

      h_origen.nombre_hotel AS hotel_origen

    FROM equipos e
    JOIN inventario inv ON e.id_equipo = inv.id_equipo
    LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
    LEFT JOIN marca ON e.id_marca = marca.id_marca
    LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
    LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
    LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
    LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
    LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
    LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
    LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
    LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
    LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
    LEFT JOIN estado_equipo est ON inv.id_estado = est.id_estado

    -- 🔗 Asignaciones (resguardo o préstamo)
    LEFT JOIN (
      SELECT id_equipo, asignado_por, fecha_asignacion, 'Resguardo' AS tipo_asignacion FROM resguardo_equipos
      UNION
      SELECT id_equipo, asignado_por, fecha_asignacion, 'Préstamo' AS tipo_asignacion FROM prestamo_equipos
    ) asignaciones ON asignaciones.id_equipo = e.id_equipo

    LEFT JOIN usuarios u ON asignaciones.asignado_por = u.id_user
    LEFT JOIN usuarios creador ON e.id_user = creador.id_user
    LEFT JOIN bajas b ON b.id_equipo = e.id_equipo
    LEFT JOIN usuarios baja_user ON b.id_user = baja_user.id_user

    -- ✅ Unir datos del colaborador (resguardo o préstamo)
    LEFT JOIN (
      SELECT 
        re.id_equipo,
        r.nombre_colaborador,
        r.direccion,
        r.gerencia,
        r.nombre_equipo,
        r.plataforma,
        r.id_hotel_origen
      FROM resguardos r
      JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
      UNION
      SELECT 
        pe.id_equipo,
        p.nombre_colaborador,
        p.direccion,
        p.gerencia,
        p.nombre_equipo,
        p.plataforma,
        p.id_hotel_origen
      FROM prestamos p
      JOIN prestamo_equipos pe ON p.id_prestamo = pe.id_prestamo
    ) datos_colaborador ON datos_colaborador.id_equipo = e.id_equipo

    -- ✅ Hotel origen del colaborador
    LEFT JOIN hoteles h_origen ON datos_colaborador.id_hotel_origen = h_origen.id_hotel

    ORDER BY e.id_equipo DESC;
  `;

  db.query(sql, (err, results) => {
    if (err) {
      console.error('Error en la consulta SQL:', err);
      return res.status(500).json({ error: err.message });
    }
    res.json(results);
  });
});


router.get('/bajas', (req, res) => {
  const sql = `
    SELECT 
      e.id_equipo,
      e.numero_serie,
      tipo.nombre_tipo AS tipo,
      marca.nombre_marca AS marca,
      modelo.nombre_modelo AS modelo,
      so.nombre_so AS sistema_operativo,
      ram.capacidad AS ram,
      proc.nombre_procesador AS procesador,
      dd.tipo_disco AS disco_duro,
      ai.capacidad AS almacenamiento,
      al.numero_factura,
      al.fecha_compra,
      prov.nombre_proveedor AS proveedor,
      b.hotel_origen AS hotel,
      inv.id_estado,
      est.nombre_estado AS estado,
      b.fecha_baja,
      b.motivo,
      b.descripcion,
      u.username AS dado_de_baja_por_username,
      u.nombre AS dado_de_baja_por_nombre

    FROM bajas b
    JOIN equipos e ON b.id_equipo = e.id_equipo
    JOIN inventario inv ON inv.id_equipo = e.id_equipo
    LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
    LEFT JOIN marca ON e.id_marca = marca.id_marca
    LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
    LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
    LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
    LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
    LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
    LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
    LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
    LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
    LEFT JOIN estado_equipo est ON inv.id_estado = est.id_estado
    LEFT JOIN usuarios u ON b.id_user = u.id_user

    ORDER BY b.fecha_baja DESC
  `;

  db.query(sql, (err, results) => {
    if (err) {
      console.error('❌ Error al consultar reporte de bajas:', err);
      return res.status(500).json({ error: 'Error al consultar reporte de bajas' });
    }

    res.json(results);
  });
});

// 📄 Obtener equipos sin asignar con estado ALTA
router.get('/sinasignar', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
       SELECT 
        h.nombre_hotel AS hotel,
        e.fecha_registro,
        creador.nombre AS creado_por,
        al.fecha_compra,
        al.numero_factura,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        est.nombre_estado AS estado,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno
      FROM equipos e
      JOIN inventario inv ON e.id_equipo = inv.id_equipo
      JOIN estado_equipo est ON inv.id_estado = est.id_estado
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
      LEFT JOIN usuarios creador ON e.id_user = creador.id_user
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      WHERE est.nombre_estado = 'ALTA'
      ORDER BY e.fecha_registro DESC
    `);

    res.json({ success: true, data: rows });
  } catch (error) {
    console.error('❌ Error al obtener datos sin asignar:', error);
    res.status(500).json({ success: false, error: 'Error al obtener datos' });
  }
});


router.get('/resguardos', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
      SELECT 
        h.nombre_hotel AS hotel,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        'RESGUARDO' AS status,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno,
        datos_colaborador.nombre_colaborador AS colaborador_asignado,
        datos_colaborador.direccion AS direccion_colaborador,
        datos_colaborador.gerencia AS gerencia_colaborador,
        datos_colaborador.nombre_equipo,
        datos_colaborador.plataforma,
        asignaciones.fecha_asignacion,
        h_origen.nombre_hotel AS hotel_asignacion,
        u.nombre AS ing_asigno

      FROM equipos e
      JOIN inventario inv ON e.id_equipo = inv.id_equipo
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel

      -- Solo equipos con asignación de tipo 'Resguardo'
      LEFT JOIN (
        SELECT id_equipo, asignado_por, fecha_asignacion FROM resguardo_equipos
      ) asignaciones ON asignaciones.id_equipo = e.id_equipo

      LEFT JOIN usuarios u ON asignaciones.asignado_por = u.id_user

      LEFT JOIN (
        SELECT 
          re.id_equipo,
          r.nombre_colaborador,
          r.direccion,
          r.gerencia,
          r.nombre_equipo,
          r.plataforma,
          r.id_hotel_origen
        FROM resguardos r
        JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
      ) datos_colaborador ON datos_colaborador.id_equipo = e.id_equipo

      LEFT JOIN hoteles h_origen ON datos_colaborador.id_hotel_origen = h_origen.id_hotel

      WHERE asignaciones.id_equipo IS NOT NULL
      ORDER BY asignaciones.fecha_asignacion DESC
    `);

    res.json(rows);
  } catch (error) {
    console.error('❌ Error al obtener resguardos:', error);
    res.status(500).json({ error: 'Error al obtener resguardos' });
  }
});

router.get('/resguardos/colaborador', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
      SELECT 
        h.nombre_hotel AS hotel,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        'RESGUARDO' AS status,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno,
        r.num_colaborador,
        r.nombre_colaborador AS colaborador_asignado,
        r.direccion AS direccion_colaborador,
        r.gerencia AS gerencia_colaborador,
        r.nombre_equipo,
        r.plataforma,
        re.fecha_asignacion,
        h_origen.nombre_hotel AS hotel_asignacion,
        u.nombre AS ing_asigno
      FROM resguardos r
      JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
      JOIN equipos e ON re.id_equipo = e.id_equipo
      LEFT JOIN inventario inv ON e.id_equipo = inv.id_equipo
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
      LEFT JOIN hoteles h_origen ON r.id_hotel_origen = h_origen.id_hotel
      LEFT JOIN usuarios u ON re.asignado_por = u.id_user
      WHERE r.num_colaborador IS NOT NULL
      ORDER BY re.fecha_asignacion DESC
    `);
    res.json(rows);
  } catch (err) {
    console.error('❌ Error al obtener resguardos con colaborador:', err);
    res.status(500).json({ error: 'Error al obtener resguardos con colaborador' });
  }
});

router.get('/resguardos/sincolaborador', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
      SELECT 
        h.nombre_hotel AS hotel,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        'RESGUARDO' AS status,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno,
        r.nombre_colaborador AS colaborador_asignado,
        r.direccion AS direccion_colaborador,
        r.gerencia AS gerencia_colaborador,
        r.nombre_equipo,
        r.plataforma,
        re.fecha_asignacion,
        h_origen.nombre_hotel AS hotel_asignacion,
        u.nombre AS ing_asigno
      FROM resguardos r
      JOIN resguardo_equipos re ON r.id_resguardo = re.id_resguardo
      JOIN equipos e ON re.id_equipo = e.id_equipo
      LEFT JOIN inventario inv ON e.id_equipo = inv.id_equipo
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel
      LEFT JOIN hoteles h_origen ON r.id_hotel_origen = h_origen.id_hotel
      LEFT JOIN usuarios u ON re.asignado_por = u.id_user
      WHERE r.num_colaborador IS NULL
      ORDER BY re.fecha_asignacion DESC
    `);
    res.json(rows);
  } catch (err) {
    console.error('❌ Error al obtener resguardos sin colaborador:', err);
    res.status(500).json({ error: 'Error al obtener resguardos sin colaborador' });
  }
});


router.get('/prestamos', async (req, res) => {
  try {
    const [rows] = await db.promise().query(`
       SELECT 
        h.nombre_hotel AS hotel,
        prov.nombre_proveedor AS proveedor,
        tipo.nombre_tipo AS tipo_equipo,
        marca.nombre_marca AS marca,
        modelo.nombre_modelo AS modelo,
        e.numero_serie,
        'PRÉSTAMO' AS status,
        so.nombre_so AS sistema_operativo,
        ram.capacidad AS ram,
        proc.nombre_procesador AS procesador,
        dd.tipo_disco AS disco_duro,
        ai.capacidad AS almacenamiento_interno,
        datos_colaborador.nombre_colaborador AS colaborador_asignado,
        datos_colaborador.direccion AS direccion_colaborador,
        datos_colaborador.gerencia AS gerencia_colaborador,
        datos_colaborador.nombre_equipo,
        datos_colaborador.plataforma,
        datos_colaborador.fecha_prestamo,           -- ✅ FALTA
        datos_colaborador.fecha_devolucion,         -- ✅ FALTA
        asignaciones.fecha_asignacion,
        h_origen.nombre_hotel AS hotel_asignacion,
        u.nombre AS ing_asigno
      FROM equipos e
      JOIN inventario inv ON e.id_equipo = inv.id_equipo
      LEFT JOIN tipo_equipo tipo ON e.id_tipo_equipo = tipo.id_tipo_equipo
      LEFT JOIN marca ON e.id_marca = marca.id_marca
      LEFT JOIN modelo ON e.id_modelo = modelo.id_modelo
      LEFT JOIN sistema_operativo so ON e.id_sistema_operativo = so.id_so
      LEFT JOIN memoria_ram ram ON e.id_memoria_ram = ram.id_memoria_ram
      LEFT JOIN procesador proc ON e.id_procesador = proc.id_procesador
      LEFT JOIN tipo_disco_duro dd ON e.id_tipo_disco_duro = dd.id_disco
      LEFT JOIN almacenamiento_interno ai ON e.id_almacenamiento = ai.id_almacenamiento
      LEFT JOIN almacen al ON al.id_equipo = e.id_equipo
      LEFT JOIN proveedor prov ON al.id_proveedor = prov.id_proveedor
      LEFT JOIN hoteles h ON inv.id_hotel = h.id_hotel

      LEFT JOIN (
        SELECT id_equipo, asignado_por, fecha_asignacion FROM prestamo_equipos
      ) asignaciones ON asignaciones.id_equipo = e.id_equipo

      LEFT JOIN usuarios u ON asignaciones.asignado_por = u.id_user

      LEFT JOIN (
        SELECT 
          pe.id_equipo,
          p.nombre_colaborador,
          p.direccion,
          p.gerencia,
          p.nombre_equipo,
          p.plataforma,
          p.id_hotel_origen,
          p.fecha_prestamo,
          p.fecha_devolucion
        FROM prestamos p
        JOIN prestamo_equipos pe ON p.id_prestamo = pe.id_prestamo
      ) datos_colaborador ON datos_colaborador.id_equipo = e.id_equipo

      LEFT JOIN hoteles h_origen ON datos_colaborador.id_hotel_origen = h_origen.id_hotel

      WHERE asignaciones.id_equipo IS NOT NULL
      ORDER BY asignaciones.fecha_asignacion DESC
    `);

    res.json(rows);
  } catch (err) {
    console.error('❌ Error al obtener los préstamos:', err);
    res.status(500).json({ error: 'Error al obtener los préstamos' });
  }
});



module.exports = router;
