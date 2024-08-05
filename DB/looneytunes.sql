-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-08-2024 a las 04:10:21
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
-- Base de datos: `looneytunes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_administradores`
--

CREATE TABLE `tab_administradores` (
  `ID_ADMINISTRADOR` int(11) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `NOMBRE_ADMIN` varchar(50) DEFAULT NULL,
  `APELLIDO_ADMIN` varchar(50) DEFAULT NULL,
  `CELULAR_ADMIN` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_administradores`
--

INSERT INTO `tab_administradores` (`ID_ADMINISTRADOR`, `ID_USUARIO`, `NOMBRE_ADMIN`, `APELLIDO_ADMIN`, `CELULAR_ADMIN`) VALUES
(3, 17, 'Carlos', 'Rosales', '0963060020');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_categorias`
--

CREATE TABLE `tab_categorias` (
  `ID_CATEGORIA` int(11) NOT NULL,
  `CATEGORIA` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_categorias`
--

INSERT INTO `tab_categorias` (`ID_CATEGORIA`, `CATEGORIA`) VALUES
(1, 'MOSQUITOS'),
(2, 'PRE MINI'),
(3, 'MINI DAMAS'),
(4, 'MINI HOMBRES'),
(5, 'U13 DAMAS'),
(6, 'U13 HOMBRES'),
(8, 'U15 HOMBRES'),
(10, 'U15 DAMAS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_deportistas`
--

CREATE TABLE `tab_deportistas` (
  `ID_DEPORTISTA` int(11) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `NOMBRE_DEPO` varchar(50) DEFAULT NULL,
  `APELLIDO_DEPO` varchar(50) DEFAULT NULL,
  `FECHA_NACIMIENTO` date DEFAULT NULL,
  `CEDULA_DEPO` varchar(10) DEFAULT NULL,
  `NUMERO_CELULAR` varchar(10) DEFAULT NULL,
  `GENERO` varchar(20) DEFAULT NULL,
  `ID_CATEGORIA` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_deportistas`
--

INSERT INTO `tab_deportistas` (`ID_DEPORTISTA`, `ID_USUARIO`, `NOMBRE_DEPO`, `APELLIDO_DEPO`, `FECHA_NACIMIENTO`, `CEDULA_DEPO`, `NUMERO_CELULAR`, `GENERO`, `ID_CATEGORIA`) VALUES
(8, 25, 'Samia', 'Delacruz', '2003-06-26', '1001001004', '0912365478', 'Femenino', 1),
(10, 27, 'Brandon', 'Alvarez', '2003-08-26', '1001001001', '0987654322', 'Masculino', 4),
(11, 28, 'Pablo', 'Chasi', '2002-03-06', '1001001002', '0912345678', 'Masculino', 8),
(12, 29, 'Luis', 'Andrade', '2003-03-26', '1001001003', '0912365748', 'Masculino', 1),
(16, 50, 'Francisco', 'Vilatuña', '2006-01-26', '1005415003', '0963060020', 'Masculino', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_detalles`
--

CREATE TABLE `tab_detalles` (
  `ID_DETALLE` int(11) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `NUMERO_CAMISA` varchar(2) DEFAULT NULL,
  `ALTURA` varchar(10) DEFAULT NULL,
  `PESO` varchar(10) DEFAULT NULL,
  `FECHA_INGRESO` date DEFAULT NULL,
  `ID_DEPORTISTA` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_detalles`
--

INSERT INTO `tab_detalles` (`ID_DETALLE`, `ID_USUARIO`, `NUMERO_CAMISA`, `ALTURA`, `PESO`, `FECHA_INGRESO`, `ID_DEPORTISTA`) VALUES
(18, 27, '47', '1.70', '70', '2024-07-01', 10),
(19, 28, '13', '75', '95', '2024-07-01', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_entrenadores`
--

CREATE TABLE `tab_entrenadores` (
  `ID_ENTRENADOR` int(11) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `NOMBRE_ENTRE` varchar(50) DEFAULT NULL,
  `APELLIDO_ENTRE` varchar(50) DEFAULT NULL,
  `EXPERIENCIA_ENTRE` varchar(10) DEFAULT NULL,
  `CELULAR_ENTRE` varchar(10) DEFAULT NULL,
  `CORREO_ENTRE` varchar(50) DEFAULT NULL,
  `DIRECCION_ENTRE` varchar(50) DEFAULT NULL,
  `CEDULA_ENTRE` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_entrenadores`
--

INSERT INTO `tab_entrenadores` (`ID_ENTRENADOR`, `ID_USUARIO`, `NOMBRE_ENTRE`, `APELLIDO_ENTRE`, `EXPERIENCIA_ENTRE`, `CELULAR_ENTRE`, `CORREO_ENTRE`, `DIRECCION_ENTRE`, `CEDULA_ENTRE`) VALUES
(2, 16, 'Santiago', 'Andrade', '6 meses', '0984657646', 'andradebrandon26@gmail.com', 'Ibarra', '1003447560'),
(6, 54, 'Christian', 'Andrade', '1 año', '0963060020', 'tustasgamer@gmail.com', 'Calle Roca Y Olmedo', '1005415003');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_entrenador_categoria`
--

CREATE TABLE `tab_entrenador_categoria` (
  `ID_ENTRENADOR` int(11) NOT NULL,
  `ID_CATEGORIA` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_entrenador_categoria`
--

INSERT INTO `tab_entrenador_categoria` (`ID_ENTRENADOR`, `ID_CATEGORIA`) VALUES
(2, 1),
(6, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_logs`
--

CREATE TABLE `tab_logs` (
  `ID_LOG` int(11) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `EVENTO` text DEFAULT NULL,
  `HORA_LOG` time DEFAULT NULL,
  `DIA_LOG` date DEFAULT NULL,
  `IP` varchar(20) DEFAULT NULL,
  `TIPO_EVENTO` enum('inicio_sesion','cierre_sesion','nuevo_usuario','subida_base_datos') DEFAULT 'inicio_sesion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_logs`
--

INSERT INTO `tab_logs` (`ID_LOG`, `ID_USUARIO`, `EVENTO`, `HORA_LOG`, `DIA_LOG`, `IP`, `TIPO_EVENTO`) VALUES
(261, 18, 'Cierre de sesión', '14:10:17', '2024-08-01', '::1', 'cierre_sesion'),
(262, 17, 'Se ha iniciado una nueva sesión', '14:10:19', '2024-08-01', '::1', 'inicio_sesion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_pagos`
--

CREATE TABLE `tab_pagos` (
  `ID_PAGO` int(11) NOT NULL,
  `ID_REPRESENTANTE` int(11) DEFAULT NULL,
  `ID_DEPORTISTA` int(11) DEFAULT NULL,
  `TIPO_PAGO` varchar(50) DEFAULT NULL,
  `MONTO` decimal(10,2) DEFAULT NULL,
  `FECHA` date DEFAULT NULL,
  `BANCO` varchar(100) DEFAULT NULL,
  `MOTIVO` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_pagos`
--

INSERT INTO `tab_pagos` (`ID_PAGO`, `ID_REPRESENTANTE`, `ID_DEPORTISTA`, `TIPO_PAGO`, `MONTO`, `FECHA`, `BANCO`, `MOTIVO`) VALUES
(2, 3, 10, 'transferencia', 78.00, '2024-07-11', 'Pichincha', 'asa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_productos`
--

CREATE TABLE `tab_productos` (
  `id_producto` int(20) NOT NULL,
  `producto_codigo` varchar(70) NOT NULL,
  `producto_nombre` varchar(70) NOT NULL,
  `producto_precio` decimal(30,2) NOT NULL,
  `producto_stock` int(25) NOT NULL,
  `producto_foto` varchar(500) NOT NULL,
  `id_categoria_producto` int(7) NOT NULL,
  `ID_USUARIO` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_producto_categoria`
--

CREATE TABLE `tab_producto_categoria` (
  `id_categoria_producto` int(7) NOT NULL,
  `categoria_nombre` varchar(50) NOT NULL,
  `categoria_ubicacion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_representantes`
--

CREATE TABLE `tab_representantes` (
  `ID_REPRESENTANTE` int(11) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `NOMBRE_REPRE` varchar(50) DEFAULT NULL,
  `APELLIDO_REPRE` varchar(50) DEFAULT NULL,
  `CELULAR_REPRE` varchar(10) DEFAULT NULL,
  `CORREO_REPRE` varchar(100) DEFAULT NULL,
  `DIRECCION_REPRE` varchar(100) DEFAULT NULL,
  `CEDULA_REPRE` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_representantes`
--

INSERT INTO `tab_representantes` (`ID_REPRESENTANTE`, `ID_USUARIO`, `NOMBRE_REPRE`, `APELLIDO_REPRE`, `CELULAR_REPRE`, `CORREO_REPRE`, `DIRECCION_REPRE`, `CEDULA_REPRE`) VALUES
(3, 18, 'Viviana', 'Alvarez', '0987654321', 'user_3@gmail.com', 'Pimampiro', '1002536181'),
(4, 40, 'Raquel', 'Andrade', '0963060020', 'santy_rosales2003@hotmail.com', 'La Victoria', '1005415003');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_representantes_deportistas`
--

CREATE TABLE `tab_representantes_deportistas` (
  `ID_DEPORTISTA` int(11) NOT NULL,
  `ID_REPRESENTANTE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_representantes_deportistas`
--

INSERT INTO `tab_representantes_deportistas` (`ID_DEPORTISTA`, `ID_REPRESENTANTE`) VALUES
(16, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_tareas_programadas`
--

CREATE TABLE `tab_tareas_programadas` (
  `ID_TAREA` int(11) NOT NULL,
  `ID_DEPORTISTA` int(11) NOT NULL,
  `FECHA_PROGRAMADA` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_temp_deportistas`
--

CREATE TABLE `tab_temp_deportistas` (
  `ID_TEMP_DEPORTISTA` int(11) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `ID_DEPORTISTA` int(11) NOT NULL,
  `NOMBRE_DEPO` varchar(50) DEFAULT NULL,
  `APELLIDO_DEPO` varchar(50) DEFAULT NULL,
  `FECHA_NACIMIENTO` date DEFAULT NULL,
  `CEDULA_DEPO` varchar(10) DEFAULT NULL,
  `NUMERO_CELULAR` varchar(10) DEFAULT NULL,
  `GENERO` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_temp_deportistas`
--

INSERT INTO `tab_temp_deportistas` (`ID_TEMP_DEPORTISTA`, `ID_USUARIO`, `ID_DEPORTISTA`, `NOMBRE_DEPO`, `APELLIDO_DEPO`, `FECHA_NACIMIENTO`, `CEDULA_DEPO`, `NUMERO_CELULAR`, `GENERO`) VALUES
(39, 16, 10, 'Brandon', 'Alvarez', '2003-08-26', '1001001001', '0987654322', 'Masculino'),
(40, 16, 11, 'Pablo', 'Chasi', '2002-03-06', '1001001002', '0912345678', 'Masculino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_tipo_usuario`
--

CREATE TABLE `tab_tipo_usuario` (
  `ID_TIPO` int(11) NOT NULL,
  `TIPO` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_tipo_usuario`
--

INSERT INTO `tab_tipo_usuario` (`ID_TIPO`, `TIPO`) VALUES
(1, 'Administrador'),
(2, 'Entrenador'),
(3, 'Representante'),
(4, 'Deportista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_usuarios`
--

CREATE TABLE `tab_usuarios` (
  `ID_USUARIO` int(11) NOT NULL,
  `USUARIO` varchar(20) DEFAULT NULL,
  `PASS` varchar(100) DEFAULT NULL,
  `intentos_fallidos` int(11) DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_usuarios`
--

INSERT INTO `tab_usuarios` (`ID_USUARIO`, `USUARIO`, `PASS`, `intentos_fallidos`, `bloqueado_hasta`) VALUES
(16, 'Santiago', '$2y$10$drUQ3gjrFF5PtnRVAkWGgeQbXtqZqF451Ilzl2IDL80Z0aHsh9L8C', 0, NULL),
(17, 'Carlos', '$2y$10$q/IbwCdYWpIeFPQpFamUtuzRyX9sqe8eMPQfiP.MN06OV.zxzpyxu', 0, NULL),
(18, 'Viviana', '$2y$10$P2FXS9k8pp00fhFN8qEtg.0RkvMXforjqfrdAonnIlE.MdDN7NEb6', 0, NULL),
(25, 'Samia', '$2y$10$2.Ex7qBAmjTyMxrxbKtQJuUHElXIaBDroT5DJBrwb4LZBAIyel/qm', 0, NULL),
(27, 'Brandon', '$2y$10$zuA7jncJXXMeFMzmb/XUROEIg8dHJe4igng4mWeSja12DFZOC4rzG', 0, NULL),
(28, 'Pablo', '$2y$10$tsyl.IF1cagtsdNELZEhOOgmNM4/Lv7akbOVUqd5JZ04imKNWDyEi', 0, NULL),
(29, 'Luis', '$2y$10$W.YAEcunEQqWJJqTyLa8zOh9429IExWPMDs40iU40QKDSSeZKmmvq', 0, NULL),
(39, 'Carlos', '$2y$10$ttXulishggwe2v.fz.4EPOkUw8logHb7Wmo0J4ian89FM688uiWUG', 0, NULL),
(40, 'Raquel', '$2y$10$EhWPbmIm70wlbIjNCxbnmeelozAQVV2hguti/EB3.bsjf0noOkW1e', 0, NULL),
(50, 'Francisco', '$2y$10$Y6QBinOgrECiNVyDDX.pUufzLqlRA53bkBLAg8PLZerNZgyrkbBUi', 0, NULL),
(54, 'Christian', '$2y$10$TceF7SOJPbNHyWIugR6t8.m2CshOc/IRBPmZ7zLU99a.YcFEXR5a2', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_usu_tipo`
--

CREATE TABLE `tab_usu_tipo` (
  `ID_USU_TIPO` int(11) NOT NULL,
  `ID_TIPO` int(11) DEFAULT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_usu_tipo`
--

INSERT INTO `tab_usu_tipo` (`ID_USU_TIPO`, `ID_TIPO`, `ID_USUARIO`) VALUES
(16, 2, 16),
(17, 1, 17),
(18, 3, 18),
(25, 4, 25),
(27, 4, 27),
(28, 4, 28),
(29, 4, 29),
(39, 1, 39),
(40, 3, 40),
(50, 4, 50),
(54, 2, 54);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tab_administradores`
--
ALTER TABLE `tab_administradores`
  ADD PRIMARY KEY (`ID_ADMINISTRADOR`),
  ADD KEY `FK_REFERENCE_15` (`ID_USUARIO`);

--
-- Indices de la tabla `tab_categorias`
--
ALTER TABLE `tab_categorias`
  ADD PRIMARY KEY (`ID_CATEGORIA`);

--
-- Indices de la tabla `tab_deportistas`
--
ALTER TABLE `tab_deportistas`
  ADD PRIMARY KEY (`ID_DEPORTISTA`),
  ADD KEY `FK_REFERENCE_12` (`ID_USUARIO`),
  ADD KEY `ID_CATEGORIA` (`ID_CATEGORIA`);

--
-- Indices de la tabla `tab_detalles`
--
ALTER TABLE `tab_detalles`
  ADD PRIMARY KEY (`ID_DETALLE`),
  ADD KEY `FK_REFERENCE_9` (`ID_USUARIO`),
  ADD KEY `ID_DEPORTISTA` (`ID_DEPORTISTA`);

--
-- Indices de la tabla `tab_entrenadores`
--
ALTER TABLE `tab_entrenadores`
  ADD PRIMARY KEY (`ID_ENTRENADOR`),
  ADD KEY `FK_REFERENCE_8` (`ID_USUARIO`);

--
-- Indices de la tabla `tab_entrenador_categoria`
--
ALTER TABLE `tab_entrenador_categoria`
  ADD PRIMARY KEY (`ID_ENTRENADOR`,`ID_CATEGORIA`),
  ADD KEY `ID_CATEGORIA` (`ID_CATEGORIA`);

--
-- Indices de la tabla `tab_logs`
--
ALTER TABLE `tab_logs`
  ADD PRIMARY KEY (`ID_LOG`),
  ADD KEY `FK_REFERENCE_14` (`ID_USUARIO`);

--
-- Indices de la tabla `tab_pagos`
--
ALTER TABLE `tab_pagos`
  ADD PRIMARY KEY (`ID_PAGO`),
  ADD KEY `ID_REPRESENTANTE` (`ID_REPRESENTANTE`),
  ADD KEY `ID_DEPORTISTA` (`ID_DEPORTISTA`);

--
-- Indices de la tabla `tab_productos`
--
ALTER TABLE `tab_productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria_producto` (`id_categoria_producto`),
  ADD KEY `ID_USUARIO` (`ID_USUARIO`);

--
-- Indices de la tabla `tab_producto_categoria`
--
ALTER TABLE `tab_producto_categoria`
  ADD PRIMARY KEY (`id_categoria_producto`);

--
-- Indices de la tabla `tab_representantes`
--
ALTER TABLE `tab_representantes`
  ADD PRIMARY KEY (`ID_REPRESENTANTE`),
  ADD KEY `FK_REFERENCE_10` (`ID_USUARIO`);

--
-- Indices de la tabla `tab_representantes_deportistas`
--
ALTER TABLE `tab_representantes_deportistas`
  ADD PRIMARY KEY (`ID_DEPORTISTA`,`ID_REPRESENTANTE`),
  ADD KEY `ID_REPRESENTANTE` (`ID_REPRESENTANTE`);

--
-- Indices de la tabla `tab_tareas_programadas`
--
ALTER TABLE `tab_tareas_programadas`
  ADD PRIMARY KEY (`ID_TAREA`);

--
-- Indices de la tabla `tab_temp_deportistas`
--
ALTER TABLE `tab_temp_deportistas`
  ADD PRIMARY KEY (`ID_TEMP_DEPORTISTA`),
  ADD KEY `ID_USUARIO` (`ID_USUARIO`),
  ADD KEY `ref1` (`ID_DEPORTISTA`);

--
-- Indices de la tabla `tab_tipo_usuario`
--
ALTER TABLE `tab_tipo_usuario`
  ADD PRIMARY KEY (`ID_TIPO`);

--
-- Indices de la tabla `tab_usuarios`
--
ALTER TABLE `tab_usuarios`
  ADD PRIMARY KEY (`ID_USUARIO`);

--
-- Indices de la tabla `tab_usu_tipo`
--
ALTER TABLE `tab_usu_tipo`
  ADD PRIMARY KEY (`ID_USU_TIPO`),
  ADD KEY `FK_REFERENCE_17` (`ID_TIPO`),
  ADD KEY `FK_REFERENCE_18` (`ID_USUARIO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tab_administradores`
--
ALTER TABLE `tab_administradores`
  MODIFY `ID_ADMINISTRADOR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tab_categorias`
--
ALTER TABLE `tab_categorias`
  MODIFY `ID_CATEGORIA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tab_deportistas`
--
ALTER TABLE `tab_deportistas`
  MODIFY `ID_DEPORTISTA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tab_detalles`
--
ALTER TABLE `tab_detalles`
  MODIFY `ID_DETALLE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tab_entrenadores`
--
ALTER TABLE `tab_entrenadores`
  MODIFY `ID_ENTRENADOR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tab_logs`
--
ALTER TABLE `tab_logs`
  MODIFY `ID_LOG` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;

--
-- AUTO_INCREMENT de la tabla `tab_pagos`
--
ALTER TABLE `tab_pagos`
  MODIFY `ID_PAGO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tab_productos`
--
ALTER TABLE `tab_productos`
  MODIFY `id_producto` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tab_producto_categoria`
--
ALTER TABLE `tab_producto_categoria`
  MODIFY `id_categoria_producto` int(7) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tab_representantes`
--
ALTER TABLE `tab_representantes`
  MODIFY `ID_REPRESENTANTE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tab_tareas_programadas`
--
ALTER TABLE `tab_tareas_programadas`
  MODIFY `ID_TAREA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tab_temp_deportistas`
--
ALTER TABLE `tab_temp_deportistas`
  MODIFY `ID_TEMP_DEPORTISTA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `tab_tipo_usuario`
--
ALTER TABLE `tab_tipo_usuario`
  MODIFY `ID_TIPO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tab_usuarios`
--
ALTER TABLE `tab_usuarios`
  MODIFY `ID_USUARIO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `tab_usu_tipo`
--
ALTER TABLE `tab_usu_tipo`
  MODIFY `ID_USU_TIPO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tab_administradores`
--
ALTER TABLE `tab_administradores`
  ADD CONSTRAINT `FK_REFERENCE_15` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);

--
-- Filtros para la tabla `tab_deportistas`
--
ALTER TABLE `tab_deportistas`
  ADD CONSTRAINT `FK_REFERENCE_12` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`),
  ADD CONSTRAINT `tab_deportistas_ibfk_1` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `tab_categorias` (`ID_CATEGORIA`);

--
-- Filtros para la tabla `tab_detalles`
--
ALTER TABLE `tab_detalles`
  ADD CONSTRAINT `FK_REFERENCE_9` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`),
  ADD CONSTRAINT `ID_DEPORTISTA` FOREIGN KEY (`ID_DEPORTISTA`) REFERENCES `tab_deportistas` (`ID_DEPORTISTA`);

--
-- Filtros para la tabla `tab_entrenadores`
--
ALTER TABLE `tab_entrenadores`
  ADD CONSTRAINT `FK_REFERENCE_8` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);

--
-- Filtros para la tabla `tab_entrenador_categoria`
--
ALTER TABLE `tab_entrenador_categoria`
  ADD CONSTRAINT `tab_entrenador_categoria_ibfk_1` FOREIGN KEY (`ID_ENTRENADOR`) REFERENCES `tab_entrenadores` (`ID_ENTRENADOR`),
  ADD CONSTRAINT `tab_entrenador_categoria_ibfk_2` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `tab_categorias` (`ID_CATEGORIA`);

--
-- Filtros para la tabla `tab_logs`
--
ALTER TABLE `tab_logs`
  ADD CONSTRAINT `FK_REFERENCE_14` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);

--
-- Filtros para la tabla `tab_pagos`
--
ALTER TABLE `tab_pagos`
  ADD CONSTRAINT `tab_pagos_ibfk_1` FOREIGN KEY (`ID_REPRESENTANTE`) REFERENCES `tab_representantes` (`ID_REPRESENTANTE`),
  ADD CONSTRAINT `tab_pagos_ibfk_2` FOREIGN KEY (`ID_DEPORTISTA`) REFERENCES `tab_deportistas` (`ID_DEPORTISTA`);

--
-- Filtros para la tabla `tab_productos`
--
ALTER TABLE `tab_productos`
  ADD CONSTRAINT `tab_productos_ibfk_1` FOREIGN KEY (`id_categoria_producto`) REFERENCES `tab_producto_categoria` (`id_categoria_producto`),
  ADD CONSTRAINT `tab_productos_ibfk_2` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);

--
-- Filtros para la tabla `tab_representantes`
--
ALTER TABLE `tab_representantes`
  ADD CONSTRAINT `FK_REFERENCE_10` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);

--
-- Filtros para la tabla `tab_representantes_deportistas`
--
ALTER TABLE `tab_representantes_deportistas`
  ADD CONSTRAINT `tab_representantes_deportistas_ibfk_1` FOREIGN KEY (`ID_DEPORTISTA`) REFERENCES `tab_deportistas` (`ID_DEPORTISTA`) ON DELETE CASCADE,
  ADD CONSTRAINT `tab_representantes_deportistas_ibfk_2` FOREIGN KEY (`ID_REPRESENTANTE`) REFERENCES `tab_representantes` (`ID_REPRESENTANTE`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tab_temp_deportistas`
--
ALTER TABLE `tab_temp_deportistas`
  ADD CONSTRAINT `ref1` FOREIGN KEY (`ID_DEPORTISTA`) REFERENCES `tab_deportistas` (`ID_DEPORTISTA`),
  ADD CONSTRAINT `tab_temp_deportistas_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tab_usu_tipo`
--
ALTER TABLE `tab_usu_tipo`
  ADD CONSTRAINT `FK_REFERENCE_17` FOREIGN KEY (`ID_TIPO`) REFERENCES `tab_tipo_usuario` (`ID_TIPO`),
  ADD CONSTRAINT `FK_REFERENCE_18` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
