-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-08-2024 a las 22:14:21
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
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_administradores`
--

CREATE TABLE `tab_administradores` (
  `ID_ADMINISTRADOR` int(11) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `NOMBRE_ADMIN` varchar(50) DEFAULT NULL,
  `APELLIDO_ADMIN` varchar(50) DEFAULT NULL,
  `CELULAR_ADMIN` varchar(10) DEFAULT NULL,
  `status` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_administradores`
--

INSERT INTO `tab_administradores` (`ID_ADMINISTRADOR`, `ID_USUARIO`, `NOMBRE_ADMIN`, `APELLIDO_ADMIN`, `CELULAR_ADMIN`, `status`) VALUES
(3, 17, 'Carlos', 'Rosales', '0984657646', 'activo'),
(5, 56, 'Santiago', 'Rosales', '0963060020', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_bancos`
--

CREATE TABLE `tab_bancos` (
  `ID_BANCO` int(11) NOT NULL,
  `NOMBRE` varchar(100) NOT NULL,
  `ESTADO` enum('activo','inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_bancos`
--

INSERT INTO `tab_bancos` (`ID_BANCO`, `NOMBRE`, `ESTADO`) VALUES
(0, 'Efectivo', 'inactivo'),
(1, 'pichincha', 'activo'),
(2, 'ProduBanco', 'activo'),
(3, 'Banco Austro', 'activo'),
(4, 'Banco Pacifico', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_categorias`
--

CREATE TABLE `tab_categorias` (
  `ID_CATEGORIA` int(11) NOT NULL,
  `CATEGORIA` varchar(30) NOT NULL,
  `LIMITE_DEPORTISTAS` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_categorias`
--

INSERT INTO `tab_categorias` (`ID_CATEGORIA`, `CATEGORIA`, `LIMITE_DEPORTISTAS`) VALUES
(1, 'MOSQUITOS', 5),
(2, 'PRE MINI', 2),
(3, 'MINI DAMAS', 5),
(4, 'MINI HOMBRES', 5),
(5, 'U13 DAMAS', NULL),
(6, 'U13 HOMBRES', NULL),
(8, 'U15 HOMBRES', NULL),
(12, 'U15 DAMAS', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_categoria_deportista`
--

CREATE TABLE `tab_categoria_deportista` (
  `ID_CATEGORIA` int(11) NOT NULL,
  `ID_DEPORTISTA` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_categoria_deportista`
--

INSERT INTO `tab_categoria_deportista` (`ID_CATEGORIA`, `ID_DEPORTISTA`) VALUES
(1, 10),
(1, 12),
(2, 8),
(8, 11),
(8, 16);

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
  `status` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_deportistas`
--

INSERT INTO `tab_deportistas` (`ID_DEPORTISTA`, `ID_USUARIO`, `NOMBRE_DEPO`, `APELLIDO_DEPO`, `FECHA_NACIMIENTO`, `CEDULA_DEPO`, `NUMERO_CELULAR`, `GENERO`, `status`) VALUES
(8, 25, 'Samia', 'Delacruz', '2003-06-26', '1001001004', '0912365478', 'Femenino', 'activo'),
(10, 27, 'Brandon', 'Alvarez', '2003-08-26', '1001001001', '0987654322', 'Masculino', 'activo'),
(11, 28, 'Pablo', 'Chasi', '2002-03-06', '1001001002', '0912345678', 'Masculino', 'activo'),
(12, 29, 'Luis', 'Andrade', '2003-03-26', '1001001003', '0912365748', 'Masculino', 'activo'),
(16, 50, 'Francisco', 'Vilatuña', '2006-01-26', '1005415003', '0963060020', 'Masculino', 'activo');

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
(18, 27, '47', '170', '70', '2024-07-01', 10),
(19, 28, '13', '75', '95', '2024-07-01', 11),
(20, 16, '13', '170', '50', '2024-08-13', 12),
(21, 16, '13', '172', '55', '2024-09-14', 12);

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
  `CEDULA_ENTRE` varchar(10) DEFAULT NULL,
  `status` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_entrenadores`
--

INSERT INTO `tab_entrenadores` (`ID_ENTRENADOR`, `ID_USUARIO`, `NOMBRE_ENTRE`, `APELLIDO_ENTRE`, `EXPERIENCIA_ENTRE`, `CELULAR_ENTRE`, `CORREO_ENTRE`, `DIRECCION_ENTRE`, `CEDULA_ENTRE`, `status`) VALUES
(2, 16, 'Santiago', 'Andrade', '6 meses', '0984657646', 'andradebrandon26@gmail.com', 'Ibarra', '1003447560', 'activo'),
(6, 54, 'Christian', 'Andrade', '2 años', '0963060020', 'tustasgamer@gmail.com', 'Calle Roca Y Olmedo', '1005415003', 'activo');

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
(2, 2),
(6, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_informes`
--

CREATE TABLE `tab_informes` (
  `id_informe` int(11) NOT NULL,
  `id_deportista` int(11) DEFAULT NULL,
  `id_representante` int(11) DEFAULT NULL,
  `id_entrenador` int(11) DEFAULT NULL,
  `informe` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_informes`
--

INSERT INTO `tab_informes` (`id_informe`, `id_deportista`, `id_representante`, `id_entrenador`, `informe`, `fecha_creacion`) VALUES
(1, 12, 4, 2, 'Mal uniformado', '2024-08-14 01:33:13'),
(3, 10, 3, 2, 'Todo correcto', '2024-08-14 02:50:29');

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
  `TIPO_EVENTO` enum('inicio_sesion','cierre_sesion','nuevo_usuario','subida_base_datos','nuevo_producto_creado','nueva_categoria_producto_creado','nueva_categoria_deportista_creado','nuevo_informe_enviado','nuevo_pago_agregado','nuevo_limite_categoria_deportistas_definido','usuario_inactivo','usuario_activo','actualizacion_perfil,','reporte_pagos') DEFAULT 'inicio_sesion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_logs`
--

INSERT INTO `tab_logs` (`ID_LOG`, `ID_USUARIO`, `EVENTO`, `HORA_LOG`, `DIA_LOG`, `IP`, `TIPO_EVENTO`) VALUES
(279, 17, 'Cierre de sesión', '10:08:36', '2024-08-07', '::1', 'cierre_sesion'),
(280, 17, 'Se ha iniciado una nueva sesión', '10:08:38', '2024-08-07', '::1', 'inicio_sesion'),
(281, 17, 'Se ha iniciado una nueva sesión', '14:27:09', '2024-08-07', '::1', 'inicio_sesion'),
(282, 17, 'Se ha iniciado una nueva sesión', '10:10:03', '2024-08-08', '::1', 'inicio_sesion'),
(283, 17, 'Cierre de sesión', '10:52:24', '2024-08-08', '::1', 'cierre_sesion'),
(284, 17, 'Se ha iniciado una nueva sesión', '18:41:35', '2024-08-08', '::1', 'inicio_sesion'),
(285, 17, 'Cierre de sesión', '18:41:56', '2024-08-08', '::1', 'cierre_sesion'),
(286, 18, 'Se ha iniciado una nueva sesión', '18:42:21', '2024-08-08', '::1', 'inicio_sesion'),
(287, 50, 'Se ha iniciado una nueva sesión', '18:47:56', '2024-08-08', '::1', 'inicio_sesion'),
(288, 50, 'Cierre de sesión', '18:48:19', '2024-08-08', '::1', 'cierre_sesion'),
(289, 29, 'Se ha iniciado una nueva sesión', '18:48:36', '2024-08-08', '::1', 'inicio_sesion'),
(290, 29, 'Cierre de sesión', '18:48:42', '2024-08-08', '::1', 'cierre_sesion'),
(291, 16, 'Se ha iniciado una nueva sesión', '18:49:05', '2024-08-08', '::1', 'inicio_sesion'),
(292, 18, 'Se ha iniciado una nueva sesión', '19:12:55', '2024-08-08', '::1', 'inicio_sesion'),
(293, 18, 'Se ha iniciado una nueva sesión', '19:19:48', '2024-08-08', '::1', 'inicio_sesion'),
(294, 18, 'Cierre de sesión', '20:06:25', '2024-08-08', '::1', 'cierre_sesion'),
(295, 40, 'Se ha iniciado una nueva sesión', '20:06:52', '2024-08-08', '::1', 'inicio_sesion'),
(296, 40, 'Cierre de sesión', '21:12:48', '2024-08-08', '::1', 'cierre_sesion'),
(297, 18, 'Se ha iniciado una nueva sesión', '21:13:09', '2024-08-08', '::1', 'inicio_sesion'),
(298, 18, 'Cierre de sesión', '21:16:42', '2024-08-08', '::1', 'cierre_sesion'),
(299, 16, 'Se ha iniciado una nueva sesión', '21:17:06', '2024-08-08', '::1', 'inicio_sesion'),
(300, 16, 'Cierre de sesión', '21:18:28', '2024-08-08', '::1', 'cierre_sesion'),
(301, 18, 'Se ha iniciado una nueva sesión', '21:18:42', '2024-08-08', '::1', 'inicio_sesion'),
(302, 18, 'Cierre de sesión', '21:43:08', '2024-08-08', '::1', 'cierre_sesion'),
(303, 18, 'Se ha iniciado una nueva sesión', '21:43:38', '2024-08-08', '::1', 'inicio_sesion'),
(304, 18, 'Cierre de sesión', '21:43:57', '2024-08-08', '::1', 'cierre_sesion'),
(305, 40, 'Se ha iniciado una nueva sesión', '21:44:09', '2024-08-08', '::1', 'inicio_sesion'),
(306, 40, 'Cierre de sesión', '21:44:24', '2024-08-08', '::1', 'cierre_sesion'),
(307, 40, 'Se ha iniciado una nueva sesión', '21:45:33', '2024-08-08', '::1', 'inicio_sesion'),
(308, 17, 'Se ha iniciado una nueva sesión', '22:51:02', '2024-08-11', '::1', 'inicio_sesion'),
(309, 17, 'Cierre de sesión', '17:08:21', '2024-08-13', '::1', 'cierre_sesion'),
(310, 17, 'Se ha iniciado una nueva sesión', '17:08:47', '2024-08-13', '::1', 'inicio_sesion'),
(311, 17, 'Cierre de sesión', '17:16:51', '2024-08-13', '::1', 'cierre_sesion'),
(312, 16, 'Se ha iniciado una nueva sesión', '17:16:54', '2024-08-13', '::1', 'inicio_sesion'),
(313, 16, 'Cierre de sesión', '17:17:11', '2024-08-13', '::1', 'cierre_sesion'),
(314, 18, 'Se ha iniciado una nueva sesión', '17:17:14', '2024-08-13', '::1', 'inicio_sesion'),
(315, 18, 'Cierre de sesión', '17:17:32', '2024-08-13', '::1', 'cierre_sesion'),
(316, 50, 'Se ha iniciado una nueva sesión', '17:17:36', '2024-08-13', '::1', 'inicio_sesion'),
(317, 50, 'Cierre de sesión', '17:17:45', '2024-08-13', '::1', 'cierre_sesion'),
(318, 17, 'Se ha iniciado una nueva sesión', '17:17:56', '2024-08-13', '::1', 'inicio_sesion'),
(319, 17, 'Cierre de sesión', '23:28:25', '2024-08-14', '::1', 'cierre_sesion'),
(320, 17, 'Se ha iniciado una nueva sesión', '23:32:09', '2024-08-14', '::1', 'inicio_sesion'),
(321, 17, 'Cierre de sesión', '23:34:57', '2024-08-14', '::1', 'cierre_sesion'),
(322, 17, 'Se ha iniciado una nueva sesión', '23:35:02', '2024-08-14', '::1', 'inicio_sesion'),
(323, 17, 'Cierre de sesión', '22:22:29', '2024-08-16', '127.0.0.1', 'cierre_sesion'),
(324, 17, 'Se ha iniciado una nueva sesión', '01:05:42', '2024-08-17', '::1', 'inicio_sesion'),
(326, 17, 'Cierre de sesión', '10:16:42', '2024-08-18', '::1', 'cierre_sesion'),
(327, 16, 'Se ha iniciado una nueva sesión', '10:16:55', '2024-08-18', '::1', 'inicio_sesion'),
(328, 16, 'Cierre de sesión', '10:16:57', '2024-08-18', '::1', 'cierre_sesion'),
(329, 16, 'Se ha iniciado una nueva sesión', '10:17:21', '2024-08-18', '::1', 'inicio_sesion'),
(330, 16, 'Cierre de sesión', '10:17:23', '2024-08-18', '::1', 'cierre_sesion'),
(331, 16, 'Se ha iniciado una nueva sesión', '10:55:22', '2024-08-18', '::1', 'inicio_sesion'),
(332, 16, 'Cierre de sesión', '10:55:24', '2024-08-18', '::1', 'cierre_sesion'),
(333, 17, 'Se ha iniciado una nueva sesión', '10:55:26', '2024-08-18', '::1', 'inicio_sesion'),
(334, 17, 'Cierre de sesión', '10:55:45', '2024-08-18', '::1', 'cierre_sesion'),
(335, 17, 'Se ha iniciado una nueva sesión', '11:08:28', '2024-08-18', '::1', 'inicio_sesion'),
(336, 17, 'Registro de nuevo administrador: Santiago Rosales', '11:09:31', '2024-08-18', '::1', 'nuevo_usuario'),
(337, 56, 'Se ha iniciado una nueva sesión', '11:11:15', '2024-08-18', '::1', 'inicio_sesion'),
(338, 17, 'Cierre de sesión', '11:15:28', '2024-08-18', '::1', 'cierre_sesion'),
(339, 56, 'Cierre de sesión', '11:15:32', '2024-08-18', '::1', 'cierre_sesion'),
(340, 17, 'Se ha iniciado una nueva sesión', '11:15:34', '2024-08-18', '::1', 'inicio_sesion'),
(341, 56, 'Se ha iniciado una nueva sesión', '11:20:00', '2024-08-18', '::1', 'inicio_sesion'),
(342, 56, 'Cierre de sesión', '11:22:20', '2024-08-18', '::1', 'cierre_sesion'),
(343, 56, 'Se ha iniciado una nueva sesión', '11:22:26', '2024-08-18', '::1', 'inicio_sesion'),
(344, 56, 'Cierre de sesión', '11:23:24', '2024-08-18', '::1', 'cierre_sesion'),
(345, 16, 'Se ha iniciado una nueva sesión', '11:24:19', '2024-08-18', '::1', 'inicio_sesion'),
(346, 16, 'Cierre de sesión', '11:24:27', '2024-08-18', '::1', 'cierre_sesion'),
(347, 16, 'Se ha iniciado una nueva sesión', '11:24:40', '2024-08-18', '::1', 'inicio_sesion'),
(348, 16, 'Cierre de sesión', '11:24:47', '2024-08-18', '::1', 'cierre_sesion'),
(349, NULL, 'Producto actualizado: Balon', '21:17:41', '2024-08-18', '::1', 'nuevo_producto_creado'),
(350, 17, 'Producto actualizado: Balon', '21:18:58', '2024-08-18', '::1', 'nuevo_producto_creado'),
(351, 17, 'Producto actualizado: Balon', '21:19:05', '2024-08-18', '::1', 'nuevo_producto_creado'),
(352, 17, 'Producto actualizado: Balon', '21:19:10', '2024-08-18', '::1', 'nuevo_producto_creado'),
(353, 17, 'Producto actualizado: Balon', '21:19:23', '2024-08-18', '::1', 'nuevo_producto_creado'),
(354, 17, 'Actualización de información del deportista: Samia Delacruz', '21:47:52', '2024-08-18', '::1', 'actualizacion_perfil'),
(355, 17, 'Actualización de información del deportista: Samia Delacruz', '21:47:56', '2024-08-18', '::1', 'actualizacion_perfil'),
(356, 17, 'Actualización de información del deportista: Samia Delacruz', '21:49:32', '2024-08-18', '::1', 'actualizacion_perfil'),
(357, 17, 'Actualización de información del deportista: Samia Delacruz', '21:50:13', '2024-08-18', '::1', 'actualizacion_perfil'),
(358, 17, 'Actualización de información del deportista: Samia Delacruz', '21:50:21', '2024-08-18', '::1', 'actualizacion_perfil'),
(359, 17, 'Actualización de información del deportista: Christian Andrade', '22:29:35', '2024-08-18', '::1', 'actualizacion_perfil'),
(360, 17, 'Actualización de información del administrador: Santiago Rosales', '22:29:53', '2024-08-18', '::1', 'actualizacion_perfil'),
(361, 17, 'Cierre de sesión', '15:44:23', '2024-08-18', '::1', 'cierre_sesion'),
(362, 17, 'Se ha iniciado una nueva sesión', '15:44:25', '2024-08-18', '::1', 'inicio_sesion'),
(363, 17, 'Actualización de información del administrador: Santiago Andrade', '17:44:17', '2024-08-19', '::1', 'actualizacion_perfil'),
(364, 17, 'Actualización de información del administrador: Santiago Rosales', '17:44:23', '2024-08-19', '::1', 'actualizacion_perfil'),
(365, 54, 'Se ha iniciado una nueva sesión desde la IP: ::1', '14:36:46', '2024-08-21', '::1', 'inicio_sesion'),
(366, 54, 'Cierre de sesión', '14:38:06', '2024-08-21', '::1', 'cierre_sesion'),
(367, 17, 'Se ha iniciado una nueva sesión desde la IP: ::1', '14:38:08', '2024-08-21', '::1', 'inicio_sesion'),
(368, 17, 'Cierre de sesión', '14:45:26', '2024-08-21', '::1', 'cierre_sesion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_pagos`
--

CREATE TABLE `tab_pagos` (
  `ID_PAGO` int(11) NOT NULL,
  `ID_REPRESENTANTE` int(11) DEFAULT NULL,
  `ID_DEPORTISTA` int(11) DEFAULT NULL,
  `ID_BANCO` int(11) NOT NULL,
  `METODO_PAGO` varchar(50) DEFAULT NULL,
  `MONTO` decimal(10,2) DEFAULT NULL,
  `FECHA_PAGO` date DEFAULT NULL,
  `MOTIVO` text DEFAULT NULL,
  `NOMBRE_ARCHIVO` varchar(50) NOT NULL,
  `ENTIDAD_ORIGEN` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_pagos`
--

INSERT INTO `tab_pagos` (`ID_PAGO`, `ID_REPRESENTANTE`, `ID_DEPORTISTA`, `ID_BANCO`, `METODO_PAGO`, `MONTO`, `FECHA_PAGO`, `MOTIVO`, `NOMBRE_ARCHIVO`, `ENTIDAD_ORIGEN`) VALUES
(56, 4, 12, 0, 'efectivo', 123456.00, '2024-08-08', 'Pago del mes de Agosto ', '', ''),
(57, 4, 16, 2, 'transferencia', 123456.00, '2024-08-08', 'Pago del mes de Agosto ', 'Acta de recepcion de producto.docx', 'lalalalallala'),
(70, 4, 16, 0, 'efectivo', 51.00, '2024-08-12', 'Pago del mes de Agosto ', '', ''),
(71, 4, 16, 0, 'efectivo', 15.00, '2024-08-13', 'Pago del mes de Agosto ', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_pdfs`
--

CREATE TABLE `tab_pdfs` (
  `id_pdf` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_pdfs`
--

INSERT INTO `tab_pdfs` (`id_pdf`, `id_usuario`, `file_name`, `file_path`, `uploaded_at`) VALUES
(13, 16, '16_cv.pdf', '/looneytunes/entrenador/pdfs/16_cv.pdf', '2024-08-20 22:03:09');

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

--
-- Volcado de datos para la tabla `tab_productos`
--

INSERT INTO `tab_productos` (`id_producto`, `producto_codigo`, `producto_nombre`, `producto_precio`, `producto_stock`, `producto_foto`, `id_categoria_producto`, `ID_USUARIO`) VALUES
(1, '1234', 'Balon', 15.00, 15, '', 1, 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tab_producto_categoria`
--

CREATE TABLE `tab_producto_categoria` (
  `id_categoria_producto` int(7) NOT NULL,
  `categoria_nombre` varchar(50) NOT NULL,
  `categoria_ubicacion` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_producto_categoria`
--

INSERT INTO `tab_producto_categoria` (`id_categoria_producto`, `categoria_nombre`, `categoria_ubicacion`) VALUES
(1, 'Balones', 'Ibarra'),
(2, 'Indumentaria', 'Otavalo');

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
  `CEDULA_REPRE` varchar(10) DEFAULT NULL,
  `status` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_representantes`
--

INSERT INTO `tab_representantes` (`ID_REPRESENTANTE`, `ID_USUARIO`, `NOMBRE_REPRE`, `APELLIDO_REPRE`, `CELULAR_REPRE`, `CORREO_REPRE`, `DIRECCION_REPRE`, `CEDULA_REPRE`, `status`) VALUES
(3, 18, 'Viviana', 'Alvarez', '0987654321', 'user_3@gmail.com', 'Pimampiro', '1002536181', 'activo'),
(4, 40, 'Raquel', 'Andrade', '0963060020', 'santy_rosales2003@hotmail.com', 'La victoria', '1005415003', 'activo');

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
(8, 3),
(10, 3),
(12, 4),
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
  `bloqueado_hasta` datetime DEFAULT NULL,
  `status` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_exp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tab_usuarios`
--

INSERT INTO `tab_usuarios` (`ID_USUARIO`, `USUARIO`, `PASS`, `intentos_fallidos`, `bloqueado_hasta`, `status`, `reset_token`, `reset_token_exp`) VALUES
(16, 'Santiago', '$2y$10$drUQ3gjrFF5PtnRVAkWGgeQbXtqZqF451Ilzl2IDL80Z0aHsh9L8C', 0, NULL, 'activo', NULL, NULL),
(17, 'Carlos', '$2y$10$q/IbwCdYWpIeFPQpFamUtuzRyX9sqe8eMPQfiP.MN06OV.zxzpyxu', 0, NULL, 'activo', NULL, NULL),
(18, 'Viviana', '$2y$10$P2FXS9k8pp00fhFN8qEtg.0RkvMXforjqfrdAonnIlE.MdDN7NEb6', 0, NULL, 'activo', NULL, NULL),
(25, 'Samia', '$2y$10$2.Ex7qBAmjTyMxrxbKtQJuUHElXIaBDroT5DJBrwb4LZBAIyel/qm', 0, NULL, 'activo', NULL, NULL),
(27, 'Brandon', '$2y$10$zuA7jncJXXMeFMzmb/XUROEIg8dHJe4igng4mWeSja12DFZOC4rzG', 0, NULL, 'activo', NULL, NULL),
(28, 'Pablo', '$2y$10$tsyl.IF1cagtsdNELZEhOOgmNM4/Lv7akbOVUqd5JZ04imKNWDyEi', 0, NULL, 'activo', NULL, NULL),
(29, 'Luis', '$2y$10$W.YAEcunEQqWJJqTyLa8zOh9429IExWPMDs40iU40QKDSSeZKmmvq', 0, NULL, 'activo', NULL, NULL),
(39, 'Carlos', '$2y$10$ttXulishggwe2v.fz.4EPOkUw8logHb7Wmo0J4ian89FM688uiWUG', 0, NULL, 'activo', NULL, NULL),
(40, 'Raquel', '$2y$10$EhWPbmIm70wlbIjNCxbnmeelozAQVV2hguti/EB3.bsjf0noOkW1e', 0, NULL, 'activo', NULL, NULL),
(50, 'Francisco', '$2y$10$Y6QBinOgrECiNVyDDX.pUufzLqlRA53bkBLAg8PLZerNZgyrkbBUi', 0, NULL, 'activo', NULL, NULL),
(54, 'Christian', '$2y$10$UTpzN/orLLN3XGYyPgcVj.eOlo.xOYxRkDsdi7siUrxq3IEz3LSw2', 0, NULL, 'activo', '18e6792cc8807554ca7172d5873f3e772d74ba8e9e69deb0f00bd159c385b9f86a5e8d709860737b73811ef4b64f5037a57f', '2024-08-21 23:06:17'),
(56, 'santiago.rosales', '$2y$10$bkWPEZWYouKXzovq6x3Yf.YDBgtXpxk0/vaQCypaQIAaktpS4UHTC', 0, NULL, 'activo', NULL, NULL);

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
(54, 2, 54),
(56, 1, 56);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tab_administradores`
--
ALTER TABLE `tab_administradores`
  ADD PRIMARY KEY (`ID_ADMINISTRADOR`),
  ADD KEY `FK_REFERENCE_15` (`ID_USUARIO`);

--
-- Indices de la tabla `tab_bancos`
--
ALTER TABLE `tab_bancos`
  ADD PRIMARY KEY (`ID_BANCO`);

--
-- Indices de la tabla `tab_categorias`
--
ALTER TABLE `tab_categorias`
  ADD PRIMARY KEY (`ID_CATEGORIA`);

--
-- Indices de la tabla `tab_categoria_deportista`
--
ALTER TABLE `tab_categoria_deportista`
  ADD PRIMARY KEY (`ID_CATEGORIA`,`ID_DEPORTISTA`),
  ADD KEY `ID_DEPORTISTA` (`ID_DEPORTISTA`);

--
-- Indices de la tabla `tab_deportistas`
--
ALTER TABLE `tab_deportistas`
  ADD PRIMARY KEY (`ID_DEPORTISTA`),
  ADD KEY `FK_REFERENCE_12` (`ID_USUARIO`);

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
-- Indices de la tabla `tab_informes`
--
ALTER TABLE `tab_informes`
  ADD PRIMARY KEY (`id_informe`),
  ADD KEY `id_deportista` (`id_deportista`),
  ADD KEY `id_representante` (`id_representante`),
  ADD KEY `id_entrenador` (`id_entrenador`);

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
  ADD KEY `ID_DEPORTISTA` (`ID_DEPORTISTA`),
  ADD KEY `ID_BANCO` (`ID_BANCO`);

--
-- Indices de la tabla `tab_pdfs`
--
ALTER TABLE `tab_pdfs`
  ADD PRIMARY KEY (`id_pdf`),
  ADD KEY `id_usuario` (`id_usuario`);

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
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tab_administradores`
--
ALTER TABLE `tab_administradores`
  MODIFY `ID_ADMINISTRADOR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tab_bancos`
--
ALTER TABLE `tab_bancos`
  MODIFY `ID_BANCO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT de la tabla `tab_categorias`
--
ALTER TABLE `tab_categorias`
  MODIFY `ID_CATEGORIA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tab_deportistas`
--
ALTER TABLE `tab_deportistas`
  MODIFY `ID_DEPORTISTA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tab_detalles`
--
ALTER TABLE `tab_detalles`
  MODIFY `ID_DETALLE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `tab_entrenadores`
--
ALTER TABLE `tab_entrenadores`
  MODIFY `ID_ENTRENADOR` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tab_informes`
--
ALTER TABLE `tab_informes`
  MODIFY `id_informe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tab_logs`
--
ALTER TABLE `tab_logs`
  MODIFY `ID_LOG` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=369;

--
-- AUTO_INCREMENT de la tabla `tab_pagos`
--
ALTER TABLE `tab_pagos`
  MODIFY `ID_PAGO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `tab_pdfs`
--
ALTER TABLE `tab_pdfs`
  MODIFY `id_pdf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tab_productos`
--
ALTER TABLE `tab_productos`
  MODIFY `id_producto` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tab_producto_categoria`
--
ALTER TABLE `tab_producto_categoria`
  MODIFY `id_categoria_producto` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `ID_USUARIO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `tab_usu_tipo`
--
ALTER TABLE `tab_usu_tipo`
  MODIFY `ID_USU_TIPO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tab_administradores`
--
ALTER TABLE `tab_administradores`
  ADD CONSTRAINT `FK_REFERENCE_15` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);

--
-- Filtros para la tabla `tab_categoria_deportista`
--
ALTER TABLE `tab_categoria_deportista`
  ADD CONSTRAINT `tab_categoria_deportista_ibfk_1` FOREIGN KEY (`ID_CATEGORIA`) REFERENCES `tab_categorias` (`ID_CATEGORIA`) ON DELETE CASCADE,
  ADD CONSTRAINT `tab_categoria_deportista_ibfk_2` FOREIGN KEY (`ID_DEPORTISTA`) REFERENCES `tab_deportistas` (`ID_DEPORTISTA`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tab_deportistas`
--
ALTER TABLE `tab_deportistas`
  ADD CONSTRAINT `FK_REFERENCE_12` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);

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
-- Filtros para la tabla `tab_informes`
--
ALTER TABLE `tab_informes`
  ADD CONSTRAINT `tab_informes_ibfk_1` FOREIGN KEY (`id_deportista`) REFERENCES `tab_deportistas` (`ID_DEPORTISTA`),
  ADD CONSTRAINT `tab_informes_ibfk_2` FOREIGN KEY (`id_representante`) REFERENCES `tab_representantes` (`ID_REPRESENTANTE`),
  ADD CONSTRAINT `tab_informes_ibfk_3` FOREIGN KEY (`id_entrenador`) REFERENCES `tab_entrenadores` (`ID_ENTRENADOR`);

--
-- Filtros para la tabla `tab_logs`
--
ALTER TABLE `tab_logs`
  ADD CONSTRAINT `FK_REFERENCE_14` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tab_usuarios` (`ID_USUARIO`);

--
-- Filtros para la tabla `tab_pagos`
--
ALTER TABLE `tab_pagos`
  ADD CONSTRAINT `ID_BANCO` FOREIGN KEY (`ID_BANCO`) REFERENCES `tab_bancos` (`ID_BANCO`),
  ADD CONSTRAINT `tab_pagos_ibfk_1` FOREIGN KEY (`ID_REPRESENTANTE`) REFERENCES `tab_representantes` (`ID_REPRESENTANTE`),
  ADD CONSTRAINT `tab_pagos_ibfk_2` FOREIGN KEY (`ID_DEPORTISTA`) REFERENCES `tab_deportistas` (`ID_DEPORTISTA`);

--
-- Filtros para la tabla `tab_pdfs`
--
ALTER TABLE `tab_pdfs`
  ADD CONSTRAINT `tab_pdfs_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tab_usuarios` (`ID_USUARIO`) ON DELETE CASCADE;

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
