-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-08-2025 a las 00:32:35
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `palace_resorts`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen`
--

CREATE TABLE `almacen` (
  `id_almacen` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `numero_factura` varchar(100) DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacenamiento_interno`
--

CREATE TABLE `almacenamiento_interno` (
  `id_almacenamiento` int(11) NOT NULL,
  `capacidad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bajas`
--

CREATE TABLE `bajas` (
  `id_baja` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `fecha_baja` datetime DEFAULT current_timestamp(),
  `hotel_origen` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `baja_evidencias`
--

CREATE TABLE `baja_evidencias` (
  `id_evidencia` int(11) NOT NULL,
  `id_baja` int(11) NOT NULL,
  `nombre_archivo` varchar(255) DEFAULT NULL,
  `ruta_archivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL,
  `numero_serie` varchar(100) NOT NULL,
  `id_sistema_operativo` int(11) DEFAULT NULL,
  `id_procesador` int(11) DEFAULT NULL,
  `id_memoria_ram` int(11) DEFAULT NULL,
  `id_tipo_disco_duro` int(11) DEFAULT NULL,
  `id_almacenamiento` int(11) DEFAULT NULL,
  `fecha_registro` date DEFAULT curdate(),
  `id_tipo_equipo` int(11) NOT NULL,
  `id_marca` int(11) NOT NULL,
  `id_modelo` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_equipo`
--

CREATE TABLE `estado_equipo` (
  `id_estado` int(11) NOT NULL,
  `nombre_estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_equipo`
--

INSERT INTO `estado_equipo` (`id_estado`, `nombre_estado`) VALUES
(1, 'Alta'),
(2, 'Asignado'),
(3, 'Baja');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hoteles`
--

CREATE TABLE `hoteles` (
  `id_hotel` int(11) NOT NULL,
  `nombre_hotel` varchar(150) NOT NULL,
  `estado` enum('ALTA','BAJA') NOT NULL DEFAULT 'ALTA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `id_hotel` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `fecha_asignacion` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_equipos`
--

CREATE TABLE `log_equipos` (
  `id_log` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `descripcion` text DEFAULT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id_marca` int(11) NOT NULL,
  `nombre_marca` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `memoria_ram`
--

CREATE TABLE `memoria_ram` (
  `id_memoria_ram` int(11) NOT NULL,
  `capacidad` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelo`
--

CREATE TABLE `modelo` (
  `id_modelo` int(11) NOT NULL,
  `nombre_modelo` varchar(100) NOT NULL,
  `estado` enum('ALTA','BAJA') NOT NULL DEFAULT 'ALTA',
  `id_tipo_equipo` int(11) DEFAULT NULL,
  `id_marca` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plataforma`
--

CREATE TABLE `plataforma` (
  `id_plataforma` int(11) NOT NULL,
  `nombre_plataforma` varchar(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL,
  `num_colaborador` varchar(50) NOT NULL,
  `nombre_colaborador` varchar(150) NOT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `gerencia` varchar(100) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `fecha_prestamo` date NOT NULL,
  `fecha_devolucion` date NOT NULL,
  `nombre_equipo` varchar(100) NOT NULL,
  `plataforma` varchar(100) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_creador_id` int(11) NOT NULL,
  `prorroga` int(11) DEFAULT 0,
  `id_hotel_origen` int(11) DEFAULT NULL,
  `firma_colaborador` varchar(255) DEFAULT NULL,
  `firma_tecnico` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamo_equipos`
--

CREATE TABLE `prestamo_equipos` (
  `id_prestamo_equipo` int(11) NOT NULL,
  `id_prestamo` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `fecha_asignacion` datetime DEFAULT current_timestamp(),
  `asignado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procesador`
--

CREATE TABLE `procesador` (
  `id_procesador` int(11) NOT NULL,
  `nombre_procesador` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_proveedor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resguardos`
--

CREATE TABLE `resguardos` (
  `id_resguardo` int(11) NOT NULL,
  `num_colaborador` varchar(50) DEFAULT NULL,
  `nombre_colaborador` varchar(150) NOT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `gerencia` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `nombre_equipo` varchar(100) NOT NULL,
  `plataforma` varchar(100) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_creador_id` int(11) NOT NULL,
  `id_hotel_origen` int(11) DEFAULT NULL,
  `firma_colaborador` varchar(255) DEFAULT NULL,
  `firma_tecnico` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resguardo_equipos`
--

CREATE TABLE `resguardo_equipos` (
  `id_resguardo_equipo` int(11) NOT NULL,
  `id_resguardo` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `fecha_asignacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `asignado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(255) NOT NULL,
  `nombre_rol` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'admin'),
(2, 'almacen'),
(3, 'ingeniero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sistema_operativo`
--

CREATE TABLE `sistema_operativo` (
  `id_so` int(11) NOT NULL,
  `nombre_so` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_disco_duro`
--

CREATE TABLE `tipo_disco_duro` (
  `id_disco` int(11) NOT NULL,
  `tipo_disco` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_equipo`
--

CREATE TABLE `tipo_equipo` (
  `id_tipo_equipo` int(11) NOT NULL,
  `nombre_tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_equipo`
--

INSERT INTO `tipo_equipo` (`id_tipo_equipo`, `nombre_tipo`) VALUES
(1, 'CPU'),
(2, 'LAPTOP');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferencias`
--

CREATE TABLE `transferencias` (
  `id_transferencia` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `id_hotel_origen` int(11) NOT NULL,
  `id_hotel_destino` int(11) NOT NULL,
  `estado` enum('PENDIENTE','ACEPTADA','CANCELADA') DEFAULT 'PENDIENTE',
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `fecha_respuesta` datetime DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferencias_resguardos`
--

CREATE TABLE `transferencias_resguardos` (
  `id_transferencia` int(11) NOT NULL,
  `id_resguardo` int(11) DEFAULT NULL,
  `id_hotel_origen` int(11) NOT NULL,
  `id_hotel_destino` int(11) NOT NULL,
  `estado` enum('PENDIENTE','ACEPTADA','CANCELADA') DEFAULT 'PENDIENTE',
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `fecha_respuesta` datetime DEFAULT NULL,
  `creado_por` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_user` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol_id` int(255) DEFAULT NULL,
  `estado` enum('ALTA','BAJA') DEFAULT 'ALTA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_user`, `nombre`, `username`, `password`, `rol_id`, `estado`) VALUES
(1, 'admin', 'admin', '$2b$10$yEB3v143/1ORtwUZRgk3q.w27hLm/8o3PLCoksOd8/HD7dol9Av8O', 1, 'ALTA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_hoteles`
--

CREATE TABLE `usuario_hoteles` (
  `id_user` int(11) NOT NULL,
  `id_hotel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacen`
--
ALTER TABLE `almacen`
  ADD PRIMARY KEY (`id_almacen`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `id_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `almacenamiento_interno`
--
ALTER TABLE `almacenamiento_interno`
  ADD PRIMARY KEY (`id_almacenamiento`),
  ADD UNIQUE KEY `capacidad` (`capacidad`);

--
-- Indices de la tabla `bajas`
--
ALTER TABLE `bajas`
  ADD PRIMARY KEY (`id_baja`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `fk_bajas_usuario` (`id_user`);

--
-- Indices de la tabla `baja_evidencias`
--
ALTER TABLE `baja_evidencias`
  ADD PRIMARY KEY (`id_evidencia`),
  ADD KEY `id_baja` (`id_baja`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id_equipo`),
  ADD UNIQUE KEY `numero_serie` (`numero_serie`),
  ADD KEY `id_sistema_operativo` (`id_sistema_operativo`),
  ADD KEY `id_procesador` (`id_procesador`),
  ADD KEY `id_tipo_disco_duro` (`id_tipo_disco_duro`),
  ADD KEY `id_almacenamiento` (`id_almacenamiento`),
  ADD KEY `id_memoria_ram` (`id_memoria_ram`) USING BTREE,
  ADD KEY `fk_tipo_equipo` (`id_tipo_equipo`),
  ADD KEY `fk_marca` (`id_marca`),
  ADD KEY `fk_modelo` (`id_modelo`),
  ADD KEY `fk_equipo_usuario` (`id_user`);

--
-- Indices de la tabla `estado_equipo`
--
ALTER TABLE `estado_equipo`
  ADD PRIMARY KEY (`id_estado`),
  ADD UNIQUE KEY `nombre_estado` (`nombre_estado`);

--
-- Indices de la tabla `hoteles`
--
ALTER TABLE `hoteles`
  ADD PRIMARY KEY (`id_hotel`),
  ADD UNIQUE KEY `unique_nombre_hotel` (`nombre_hotel`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `id_hotel` (`id_hotel`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `log_equipos`
--
ALTER TABLE `log_equipos`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id_marca`),
  ADD UNIQUE KEY `nombre_marca` (`nombre_marca`);

--
-- Indices de la tabla `memoria_ram`
--
ALTER TABLE `memoria_ram`
  ADD PRIMARY KEY (`id_memoria_ram`),
  ADD UNIQUE KEY `capacidad` (`capacidad`);

--
-- Indices de la tabla `modelo`
--
ALTER TABLE `modelo`
  ADD PRIMARY KEY (`id_modelo`),
  ADD UNIQUE KEY `nombre_modelo` (`nombre_modelo`),
  ADD KEY `fk_modelo_tipo` (`id_tipo_equipo`),
  ADD KEY `fk_modelo_marca` (`id_marca`);

--
-- Indices de la tabla `plataforma`
--
ALTER TABLE `plataforma`
  ADD PRIMARY KEY (`id_plataforma`),
  ADD UNIQUE KEY `unique_nombre_plataforma` (`nombre_plataforma`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id_prestamo`),
  ADD UNIQUE KEY `nombre_equipo` (`nombre_equipo`),
  ADD KEY `fk_usuario_creador` (`usuario_creador_id`);

--
-- Indices de la tabla `prestamo_equipos`
--
ALTER TABLE `prestamo_equipos`
  ADD PRIMARY KEY (`id_prestamo_equipo`),
  ADD KEY `id_prestamo` (`id_prestamo`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `fk_asignado_por` (`asignado_por`);

--
-- Indices de la tabla `procesador`
--
ALTER TABLE `procesador`
  ADD PRIMARY KEY (`id_procesador`),
  ADD UNIQUE KEY `modelo_procesador` (`nombre_procesador`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `nombre_proveedor` (`nombre_proveedor`);

--
-- Indices de la tabla `resguardos`
--
ALTER TABLE `resguardos`
  ADD PRIMARY KEY (`id_resguardo`),
  ADD UNIQUE KEY `nombre_equipo` (`nombre_equipo`),
  ADD KEY `fk_usuario_creador_resguardo` (`usuario_creador_id`);

--
-- Indices de la tabla `resguardo_equipos`
--
ALTER TABLE `resguardo_equipos`
  ADD PRIMARY KEY (`id_resguardo_equipo`),
  ADD KEY `id_resguardo` (`id_resguardo`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `asignado_por` (`asignado_por`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- Indices de la tabla `sistema_operativo`
--
ALTER TABLE `sistema_operativo`
  ADD PRIMARY KEY (`id_so`),
  ADD UNIQUE KEY `nombre_so` (`nombre_so`);

--
-- Indices de la tabla `tipo_disco_duro`
--
ALTER TABLE `tipo_disco_duro`
  ADD PRIMARY KEY (`id_disco`),
  ADD UNIQUE KEY `tipo_disco` (`tipo_disco`);

--
-- Indices de la tabla `tipo_equipo`
--
ALTER TABLE `tipo_equipo`
  ADD PRIMARY KEY (`id_tipo_equipo`),
  ADD UNIQUE KEY `nombre_tipo` (`nombre_tipo`);

--
-- Indices de la tabla `transferencias`
--
ALTER TABLE `transferencias`
  ADD PRIMARY KEY (`id_transferencia`),
  ADD KEY `id_equipo` (`id_equipo`),
  ADD KEY `id_hotel_origen` (`id_hotel_origen`),
  ADD KEY `id_hotel_destino` (`id_hotel_destino`),
  ADD KEY `fk_creado_por` (`creado_por`);

--
-- Indices de la tabla `transferencias_resguardos`
--
ALTER TABLE `transferencias_resguardos`
  ADD PRIMARY KEY (`id_transferencia`),
  ADD KEY `id_resguardo` (`id_resguardo`),
  ADD KEY `id_hotel_origen` (`id_hotel_origen`),
  ADD KEY `id_hotel_destino` (`id_hotel_destino`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `fk_rol` (`rol_id`);

--
-- Indices de la tabla `usuario_hoteles`
--
ALTER TABLE `usuario_hoteles`
  ADD PRIMARY KEY (`id_user`,`id_hotel`),
  ADD KEY `id_hotel` (`id_hotel`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacen`
--
ALTER TABLE `almacen`
  MODIFY `id_almacen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `almacenamiento_interno`
--
ALTER TABLE `almacenamiento_interno`
  MODIFY `id_almacenamiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bajas`
--
ALTER TABLE `bajas`
  MODIFY `id_baja` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `baja_evidencias`
--
ALTER TABLE `baja_evidencias`
  MODIFY `id_evidencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_equipo`
--
ALTER TABLE `estado_equipo`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `hoteles`
--
ALTER TABLE `hoteles`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `log_equipos`
--
ALTER TABLE `log_equipos`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `memoria_ram`
--
ALTER TABLE `memoria_ram`
  MODIFY `id_memoria_ram` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modelo`
--
ALTER TABLE `modelo`
  MODIFY `id_modelo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `plataforma`
--
ALTER TABLE `plataforma`
  MODIFY `id_plataforma` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prestamo_equipos`
--
ALTER TABLE `prestamo_equipos`
  MODIFY `id_prestamo_equipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `procesador`
--
ALTER TABLE `procesador`
  MODIFY `id_procesador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resguardos`
--
ALTER TABLE `resguardos`
  MODIFY `id_resguardo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `resguardo_equipos`
--
ALTER TABLE `resguardo_equipos`
  MODIFY `id_resguardo_equipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `sistema_operativo`
--
ALTER TABLE `sistema_operativo`
  MODIFY `id_so` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_disco_duro`
--
ALTER TABLE `tipo_disco_duro`
  MODIFY `id_disco` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_equipo`
--
ALTER TABLE `tipo_equipo`
  MODIFY `id_tipo_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `transferencias`
--
ALTER TABLE `transferencias`
  MODIFY `id_transferencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transferencias_resguardos`
--
ALTER TABLE `transferencias_resguardos`
  MODIFY `id_transferencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `almacen`
--
ALTER TABLE `almacen`
  ADD CONSTRAINT `almacen_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `almacen_ibfk_2` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `bajas`
--
ALTER TABLE `bajas`
  ADD CONSTRAINT `bajas_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `fk_bajas_usuario` FOREIGN KEY (`id_user`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `baja_evidencias`
--
ALTER TABLE `baja_evidencias`
  ADD CONSTRAINT `baja_evidencias_ibfk_1` FOREIGN KEY (`id_baja`) REFERENCES `bajas` (`id_baja`);

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `equipos_ibfk_2` FOREIGN KEY (`id_sistema_operativo`) REFERENCES `sistema_operativo` (`id_so`),
  ADD CONSTRAINT `equipos_ibfk_3` FOREIGN KEY (`id_procesador`) REFERENCES `procesador` (`id_procesador`),
  ADD CONSTRAINT `equipos_ibfk_4` FOREIGN KEY (`id_memoria_ram`) REFERENCES `memoria_ram` (`id_memoria_ram`),
  ADD CONSTRAINT `equipos_ibfk_5` FOREIGN KEY (`id_tipo_disco_duro`) REFERENCES `tipo_disco_duro` (`id_disco`),
  ADD CONSTRAINT `equipos_ibfk_6` FOREIGN KEY (`id_almacenamiento`) REFERENCES `almacenamiento_interno` (`id_almacenamiento`),
  ADD CONSTRAINT `fk_equipo_usuario` FOREIGN KEY (`id_user`) REFERENCES `usuarios` (`id_user`),
  ADD CONSTRAINT `fk_marca` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id_marca`),
  ADD CONSTRAINT `fk_modelo` FOREIGN KEY (`id_modelo`) REFERENCES `modelo` (`id_modelo`),
  ADD CONSTRAINT `fk_tipo_equipo` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `tipo_equipo` (`id_tipo_equipo`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`id_hotel`) REFERENCES `hoteles` (`id_hotel`),
  ADD CONSTRAINT `inventario_ibfk_3` FOREIGN KEY (`id_estado`) REFERENCES `estado_equipo` (`id_estado`);

--
-- Filtros para la tabla `log_equipos`
--
ALTER TABLE `log_equipos`
  ADD CONSTRAINT `log_equipos_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `log_equipos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `modelo`
--
ALTER TABLE `modelo`
  ADD CONSTRAINT `fk_modelo_marca` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id_marca`),
  ADD CONSTRAINT `fk_modelo_tipo` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `tipo_equipo` (`id_tipo_equipo`);

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `fk_usuario_creador` FOREIGN KEY (`usuario_creador_id`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `prestamo_equipos`
--
ALTER TABLE `prestamo_equipos`
  ADD CONSTRAINT `fk_asignado_por` FOREIGN KEY (`asignado_por`) REFERENCES `usuarios` (`id_user`),
  ADD CONSTRAINT `prestamo_equipos_ibfk_1` FOREIGN KEY (`id_prestamo`) REFERENCES `prestamos` (`id_prestamo`),
  ADD CONSTRAINT `prestamo_equipos_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `prestamo_equipos_ibfk_3` FOREIGN KEY (`asignado_por`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `resguardos`
--
ALTER TABLE `resguardos`
  ADD CONSTRAINT `fk_usuario_creador_resguardo` FOREIGN KEY (`usuario_creador_id`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `resguardo_equipos`
--
ALTER TABLE `resguardo_equipos`
  ADD CONSTRAINT `resguardo_equipos_ibfk_1` FOREIGN KEY (`id_resguardo`) REFERENCES `resguardos` (`id_resguardo`),
  ADD CONSTRAINT `resguardo_equipos_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `resguardo_equipos_ibfk_3` FOREIGN KEY (`asignado_por`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `transferencias`
--
ALTER TABLE `transferencias`
  ADD CONSTRAINT `fk_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transferencias_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `transferencias_ibfk_2` FOREIGN KEY (`id_hotel_origen`) REFERENCES `hoteles` (`id_hotel`),
  ADD CONSTRAINT `transferencias_ibfk_3` FOREIGN KEY (`id_hotel_destino`) REFERENCES `hoteles` (`id_hotel`);

--
-- Filtros para la tabla `transferencias_resguardos`
--
ALTER TABLE `transferencias_resguardos`
  ADD CONSTRAINT `transferencias_resguardos_ibfk_1` FOREIGN KEY (`id_resguardo`) REFERENCES `resguardos` (`id_resguardo`),
  ADD CONSTRAINT `transferencias_resguardos_ibfk_2` FOREIGN KEY (`id_hotel_origen`) REFERENCES `hoteles` (`id_hotel`),
  ADD CONSTRAINT `transferencias_resguardos_ibfk_3` FOREIGN KEY (`id_hotel_destino`) REFERENCES `hoteles` (`id_hotel`),
  ADD CONSTRAINT `transferencias_resguardos_ibfk_4` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id_user`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `usuario_hoteles`
--
ALTER TABLE `usuario_hoteles`
  ADD CONSTRAINT `usuario_hoteles_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuarios` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_hoteles_ibfk_2` FOREIGN KEY (`id_hotel`) REFERENCES `hoteles` (`id_hotel`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
