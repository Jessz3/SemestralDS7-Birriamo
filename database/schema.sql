-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-07-2026 a las 00:00:04
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
-- Base de datos: `eventos_deportivos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academias`
--

CREATE TABLE `academias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(180) NOT NULL,
  `ruc` varchar(40) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `academias`
--

INSERT INTO `academias` (`id`, `nombre`, `ruc`, `descripcion`, `correo`, `telefono`, `direccion`, `logo`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Halcones', '123', '', 'halcones@gmail.com', '323232', 'Don bosco', NULL, 1, '2026-07-13 16:10:51', '2026-07-13 16:10:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academia_deportes`
--

CREATE TABLE `academia_deportes` (
  `academia_id` int(10) UNSIGNED NOT NULL,
  `deporte_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `academia_deportes`
--

INSERT INTO `academia_deportes` (`academia_id`, `deporte_id`) VALUES
(1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(10) UNSIGNED NOT NULL,
  `organizador_id` int(10) UNSIGNED NOT NULL,
  `deporte_id` int(10) UNSIGNED NOT NULL,
  `instalacion_id` int(10) UNSIGNED NOT NULL,
  `entrenador_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('BIRRIA','ENTRENAMIENTO','TORNEO','EVENTO') NOT NULL,
  `modalidad` enum('INDIVIDUAL','EQUIPO','MIXTA') NOT NULL,
  `nombre` varchar(180) NOT NULL,
  `descripcion` text NOT NULL,
  `reglas` text DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `fecha_cierre_inscripcion` datetime DEFAULT NULL,
  `edad_minima` smallint(5) UNSIGNED DEFAULT NULL,
  `edad_maxima` smallint(5) UNSIGNED DEFAULT NULL,
  `cupos_disponibles` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `capacidad_invitados` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `requiere_pago` tinyint(1) NOT NULL DEFAULT 0,
  `costo_inscripcion` decimal(10,2) NOT NULL DEFAULT 0.00,
  `costo_instalacion` decimal(10,2) NOT NULL DEFAULT 0.00,
  `imagen` varchar(255) DEFAULT NULL,
  `codigo_qr` varchar(255) DEFAULT NULL,
  `token_publico` char(64) NOT NULL,
  `estado` enum('BORRADOR','PUBLICADA','CERRADA','FINALIZADA','CANCELADA','TRASLADADA') NOT NULL DEFAULT 'BORRADOR',
  `motivo_cancelacion` text DEFAULT NULL,
  `actividad_origen_id` int(10) UNSIGNED DEFAULT NULL,
  `fecha_publicacion` datetime DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `organizador_id`, `deporte_id`, `instalacion_id`, `entrenador_id`, `tipo`, `modalidad`, `nombre`, `descripcion`, `reglas`, `fecha_inicio`, `fecha_fin`, `fecha_cierre_inscripcion`, `edad_minima`, `edad_maxima`, `cupos_disponibles`, `capacidad_invitados`, `requiere_pago`, `costo_inscripcion`, `costo_instalacion`, `imagen`, `codigo_qr`, `token_publico`, `estado`, `motivo_cancelacion`, `actividad_origen_id`, `fecha_publicacion`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 2, 5, 1, NULL, 'TORNEO', 'EQUIPO', 'Partido del mundial', 'Partido entre china vs panama', '', '2026-07-06 16:15:00', '2026-07-13 16:20:00', '2026-07-31 16:15:00', 10, NULL, 20, 0, 1, 3.00, 3.00, NULL, '/evento/1d63a1a44483e605c53605f81b8c82a659239bc6591f43041729e80612647662', '1d63a1a44483e605c53605f81b8c82a659239bc6591f43041729e80612647662', 'PUBLICADA', NULL, NULL, '2026-07-13 16:17:43', '2026-07-13 16:16:35', '2026-07-13 16:17:43'),
(2, 3, 3, 1, NULL, 'TORNEO', 'INDIVIDUAL', 'Final de Haikyuu', 'Partido final', '', '2026-07-13 16:23:00', '2026-08-01 16:23:00', NULL, 18, NULL, 20, 0, 1, 3.00, 3.00, NULL, '/evento/6af30833a2fadc092906bb7ffd7505e2291ffa6eb13090decf2602952ce7f6bb', '6af30833a2fadc092906bb7ffd7505e2291ffa6eb13090decf2602952ce7f6bb', 'PUBLICADA', NULL, NULL, '2026-07-13 16:24:47', '2026-07-13 16:24:31', '2026-07-13 16:24:47'),
(3, 3, 1, 1, NULL, 'EVENTO', 'EQUIPO', 'Futbol topuria', 'Futbol mortal mortal', '', '2026-07-12 16:51:00', '2026-07-14 16:52:00', '2028-10-14 16:51:00', 15, NULL, 20, 100, 1, 1.00, 5.00, NULL, '/evento/d1e3a8e4abcd9cba2621b14ddf26290b1dc0e38351ed48e33e8f62a2a087a56d', 'd1e3a8e4abcd9cba2621b14ddf26290b1dc0e38351ed48e33e8f62a2a087a56d', 'PUBLICADA', NULL, NULL, '2026-07-13 16:52:47', '2026-07-13 16:52:26', '2026-07-13 16:52:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_arbitros`
--

CREATE TABLE `actividad_arbitros` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `arbitro_id` int(10) UNSIGNED NOT NULL,
  `rol` varchar(80) NOT NULL DEFAULT 'Árbitro principal',
  `estado` enum('ASIGNADO','CONFIRMADO','RECHAZADO','FINALIZADO') NOT NULL DEFAULT 'ASIGNADO',
  `fecha_asignacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actividad_arbitros`
--

INSERT INTO `actividad_arbitros` (`id`, `actividad_id`, `arbitro_id`, `rol`, `estado`, `fecha_asignacion`) VALUES
(1, 3, 1, 'Árbitro principal', 'ASIGNADO', '2026-07-13 16:52:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arbitros`
--

CREATE TABLE `arbitros` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre_completo` varchar(160) NOT NULL,
  `cedula` varchar(30) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `licencia` varchar(80) DEFAULT NULL,
  `experiencia` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `arbitros`
--

INSERT INTO `arbitros` (`id`, `nombre_completo`, `cedula`, `correo`, `telefono`, `licencia`, `experiencia`, `activo`, `fecha_creacion`) VALUES
(1, 'Yango', NULL, 'yango@gmail.com', '323232', 'Increible', 'Mucha', 1, '2026-07-13 16:49:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arbitro_deportes`
--

CREATE TABLE `arbitro_deportes` (
  `arbitro_id` int(10) UNSIGNED NOT NULL,
  `deporte_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `arbitro_deportes`
--

INSERT INTO `arbitro_deportes` (`arbitro_id`, `deporte_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `modulo` varchar(80) NOT NULL,
  `accion` varchar(80) NOT NULL,
  `tabla_afectada` varchar(80) DEFAULT NULL,
  `registro_id` varchar(80) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`)),
  `direccion_ip` varchar(45) DEFAULT NULL,
  `agente_usuario` varchar(500) DEFAULT NULL,
  `firma_digital` longtext DEFAULT NULL,
  `algoritmo_firma` varchar(50) DEFAULT NULL,
  `fecha_evento` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id`, `usuario_id`, `modulo`, `accion`, `tabla_afectada`, `registro_id`, `descripcion`, `datos_anteriores`, `datos_nuevos`, `direccion_ip`, `agente_usuario`, `firma_digital`, `algoritmo_firma`, `fecha_evento`) VALUES
(1, 1, 'SISTEMA', 'INSTALACION', NULL, NULL, 'Creación inicial de la base de datos.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-13 15:09:13'),
(2, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 15:48:52'),
(3, 1, 'ORGANIZADORES', 'CREAR', 'organizadores', '1', 'Registro de organizador Luisa De Gracia (usuario: luisadegracia).', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '6c01ef5ec9d3f71a499c96324113b909189abaf0d5f5f1ba6d77610f1f5b19d1', 'HMAC-SHA256', '2026-07-13 15:49:45'),
(4, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 15:50:30'),
(5, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 15:50:55'),
(6, 1, 'USUARIOS', 'CREAR', 'usuarios', '3', 'Creacion de usuario erick12 con rol ORGANIZADOR.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '0809d83b6eda4d17b70858f1d95b51398184247e1799269b5869db23483f0b4f', 'HMAC-SHA256', '2026-07-13 15:52:02'),
(7, 3, 'AUTENTICACION', 'LOGIN', 'usuarios', '3', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'ab2319b302c7b0e86345b6052d74928ea06982e88931551bfc3d626ef8904e6d', 'HMAC-SHA256', '2026-07-13 15:52:45'),
(8, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 15:54:02'),
(9, 1, 'USUARIOS', 'CREAR', 'usuarios', '4', 'Creacion de usuario Jessz3 con rol PARTICIPANTE.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '476a3a3e837d16f88ccf413087993cfc41f4cc6ea1cc9917092d609570aa0a04', 'HMAC-SHA256', '2026-07-13 15:56:09'),
(10, 4, 'AUTENTICACION', 'LOGIN', 'usuarios', '4', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '5fc5b6cd7780b2d6d4c53b8a5d6e9e8c521cc54ae9fa15213c572d5cae1fd026', 'HMAC-SHA256', '2026-07-13 15:56:20'),
(11, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 15:58:10'),
(12, 5, 'USUARIOS', 'AUTORREGISTRO', 'usuarios', '5', 'Creacion de usuario rquinte14 con rol ORGANIZADOR.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '8932e617affa7a4413e1a5b7798c41c6da24849d43c28a55c8688c24aa3dabab', 'HMAC-SHA256', '2026-07-13 16:02:37'),
(13, 5, 'AUTENTICACION', 'LOGIN', 'usuarios', '5', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'd20a97ec1df8b8c70f71ebbd38a0c2f5ff31ae49293cc434d5f11d6829fbab39', 'HMAC-SHA256', '2026-07-13 16:02:46'),
(14, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 16:05:01'),
(15, 1, 'ACTIVIDADES', 'CREAR', 'actividades', '1', 'Actividad \'Partido del mundial\' creada en estado BORRADOR.', NULL, '{\"organizador_id\":2,\"deporte_id\":5,\"instalacion_id\":1,\"entrenador_id\":null,\"tipo\":\"TORNEO\",\"modalidad\":\"EQUIPO\",\"nombre\":\"Partido del mundial\",\"descripcion\":\"Partido entre china vs panama\",\"reglas\":\"\",\"fecha_inicio\":\"2026-07-06 16:15\",\"fecha_fin\":\"2026-07-13 16:20\",\"fecha_cierre_inscripcion\":\"2026-07-31 16:15\",\"edad_minima\":10,\"edad_maxima\":null,\"cupos_disponibles\":20,\"capacidad_invitados\":0,\"requiere_pago\":1,\"costo_inscripcion\":3,\"costo_instalacion\":3,\"arbitros\":[]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '446b9fe54b7b2d22b95fb9a4bb14beb1b87923019759a54e3631db3b1810966b', 'HMAC-SHA256', '2026-07-13 16:16:36'),
(16, 1, 'ACTIVIDADES', 'PUBLICAR', 'actividades', '1', 'Actividad publicada.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'd7252e2edd870f9c404edfbff97e82dcc23a47e9c477caa35404ef8ec0ae8422', 'HMAC-SHA256', '2026-07-13 16:17:43'),
(17, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 16:20:04'),
(18, 5, 'AUTENTICACION', 'LOGIN', 'usuarios', '5', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'd20a97ec1df8b8c70f71ebbd38a0c2f5ff31ae49293cc434d5f11d6829fbab39', 'HMAC-SHA256', '2026-07-13 16:20:51'),
(19, 5, 'ACTIVIDADES', 'CREAR', 'actividades', '2', 'Actividad \'Final de Haikyuu\' creada en estado BORRADOR.', NULL, '{\"organizador_id\":3,\"deporte_id\":3,\"instalacion_id\":1,\"entrenador_id\":null,\"tipo\":\"TORNEO\",\"modalidad\":\"INDIVIDUAL\",\"nombre\":\"Final de Haikyuu\",\"descripcion\":\"Partido final\",\"reglas\":\"\",\"fecha_inicio\":\"2026-07-13 16:23\",\"fecha_fin\":\"2026-08-01 16:23\",\"fecha_cierre_inscripcion\":null,\"edad_minima\":18,\"edad_maxima\":null,\"cupos_disponibles\":20,\"capacidad_invitados\":0,\"requiere_pago\":1,\"costo_inscripcion\":3,\"costo_instalacion\":3,\"arbitros\":[]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '7c3e64f836fb5b60391a35f59c2783face92ccefc1b90cf7f1ba0ddfee8cc66d', 'HMAC-SHA256', '2026-07-13 16:24:31'),
(20, 5, 'ACTIVIDADES', 'PUBLICAR', 'actividades', '2', 'Actividad publicada.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'f25817e5462b5c4aa28e245de2b7ecdb3af08932a5c002c29c82955d64afef7c', 'HMAC-SHA256', '2026-07-13 16:24:47'),
(21, 5, 'AUTENTICACION', 'LOGIN', 'usuarios', '5', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'd20a97ec1df8b8c70f71ebbd38a0c2f5ff31ae49293cc434d5f11d6829fbab39', 'HMAC-SHA256', '2026-07-13 16:37:33'),
(22, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 16:37:44'),
(23, 4, 'AUTENTICACION', 'LOGIN', 'usuarios', '4', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '5fc5b6cd7780b2d6d4c53b8a5d6e9e8c521cc54ae9fa15213c572d5cae1fd026', 'HMAC-SHA256', '2026-07-13 16:38:09'),
(24, 4, 'INSCRIPCIONES', 'CREAR_INDIVIDUAL', 'inscripciones_individuales', '1', 'Inscripcion publica de jessica@utp.ac.pa a la actividad #2.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '216b83e861bd5a337bd207f57b5cf30b3215a67382bfa698cf9952f7e1b9e339', 'HMAC-SHA256', '2026-07-13 16:38:31'),
(25, 5, 'AUTENTICACION', 'LOGIN', 'usuarios', '5', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'd20a97ec1df8b8c70f71ebbd38a0c2f5ff31ae49293cc434d5f11d6829fbab39', 'HMAC-SHA256', '2026-07-13 16:39:06'),
(26, 4, 'AUTENTICACION', 'LOGIN', 'usuarios', '4', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '5fc5b6cd7780b2d6d4c53b8a5d6e9e8c521cc54ae9fa15213c572d5cae1fd026', 'HMAC-SHA256', '2026-07-13 16:41:59'),
(27, 4, 'EQUIPOS', 'CREAR', 'equipos', '1', 'Equipo Mis Lacayos registrado para el representante jessica@utp.ac.pa.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'dbb421ec381379e53a83943baf1f4ce56b6848b92dd62cf5bb62681e6bee6346', 'HMAC-SHA256', '2026-07-13 16:43:29'),
(28, 4, 'AUTENTICACION', 'LOGIN', 'usuarios', '4', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '5fc5b6cd7780b2d6d4c53b8a5d6e9e8c521cc54ae9fa15213c572d5cae1fd026', 'HMAC-SHA256', '2026-07-13 16:46:24'),
(29, NULL, 'INSCRIPCIONES', 'CREAR_INDIVIDUAL', 'inscripciones_individuales', '2', 'Inscripcion publica de janitza@utp.ac.pa a la actividad #2.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '7f2a5c21519c044f7084b660b10a4110a863f91e6108da8288f5dc707ba2b733', 'HMAC-SHA256', '2026-07-13 16:48:26'),
(30, 5, 'AUTENTICACION', 'LOGIN', 'usuarios', '5', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'd20a97ec1df8b8c70f71ebbd38a0c2f5ff31ae49293cc434d5f11d6829fbab39', 'HMAC-SHA256', '2026-07-13 16:48:42'),
(31, 5, 'ACTIVIDADES', 'CREAR', 'actividades', '3', 'Actividad \'Futbol topuria\' creada en estado BORRADOR.', NULL, '{\"organizador_id\":3,\"deporte_id\":1,\"instalacion_id\":1,\"entrenador_id\":null,\"tipo\":\"EVENTO\",\"modalidad\":\"EQUIPO\",\"nombre\":\"Futbol topuria\",\"descripcion\":\"Futbol mortal mortal\",\"reglas\":\"\",\"fecha_inicio\":\"2026-07-12 16:51\",\"fecha_fin\":\"2026-07-14 16:52\",\"fecha_cierre_inscripcion\":\"2028-10-14 16:51\",\"edad_minima\":15,\"edad_maxima\":null,\"cupos_disponibles\":20,\"capacidad_invitados\":100,\"requiere_pago\":1,\"costo_inscripcion\":1,\"costo_instalacion\":5,\"arbitros\":[1]}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '7d18e007d6353d3f6592dc4032c2b451a98c87ff373673ed0666aab284b500a4', 'HMAC-SHA256', '2026-07-13 16:52:26'),
(32, 5, 'ACTIVIDADES', 'PUBLICAR', 'actividades', '3', 'Actividad publicada.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '13398e842883b7d5c9576399c20fca126c95acb2020634481e61abb4e673da18', 'HMAC-SHA256', '2026-07-13 16:52:47'),
(33, 5, 'AUTENTICACION', 'LOGIN', 'usuarios', '5', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'd20a97ec1df8b8c70f71ebbd38a0c2f5ff31ae49293cc434d5f11d6829fbab39', 'HMAC-SHA256', '2026-07-13 16:54:54'),
(34, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 16:57:12'),
(35, 1, 'AUTENTICACION', 'LOGIN', 'usuarios', '1', 'Inicio de sesion exitoso.', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '1f2b76c2d03bcef9a022afb28bad0e72dec05a5a471bdbd63cdd73c3c3c74746', 'HMAC-SHA256', '2026-07-13 16:58:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calendario_actividad_fechas`
--

CREATE TABLE `calendario_actividad_fechas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(180) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `instalacion_id` int(10) UNSIGNED DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `numero_jornada` smallint(5) UNSIGNED DEFAULT NULL,
  `estado` enum('PROGRAMADA','REALIZADA','CANCELADA','TRASLADADA') NOT NULL DEFAULT 'PROGRAMADA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `claves_rsa_usuario`
--

CREATE TABLE `claves_rsa_usuario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `clave_publica` mediumtext NOT NULL,
  `clave_privada_cifrada` mediumtext NOT NULL,
  `algoritmo` varchar(50) NOT NULL DEFAULT 'RSA-2048',
  `huella_publica` varchar(128) NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_revocacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `claves_rsa_usuario`
--

INSERT INTO `claves_rsa_usuario` (`id`, `usuario_id`, `clave_publica`, `clave_privada_cifrada`, `algoritmo`, `huella_publica`, `activa`, `fecha_creacion`, `fecha_revocacion`) VALUES
(1, 2, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuOUskQuP/FPQHxuynRmj\nnUlITWktB8n7Q2lYrVJY/gra9lqC8dS5Ubc7tT/s0l9MOvwDgYgyel3teO0Q0ET5\nsmovnR48wZ2vYu/99C7S0fIFukmiHmR6JByZpCU21h/SZydyKtKbVqIoMv6hrOQ1\nXOX6EF4NzdYmvnWLma0Ek7jlBOU8UWU48+As/CCIQzn2kgoSCa/ZO3FZTfovzQJS\nbMMqIxHTAdEJ4wXcsIqOd96QH/FlDrWQcI2sWCGGZxpvK179lDWeAl1HtZDyp3C2\ncVizDG6OjmgJ/lxBVduP9BP79fpygv6/jmJoVMbVxCel8h0+BWPP/cYALYtOkwEO\nBQIDAQAB\n-----END PUBLIC KEY-----\n', 'n+Vdv4h9w4Waa+rEVDlb+A==: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', 'RSA-2048', '08d6b238d5f7236afa547a60f7e8a8b2d67f17e2c0d5025512be6e3de8c44899', 1, '2026-07-13 15:49:45', NULL),
(2, 3, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxfrwLREoczX6sFuBuKOK\nhu7LdGx+++Cv67KmTHADCN2QlGnZE3DnN58hsTfoPG2SQRi+xSogHq5DoWWmmIK6\nX4gv+MpjrBWtxgGpasvUpJZZM4AqWtJwI7fVD03QdemQ1f5+3Plwo42Ti9Vfm3lO\n5GcmlN2/bEQXRWWpmkwfR/a0fx5imEd0L7yJF5Lk2HVWkLLZ4i/HLmstLkiI3OE2\n4NEfOIqofIvDgcKZk91oRv9GhVv4Tp7nIHnq6grJt+rjsxw4b0/QHcYX6pmo3Saa\ntqt95NJ06jvXdNXwgfGXiAmiTTbXgFayLndtow/92onUYlqZWsihEdjcnvkXAv0H\nPwIDAQAB\n-----END PUBLIC KEY-----\n', 'luFOzpfTVD+E65tI574LcA==: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', 'RSA-2048', 'ac5fda3d9a7b9d5c9745c8182e5783bd675ce8d3dd446913c5badec022f08f57', 1, '2026-07-13 15:52:02', NULL),
(3, 4, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAx3C8gZ6TQR6tI8GtSt55\ngb5vRifmzVIlZmQxt4bqenMw/Ie4OZWEF5fk5ewv9zmm8bCNKAxUEpd7t3wLrIbB\nAFGJvsFHlZPTatRm5CWnB22LupmSexjpHmaL+lc0h5rfVXPcptBP/uMohB1cNkmB\n9U8dDeNT7arKd240JXlbIsmjo1Pl623ndldmsgZvAJd0ubBNMVKlFthPUnr+x/4c\no6semkPJg1iTXS8Y72pOuXL9W/g8yaW12Vvxi3dhCBCgVVGygPvuNNtTjwVOE531\n4Ns59ACEpdwom6OTVQAMmUpNYhUVIl9SHTO38cU5zYZ+DvdhTCCzlkMmSwbSI+Oh\nywIDAQAB\n-----END PUBLIC KEY-----\n', '9eKIi10fvShMQqS9gp/R7Q==:nu2vtbu2p7wBlvrxT2Du38CXN4vWpjFnynFrlOCyu7h6F5Axc25RmnFcSdLwkLix/AQjLr4g+fZF1X4sgH4Ptm75uFttIH1LmACLh2EXvc+m3lwO3es8sB6rl8jy2oDbjq5QeEoK7TPzKs/R90ObAnqrime4txBfiKDrbtp7tWJcK0ub2NxI2FWmykavH72upSsKUHT09QPDv1RAvIXseuoq7nyptehqy7cE48h6HA3Qn9YxDWP8RcpsXEtqPmF01XzFNojp/k4BZU+3roFlg+uggOCTN3ArupJb3oK/Kb/r77aOJNvGsneYUH/FEWgDvfUfDqXPOem/Suv740Ek0TzljdeCIKbs3n8lsGUteY58lgkM7Hird84Xqvg/zdx2diNw5qTF3v/qQ3gN0DGN8ffqKyeKTYs+bFlYYWj94Bc/0GZl8pQjJ/7Q8iuyrcACXQOYaQtn7EuWC+igkHwbK/5ubbp64omfOR+l1ziCEYUwBGx4/HlwaZaUugu2TxqlTPtXCsRuKbMEw1EYFIDwC5L0jeGF+wxT17BAUPCZmx0Jb9nKnMBCpIJGWEuzRLLT0JzUXs+Kdj2GD3GEQ7X2J9QXoML5+vmxH3rMdPy+TodYDJBPvmEkn/DkRs0RlJGLqRi7cFBp7MSqGK+dAUv8lyIqyk8IxwtQjuED8h+jChKbxlHSmC+nlVhLdeAGCgDgknU7xv59b+wwV0stqEAmjVqK6rvFMlXARLV9L3Vxran51eYvt6iPJ37yFTiZFsm0V9R8+DkzN3vlSvCZ0fAcvIYS8QBJ5lGyyqxkFaLKjJfm6RYqBxAQTvWeb5QaFELXOKDjS6zpl/DSQaDKj7hn8HaShRYeQ4rt47gCYEGdSiZf1wVL/4pwLMzejGmYLaUCn01T20PG7lAG75s3eb5lUDF52pwx+kAyxw6PQOJsAGT1FoGdVmxs7hDTna3vJvA8/17JDrhMCWu89Ka+pb/xdhI6kOeHyKpIKGU5RXJEvuOowHVpD8MlSbE6DRyeh5LRcXePFR/Pkr34gEDHKxkVhHqj2weSLNNfvaDGBmFVs/oCMEUoDgLNM3WdXi1MpZ3evlmWv3ePJYqq6e0GtMaVfENmYxpOCDRwD9x8rfHonF/dyT7tURfSWTpNHAaYgfZNyHRg8Ip1yqnotBV5K1F9aWD5aOsUf+egEyhRdsqX8iA7kCiVE6rAsKe58srKLx5lZBfoCwAjbHxsS6o94WqD2dy9UflonaIeM47KEwcrrPAFk/ljgLHHdtlE+GysOjGciou1OrL9lP0UgjkuIwR5X6j2f4PdKyDc298ZmpKIZF9WFUugSVt40x4MVlw2aC/A1zGLYDmA5pBzS9s1TcTNmAr6O2IkLV9PO6c2vpz/zLqys7ucPCs9T5mDAZoR0jsV6r5RGIynGXAJBeKfSNxSZzbhLYXCH6bHHrv1HVDQp83Q+lz2cpXp0WWweWcZLZagvjWvz3GLfaKwKaqP/8zPOno3AQEFp2b0ykprzAcU8PzZzfEmgwRixeshNuyk3AlIVtxEcVyoJ2Qrx6SUosLM52hdrirNPw3W1x1l0D5LdGVbxRjuu2vgDC4f5QOD87h2L82z2jCqhf+5wlpNbXCYVF1yQkrPIsUEs3We4UPcNKZIXsYGIOWY6R6px+f54oF6/USgVXxpUh8z0uzuKVFJuF5bymLz/RMAreXAF0TZ2CEOr2dPqiRn09CaTblpPt4qHnSfyZH+3lIX2Tl2gKVeS8v+fW2YR8mEvwz8BXb5Kh0GjoZfDfD+Uh9D2gF4YzuGdlktGhIzw7jZRokK106s4660pvK49HKLQY7J6byyccwgJYstK6TUeZh+fyRBAhPiV3HrokezcoFuL6JDtQKX8DBXRWTl+ijmHYU8dLBPCW6pjdM3RR+ni1Ry3dfdIGwodDpjwTWLjErIsvW2JTkLj5ycEIXELkad7atExtaFJ7JBThz6BhkC9IQlqqmEqon8MSwZ+rXUuY9D7j0UZmHtQn/WLvOqmbWXbqxUIy4Ao3y4fP7+oxC/uaG7SrYIRLZ8cK4s8/EA1OrI8pHHD2XmH05pYLnTfS/xQy1uBUtMNlcjfN09NbS4dpplnpsR9IBc1T0Kcf/qF7yCQfuMRNMaCOLd9L9qCU5pMFH8hBriyJRw2nccyzMXEzCcq5YXj7VmPMC5CzdJhjyd+H0t6BUTzTuPcsKl9d216nRr4yESbwTnGoHDeGBbx+L80hclvP3sQRX0yYm0vnEpkVWSdsu60puPo/YuvaOr3n/8eMj1yU0=', 'RSA-2048', '9b1eb10d26c49901cdc169563818103c79608facfb0f5867bcd61ab71ba8ebef', 1, '2026-07-13 15:56:09', NULL),
(4, 5, '-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAoO9dUojq0rg2QW9PQ5qI\n1GAh6N+Rx1d2bSLex/UrStVvn1DEeXOLZfLyvMGvDvAuk3M2hqfz5SaM285w9/yK\nEZYKeMOOv/qDI+kH15tjn/A1tIDlUs4a6lbiFfXLo/X86aQQMUZTRjfcvCrIUQYq\nsqyzku/KG9ZrQYT/NSrMlOHjya5I9FleEzcLfACYP3s4vfBSssYsfVj6EJacMmAk\nP8Gj0I4s9Nc2H65Rsz9j/cX/RteodMzjag6w5qwbxIDzaKaCg7tTX5SS0Ppd7GM5\nzCGLv9HROMuFLqOEp9bpHE9O6jWCq57YY8nnRSfXevnxPd78L637Tl0LGBp8ZhqH\nBwIDAQAB\n-----END PUBLIC KEY-----\n', 'QK7WnU01Zpo6S1gHsEo3GQ==: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', 'RSA-2048', 'b108b16319105129ba28fd69d49bc10401ce6ffa61d530361c9e8bc28bcb020f', 1, '2026-07-13 16:02:36', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `clave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`clave`, `valor`, `descripcion`, `fecha_actualizacion`) VALUES
('INTENTOS_LOGIN_MAXIMOS', '5', 'Cantidad máxima de intentos fallidos de inicio de sesión.', '2026-07-13 15:09:13'),
('ITBMS_PORCENTAJE', '7.00', 'Porcentaje de ITBMS aplicado a las facturas.', '2026-07-13 15:09:13'),
('MONEDA', 'PAB', 'Código de moneda utilizado por el sistema.', '2026-07-13 15:09:13'),
('NOMBRE_SISTEMA', 'Sistema de Eventos Deportivos', 'Nombre público del sistema.', '2026-07-13 15:09:13'),
('PREFIJO_FACTURA', 'FAC', 'Prefijo utilizado para generar números de factura.', '2026-07-13 15:09:13'),
('RSA_BITS', '2048', 'Tamaño mínimo de las llaves RSA.', '2026-07-13 15:09:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deportes`
--

CREATE TABLE `deportes` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `es_equipo` tinyint(1) NOT NULL DEFAULT 0,
  `minimo_jugadores` smallint(5) UNSIGNED DEFAULT NULL,
  `maximo_jugadores` smallint(5) UNSIGNED DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `deportes`
--

INSERT INTO `deportes` (`id`, `nombre`, `descripcion`, `es_equipo`, `minimo_jugadores`, `maximo_jugadores`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Fútbol', 'Disciplina deportiva de equipo.', 1, 5, 22, 1, '2026-07-13 15:09:13', '2026-07-13 15:09:13'),
(2, 'Baloncesto', 'Disciplina deportiva de equipo.', 1, 5, 15, 1, '2026-07-13 15:09:13', '2026-07-13 15:09:13'),
(3, 'Voleibol', 'Disciplina deportiva de equipo.', 1, 6, 14, 1, '2026-07-13 15:09:13', '2026-07-13 15:09:13'),
(4, 'Béisbol', 'Disciplina deportiva de equipo.', 1, 9, 25, 1, '2026-07-13 15:09:13', '2026-07-13 15:09:13'),
(5, 'Atletismo', 'Disciplina deportiva individual o por relevos.', 0, NULL, NULL, 1, '2026-07-13 15:09:13', '2026-07-13 15:09:13'),
(6, 'Natación', 'Disciplina acuática individual o por equipos.', 0, NULL, NULL, 1, '2026-07-13 15:09:13', '2026-07-13 15:09:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones`
--

CREATE TABLE `devoluciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pago_id` bigint(20) UNSIGNED NOT NULL,
  `factura_id` bigint(20) UNSIGNED DEFAULT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `motivo` text NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('SOLICITADA','APROBADA','PROCESADA','RECHAZADA') NOT NULL DEFAULT 'SOLICITADA',
  `referencia_devolucion` varchar(120) DEFAULT NULL,
  `solicitada_por` int(10) UNSIGNED DEFAULT NULL,
  `procesada_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_proceso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenadores`
--

CREATE TABLE `entrenadores` (
  `id` int(10) UNSIGNED NOT NULL,
  `organizador_id` int(10) UNSIGNED DEFAULT NULL,
  `academia_id` int(10) UNSIGNED DEFAULT NULL,
  `nombre_completo` varchar(160) NOT NULL,
  `cedula` varchar(30) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `certificaciones` text DEFAULT NULL,
  `anios_experiencia` smallint(5) UNSIGNED DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrenador_deportes`
--

CREATE TABLE `entrenador_deportes` (
  `entrenador_id` int(10) UNSIGNED NOT NULL,
  `deporte_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(10) UNSIGNED NOT NULL,
  `participante_id` int(10) UNSIGNED NOT NULL,
  `academia_id` int(10) UNSIGNED DEFAULT NULL,
  `deporte_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `participante_id`, `academia_id`, `deporte_id`, `nombre`, `avatar`, `descripcion`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 1, 1, 2, 'Mis Lacayos', '/uploads/avatars/equipo_6a555c01478329.95118703.png', '', 1, '2026-07-13 16:43:29', '2026-07-13 16:43:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones_arbitros`
--

CREATE TABLE `evaluaciones_arbitros` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `arbitro_id` int(10) UNSIGNED NOT NULL,
  `organizador_id` int(10) UNSIGNED NOT NULL,
  `puntuacion` tinyint(3) UNSIGNED NOT NULL,
  `puntualidad` tinyint(3) UNSIGNED DEFAULT NULL,
  `conocimiento_reglas` tinyint(3) UNSIGNED DEFAULT NULL,
  `imparcialidad` tinyint(3) UNSIGNED DEFAULT NULL,
  `manejo_actividad` tinyint(3) UNSIGNED DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `fecha_evaluacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `evaluaciones_arbitros`
--

INSERT INTO `evaluaciones_arbitros` (`id`, `actividad_id`, `arbitro_id`, `organizador_id`, `puntuacion`, `puntualidad`, `conocimiento_reglas`, `imparcialidad`, `manejo_actividad`, `comentario`, `fecha_evaluacion`) VALUES
(1, 3, 1, 3, 5, 5, 5, 5, 5, 'Es muy bueno', '2026-07-13 16:52:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_factura` varchar(30) NOT NULL,
  `pago_id` bigint(20) UNSIGNED NOT NULL,
  `participante_id` int(10) UNSIGNED NOT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `equipo_id` int(10) UNSIGNED DEFAULT NULL,
  `nombre_cliente` varchar(180) NOT NULL,
  `identificacion_cliente` varchar(40) DEFAULT NULL,
  `correo_cliente` varchar(150) DEFAULT NULL,
  `fecha_venta` datetime NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `tasa_itbms` decimal(5,2) NOT NULL DEFAULT 7.00,
  `itbms` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('EMITIDA','ANULADA','DEVUELTA') NOT NULL DEFAULT 'EMITIDA',
  `ruta_pdf` varchar(255) DEFAULT NULL,
  `pdf_hash_sha256` char(64) DEFAULT NULL,
  `firma_digital` longtext DEFAULT NULL,
  `certificado_publico` mediumtext DEFAULT NULL,
  `algoritmo_firma` varchar(50) DEFAULT 'SHA256withRSA',
  `formato_documento` varchar(30) NOT NULL DEFAULT 'PDF/A',
  `fecha_firma` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `numero_factura`, `pago_id`, `participante_id`, `actividad_id`, `equipo_id`, `nombre_cliente`, `identificacion_cliente`, `correo_cliente`, `fecha_venta`, `subtotal`, `tasa_itbms`, `itbms`, `total`, `estado`, `ruta_pdf`, `pdf_hash_sha256`, `firma_digital`, `certificado_publico`, `algoritmo_firma`, `formato_documento`, `fecha_firma`) VALUES
(1, 'FAC-20260713-7381', 1, 1, 2, NULL, 'Jessica Zheng', NULL, 'jessica@utp.ac.pa', '2026-07-13 16:38:31', 3.00, 7.00, 0.21, 3.21, 'EMITIDA', NULL, NULL, '879283350816cc76e7f5d21ec8bddd2b4cb9af094026ce2cd8749acf245a64a8', NULL, 'HMAC-SHA256', 'PDF/A', '2026-07-13 16:38:31'),
(2, 'FAC-20260713-0763', 2, 2, 2, NULL, 'Janitza Justiniani', NULL, 'janitza@utp.ac.pa', '2026-07-13 16:48:26', 3.00, 7.00, 0.21, 3.21, 'EMITIDA', NULL, NULL, 'a4e6372e6f644e3011751d754646207e9843d66e521d1b5b82c1e833090fb531', NULL, 'HMAC-SHA256', 'PDF/A', '2026-07-13 16:48:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_detalles`
--

CREATE TABLE `factura_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `factura_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT 1.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal_linea` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `factura_detalles`
--

INSERT INTO `factura_detalles` (`id`, `factura_id`, `descripcion`, `cantidad`, `precio_unitario`, `subtotal_linea`) VALUES
(1, 1, 'Inscripción individual a Final de Haikyuu', 1.00, 3.00, 3.00),
(2, 2, 'Inscripción individual a Final de Haikyuu', 1.00, 3.00, 3.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_passwords`
--

CREATE TABLE `historial_passwords` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidentes_deportivos`
--

CREATE TABLE `incidentes_deportivos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `reportado_por` int(10) UNSIGNED DEFAULT NULL,
  `arbitro_id` int(10) UNSIGNED DEFAULT NULL,
  `equipo_id` int(10) UNSIGNED DEFAULT NULL,
  `jugador_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('LESION','CONDUCTA_ANTIDEPORTIVA','EXPULSION','DAÑO_INSTALACION','SUSPENSION','OTRO') NOT NULL,
  `gravedad` enum('LEVE','MODERADA','GRAVE','CRITICA') NOT NULL DEFAULT 'LEVE',
  `descripcion` text NOT NULL,
  `acciones_tomadas` text DEFAULT NULL,
  `fecha_incidente` datetime NOT NULL,
  `estado` enum('ABIERTO','EN_REVISION','RESUELTO','CERRADO') NOT NULL DEFAULT 'ABIERTO',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones_equipos`
--

CREATE TABLE `inscripciones_equipos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `equipo_id` int(10) UNSIGNED NOT NULL,
  `estado` enum('PENDIENTE_PAGO','PAGO_PENDIENTE_VALIDACION','PENDIENTE_APROBACION','APROBADA','RECHAZADA','CANCELADA','FINALIZADA') NOT NULL DEFAULT 'PENDIENTE_PAGO',
  `observaciones` text DEFAULT NULL,
  `reglas_facilitadas` text DEFAULT NULL,
  `reglas_aceptadas` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_inscripcion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_aprobacion` datetime DEFAULT NULL,
  `aprobado_por` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones_individuales`
--

CREATE TABLE `inscripciones_individuales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `participante_id` int(10) UNSIGNED NOT NULL,
  `estado` enum('PENDIENTE_PAGO','PAGO_PENDIENTE_VALIDACION','PENDIENTE_APROBACION','APROBADA','RECHAZADA','CANCELADA','FINALIZADA') NOT NULL DEFAULT 'PENDIENTE_PAGO',
  `observaciones` text DEFAULT NULL,
  `reglas_aceptadas` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_inscripcion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_aprobacion` datetime DEFAULT NULL,
  `aprobado_por` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inscripciones_individuales`
--

INSERT INTO `inscripciones_individuales` (`id`, `actividad_id`, `participante_id`, `estado`, `observaciones`, `reglas_aceptadas`, `fecha_inscripcion`, `fecha_aprobacion`, `aprobado_por`) VALUES
(1, 2, 1, 'APROBADA', NULL, 1, '2026-07-13 16:38:30', '2026-07-13 16:38:30', NULL),
(2, 2, 2, 'APROBADA', NULL, 1, '2026-07-13 16:48:26', '2026-07-13 16:48:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instalaciones`
--

CREATE TABLE `instalaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `tipo` enum('CANCHA','GIMNASIO','PISCINA','ESTADIO','PISTA','SALON','OTRO') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `provincia` varchar(100) NOT NULL DEFAULT 'Panamá',
  `distrito` varchar(100) DEFAULT NULL,
  `corregimiento` varchar(100) DEFAULT NULL,
  `espacio_disponible` varchar(150) DEFAULT NULL,
  `capacidad_invitados` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `costo_base` decimal(10,2) NOT NULL DEFAULT 0.00,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `instalaciones`
--

INSERT INTO `instalaciones` (`id`, `nombre`, `tipo`, `descripcion`, `direccion`, `provincia`, `distrito`, `corregimiento`, `espacio_disponible`, `capacidad_invitados`, `costo_base`, `latitud`, `longitud`, `activo`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Jessicas park', 'CANCHA', '', 'Don bosco', 'Panamá', 'Don Bosco', 'Juan Diaz', 'Cancha abierta', 100, 3.00, NULL, NULL, 1, '2026-07-13 16:15:31', '2026-07-13 16:15:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invitaciones`
--

CREATE TABLE `invitaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actividad_id` int(10) UNSIGNED NOT NULL,
  `academia_id` int(10) UNSIGNED DEFAULT NULL,
  `equipo_id` int(10) UNSIGNED DEFAULT NULL,
  `correo_destino` varchar(150) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `token` char(64) NOT NULL,
  `estado` enum('PENDIENTE','ACEPTADA','RECHAZADA','VENCIDA') NOT NULL DEFAULT 'PENDIENTE',
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_respuesta` datetime DEFAULT NULL,
  `fecha_expiracion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id` int(10) UNSIGNED NOT NULL,
  `equipo_id` int(10) UNSIGNED NOT NULL,
  `nombre_completo` varchar(160) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `edad` smallint(5) UNSIGNED NOT NULL,
  `peso_kg` decimal(5,2) DEFAULT NULL,
  `posicion` varchar(80) DEFAULT NULL,
  `numero_camiseta` smallint(5) UNSIGNED DEFAULT NULL,
  `capitan` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_contacto`
--

CREATE TABLE `mensajes_contacto` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `asunto` varchar(180) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('NUEVO','LEIDO','RESPONDIDO','CERRADO') NOT NULL DEFAULT 'NUEVO',
  `fecha_envio` datetime NOT NULL DEFAULT current_timestamp(),
  `atendido_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_atencion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes_contacto`
--

INSERT INTO `mensajes_contacto` (`id`, `nombre`, `correo`, `telefono`, `asunto`, `mensaje`, `estado`, `fecha_envio`, `atendido_por`, `fecha_atencion`) VALUES
(1, 'SOS', 'sos@utp.ac.pa', '', 'No se como inscribirme o como pagar', 'No se como pagar', 'LEIDO', '2026-07-13 16:58:06', 1, '2026-07-13 16:58:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `organizadores`
--

CREATE TABLE `organizadores` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `academia_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_organizador` enum('INDEPENDIENTE','ACADEMIA','ENTRENADOR','EMPRESA','COMITE','OTRO') NOT NULL DEFAULT 'INDEPENDIENTE',
  `nombre_comercial` varchar(180) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `verificado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_verificacion` datetime DEFAULT NULL,
  `verificado_por` int(10) UNSIGNED DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `organizadores`
--

INSERT INTO `organizadores` (`id`, `usuario_id`, `academia_id`, `tipo_organizador`, `nombre_comercial`, `descripcion`, `verificado`, `fecha_verificacion`, `verificado_por`, `activo`, `fecha_registro`) VALUES
(1, 2, NULL, 'ENTRENADOR', '', '', 0, NULL, NULL, 1, '2026-07-13 15:49:45'),
(2, 3, NULL, 'INDEPENDIENTE', NULL, NULL, 0, NULL, NULL, 1, '2026-07-13 15:52:02'),
(3, 5, NULL, 'INDEPENDIENTE', NULL, NULL, 0, NULL, NULL, 1, '2026-07-13 16:02:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inscripcion_individual_id` bigint(20) UNSIGNED DEFAULT NULL,
  `inscripcion_equipo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `participante_id` int(10) UNSIGNED NOT NULL,
  `metodo_pago` enum('EFECTIVO','TRANSFERENCIA','TARJETA','YAPPY','OTRO') NOT NULL,
  `referencia` varchar(120) DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `estado` enum('PENDIENTE','EN_REVISION','APROBADO','RECHAZADO','DEVUELTO','ANULADO') NOT NULL DEFAULT 'PENDIENTE',
  `fecha_pago` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_validacion` datetime DEFAULT NULL,
  `validado_por` int(10) UNSIGNED DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `inscripcion_individual_id`, `inscripcion_equipo_id`, `participante_id`, `metodo_pago`, `referencia`, `monto`, `comprobante`, `estado`, `fecha_pago`, `fecha_validacion`, `validado_por`, `observaciones`) VALUES
(1, 1, NULL, 1, 'EFECTIVO', NULL, 3.00, NULL, 'APROBADO', '2026-07-13 16:38:30', '2026-07-13 16:38:30', NULL, NULL),
(2, 2, NULL, 2, 'EFECTIVO', NULL, 3.00, NULL, 'APROBADO', '2026-07-13 16:48:26', '2026-07-13 16:48:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE `participantes` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `contacto_emergencia_nombre` varchar(150) DEFAULT NULL,
  `contacto_emergencia_telefono` varchar(30) DEFAULT NULL,
  `observaciones_medicas` varchar(500) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `participantes`
--

INSERT INTO `participantes` (`id`, `usuario_id`, `contacto_emergencia_nombre`, `contacto_emergencia_telefono`, `observaciones_medicas`, `activo`, `fecha_registro`) VALUES
(1, 4, NULL, NULL, NULL, 1, '2026-07-13 15:56:09'),
(2, 6, NULL, NULL, NULL, 1, '2026-07-13 16:48:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

CREATE TABLE `tokens_recuperacion` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `fecha_expiracion` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `cedula_pasaporte` varchar(30) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('MASCULINO','FEMENINO','OTRO','NO_ESPECIFICA') DEFAULT NULL,
  `usuario` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('ADMINISTRADOR','OPERADOR','ORGANIZADOR','PARTICIPANTE') NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `requiere_cambio_password` tinyint(1) NOT NULL DEFAULT 0,
  `intentos_fallidos` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL,
  `ultimo_acceso` datetime DEFAULT NULL,
  `creado_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `cedula_pasaporte`, `correo`, `telefono`, `fecha_nacimiento`, `sexo`, `usuario`, `password_hash`, `rol`, `activo`, `requiere_cambio_password`, `intentos_fallidos`, `bloqueado_hasta`, `ultimo_acceso`, `creado_por`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'Administrador', 'Principal', NULL, 'admin.sistema@utp.edu.pa', '6000-0000', NULL, NULL, 'admin', '$2y$12$6lKmNFOwihDFfreCpg1s4uyIgso2udjEpkiujBXKXvHP/3k8/eEaS', 'ADMINISTRADOR', 1, 0, 0, NULL, '2026-07-13 16:58:11', NULL, '2026-07-13 15:09:13', '2026-07-13 16:58:11'),
(2, 'Luisa', 'De Gracia', NULL, 'ola@gmail.com', NULL, NULL, NULL, 'luisadegracia', '$2y$12$k7XAnskH.H1qlOxK7zPNr.6CkSWoWzcazfdW78IVxxH/VPKucdcJu', 'ORGANIZADOR', 1, 1, 1, NULL, NULL, 1, '2026-07-13 15:49:44', '2026-07-13 15:50:47'),
(3, 'Erick', 'Hou', NULL, 'erick@utp.ac.pa', NULL, NULL, NULL, 'erick12', '$2y$12$irL3wERfiD9V7eXFR852peTzyZOH9kuBN0wxZxCzYftsQoL4QUxkm', 'ORGANIZADOR', 1, 0, 0, NULL, '2026-07-13 15:52:45', 1, '2026-07-13 15:52:02', '2026-07-13 15:52:45'),
(4, 'Jessica', 'Zheng', NULL, 'jessica@utp.ac.pa', NULL, NULL, NULL, 'Jessz3', '$2y$12$tE69z2ho7UTnsWFLH64XoO7QqfiCKgh9EHC7sdGWv1Mw/9lKsV0J.', 'PARTICIPANTE', 1, 0, 0, NULL, '2026-07-13 16:46:24', 1, '2026-07-13 15:56:09', '2026-07-13 16:46:24'),
(5, 'Roniel', 'Quintero', NULL, 'rquinte14@utp.ac.pa', NULL, NULL, NULL, 'rquinte14', '$2y$12$Goh4Fm6E0gAxfyPjYk8nIe244DdzBRkjaM1rcFhlEeLxwZMgoiJ5i', 'ORGANIZADOR', 1, 0, 0, NULL, '2026-07-13 16:54:54', NULL, '2026-07-13 16:02:36', '2026-07-13 16:54:54'),
(6, 'Janitza', 'Justiniani', NULL, 'janitza@utp.ac.pa', '1232323', NULL, NULL, 'janitzajustiniani', '$2y$12$AwEHQsHMUWsrSIdG4aIjL.AXcunrcZNvnTvviJvNIAkYOMxgkJNXe', 'PARTICIPANTE', 1, 1, 0, NULL, NULL, NULL, '2026-07-13 16:48:26', '2026-07-13 16:48:26');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_actividades_publicas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_actividades_publicas` (
`id` int(10) unsigned
,`token_publico` char(64)
,`nombre` varchar(180)
,`tipo` enum('BIRRIA','ENTRENAMIENTO','TORNEO','EVENTO')
,`modalidad` enum('INDIVIDUAL','EQUIPO','MIXTA')
,`descripcion` text
,`fecha_inicio` datetime
,`fecha_fin` datetime
,`edad_minima` smallint(5) unsigned
,`edad_maxima` smallint(5) unsigned
,`cupos_disponibles` int(10) unsigned
,`requiere_pago` tinyint(1)
,`costo_inscripcion` decimal(10,2)
,`imagen` varchar(255)
,`codigo_qr` varchar(255)
,`deporte` varchar(100)
,`instalacion` varchar(150)
,`direccion` varchar(255)
,`organizador` varchar(161)
,`estado` enum('BORRADOR','PUBLICADA','CERRADA','FINALIZADA','CANCELADA','TRASLADADA')
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_desempeno_arbitros`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_desempeno_arbitros` (
`arbitro_id` int(10) unsigned
,`nombre_completo` varchar(160)
,`total_evaluaciones` bigint(21)
,`promedio_general` decimal(6,2)
,`promedio_puntualidad` decimal(6,2)
,`promedio_reglas` decimal(6,2)
,`promedio_imparcialidad` decimal(6,2)
,`promedio_manejo` decimal(6,2)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_actividades_publicas`
--
DROP TABLE IF EXISTS `vw_actividades_publicas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_actividades_publicas`  AS SELECT `a`.`id` AS `id`, `a`.`token_publico` AS `token_publico`, `a`.`nombre` AS `nombre`, `a`.`tipo` AS `tipo`, `a`.`modalidad` AS `modalidad`, `a`.`descripcion` AS `descripcion`, `a`.`fecha_inicio` AS `fecha_inicio`, `a`.`fecha_fin` AS `fecha_fin`, `a`.`edad_minima` AS `edad_minima`, `a`.`edad_maxima` AS `edad_maxima`, `a`.`cupos_disponibles` AS `cupos_disponibles`, `a`.`requiere_pago` AS `requiere_pago`, `a`.`costo_inscripcion` AS `costo_inscripcion`, `a`.`imagen` AS `imagen`, `a`.`codigo_qr` AS `codigo_qr`, `d`.`nombre` AS `deporte`, `i`.`nombre` AS `instalacion`, `i`.`direccion` AS `direccion`, concat(`u`.`nombre`,' ',`u`.`apellido`) AS `organizador`, `a`.`estado` AS `estado` FROM ((((`actividades` `a` join `deportes` `d` on(`d`.`id` = `a`.`deporte_id`)) join `instalaciones` `i` on(`i`.`id` = `a`.`instalacion_id`)) join `organizadores` `o` on(`o`.`id` = `a`.`organizador_id`)) join `usuarios` `u` on(`u`.`id` = `o`.`usuario_id`)) WHERE `a`.`estado` = 'PUBLICADA' AND `a`.`fecha_inicio` >= current_timestamp() ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_desempeno_arbitros`
--
DROP TABLE IF EXISTS `vw_desempeno_arbitros`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_desempeno_arbitros`  AS SELECT `ar`.`id` AS `arbitro_id`, `ar`.`nombre_completo` AS `nombre_completo`, count(`ea`.`id`) AS `total_evaluaciones`, round(avg(`ea`.`puntuacion`),2) AS `promedio_general`, round(avg(`ea`.`puntualidad`),2) AS `promedio_puntualidad`, round(avg(`ea`.`conocimiento_reglas`),2) AS `promedio_reglas`, round(avg(`ea`.`imparcialidad`),2) AS `promedio_imparcialidad`, round(avg(`ea`.`manejo_actividad`),2) AS `promedio_manejo` FROM (`arbitros` `ar` left join `evaluaciones_arbitros` `ea` on(`ea`.`arbitro_id` = `ar`.`id`)) GROUP BY `ar`.`id`, `ar`.`nombre_completo` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `academias`
--
ALTER TABLE `academias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_academias_nombre` (`nombre`),
  ADD UNIQUE KEY `uq_academias_ruc` (`ruc`);

--
-- Indices de la tabla `academia_deportes`
--
ALTER TABLE `academia_deportes`
  ADD PRIMARY KEY (`academia_id`,`deporte_id`),
  ADD KEY `fk_academia_deportes_deporte` (`deporte_id`);

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_actividades_token` (`token_publico`),
  ADD KEY `fk_actividades_instalacion` (`instalacion_id`),
  ADD KEY `fk_actividades_entrenador` (`entrenador_id`),
  ADD KEY `fk_actividades_origen` (`actividad_origen_id`),
  ADD KEY `idx_actividades_estado_fecha` (`estado`,`fecha_inicio`),
  ADD KEY `idx_actividades_organizador` (`organizador_id`),
  ADD KEY `idx_actividades_deporte` (`deporte_id`);

--
-- Indices de la tabla `actividad_arbitros`
--
ALTER TABLE `actividad_arbitros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_actividad_arbitro` (`actividad_id`,`arbitro_id`),
  ADD KEY `fk_actividad_arbitros_arbitro` (`arbitro_id`);

--
-- Indices de la tabla `arbitros`
--
ALTER TABLE `arbitros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_arbitros_cedula` (`cedula`),
  ADD UNIQUE KEY `uq_arbitros_licencia` (`licencia`);

--
-- Indices de la tabla `arbitro_deportes`
--
ALTER TABLE `arbitro_deportes`
  ADD PRIMARY KEY (`arbitro_id`,`deporte_id`),
  ADD KEY `fk_arbitro_deportes_deporte` (`deporte_id`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bitacora_usuario_fecha` (`usuario_id`,`fecha_evento`);

--
-- Indices de la tabla `calendario_actividad_fechas`
--
ALTER TABLE `calendario_actividad_fechas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_calendario_actividad` (`actividad_id`),
  ADD KEY `fk_calendario_instalacion` (`instalacion_id`);

--
-- Indices de la tabla `claves_rsa_usuario`
--
ALTER TABLE `claves_rsa_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_claves_rsa_huella` (`huella_publica`),
  ADD KEY `fk_claves_rsa_usuario` (`usuario_id`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`clave`);

--
-- Indices de la tabla `deportes`
--
ALTER TABLE `deportes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_deportes_nombre` (`nombre`);

--
-- Indices de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_devoluciones_pago` (`pago_id`),
  ADD KEY `fk_devoluciones_factura` (`factura_id`),
  ADD KEY `fk_devoluciones_actividad` (`actividad_id`),
  ADD KEY `fk_devoluciones_solicitada_por` (`solicitada_por`),
  ADD KEY `fk_devoluciones_procesada_por` (`procesada_por`);

--
-- Indices de la tabla `entrenadores`
--
ALTER TABLE `entrenadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_entrenadores_cedula` (`cedula`),
  ADD KEY `fk_entrenadores_organizador` (`organizador_id`),
  ADD KEY `fk_entrenadores_academia` (`academia_id`);

--
-- Indices de la tabla `entrenador_deportes`
--
ALTER TABLE `entrenador_deportes`
  ADD PRIMARY KEY (`entrenador_id`,`deporte_id`),
  ADD KEY `fk_entrenador_deportes_deporte` (`deporte_id`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_equipos_academia` (`academia_id`),
  ADD KEY `fk_equipos_deporte` (`deporte_id`),
  ADD KEY `idx_equipos_participante` (`participante_id`);

--
-- Indices de la tabla `evaluaciones_arbitros`
--
ALTER TABLE `evaluaciones_arbitros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_evaluacion_arbitro` (`actividad_id`,`arbitro_id`,`organizador_id`),
  ADD KEY `fk_evaluaciones_organizador` (`organizador_id`),
  ADD KEY `idx_evaluaciones_arbitro` (`arbitro_id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_facturas_numero` (`numero_factura`),
  ADD UNIQUE KEY `uq_facturas_pago` (`pago_id`),
  ADD KEY `fk_facturas_participante` (`participante_id`),
  ADD KEY `fk_facturas_actividad` (`actividad_id`),
  ADD KEY `fk_facturas_equipo` (`equipo_id`),
  ADD KEY `idx_facturas_fecha` (`fecha_venta`);

--
-- Indices de la tabla `factura_detalles`
--
ALTER TABLE `factura_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_factura_detalles_factura` (`factura_id`);

--
-- Indices de la tabla `historial_passwords`
--
ALTER TABLE `historial_passwords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_historial_password_usuario` (`usuario_id`);

--
-- Indices de la tabla `incidentes_deportivos`
--
ALTER TABLE `incidentes_deportivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_incidentes_reportado_por` (`reportado_por`),
  ADD KEY `fk_incidentes_arbitro` (`arbitro_id`),
  ADD KEY `fk_incidentes_equipo` (`equipo_id`),
  ADD KEY `fk_incidentes_jugador` (`jugador_id`),
  ADD KEY `idx_incidentes_actividad` (`actividad_id`);

--
-- Indices de la tabla `inscripciones_equipos`
--
ALTER TABLE `inscripciones_equipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_inscripcion_equipo` (`actividad_id`,`equipo_id`),
  ADD KEY `fk_inscripcion_equipo_equipo` (`equipo_id`),
  ADD KEY `fk_inscripcion_equipo_aprobado` (`aprobado_por`),
  ADD KEY `idx_inscripciones_equipo_estado` (`estado`);

--
-- Indices de la tabla `inscripciones_individuales`
--
ALTER TABLE `inscripciones_individuales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_inscripcion_individual` (`actividad_id`,`participante_id`),
  ADD KEY `fk_inscripcion_ind_participante` (`participante_id`),
  ADD KEY `fk_inscripcion_ind_aprobado` (`aprobado_por`),
  ADD KEY `idx_inscripciones_ind_estado` (`estado`);

--
-- Indices de la tabla `instalaciones`
--
ALTER TABLE `instalaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `invitaciones`
--
ALTER TABLE `invitaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_invitaciones_token` (`token`),
  ADD KEY `fk_invitaciones_actividad` (`actividad_id`),
  ADD KEY `fk_invitaciones_academia` (`academia_id`),
  ADD KEY `fk_invitaciones_equipo` (`equipo_id`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jugadores_equipo` (`equipo_id`);

--
-- Indices de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mensajes_atendido_por` (`atendido_por`);

--
-- Indices de la tabla `organizadores`
--
ALTER TABLE `organizadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_organizadores_usuario` (`usuario_id`),
  ADD KEY `fk_organizadores_academia` (`academia_id`),
  ADD KEY `fk_organizadores_verificado_por` (`verificado_por`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pagos_inscripcion_individual` (`inscripcion_individual_id`),
  ADD KEY `fk_pagos_inscripcion_equipo` (`inscripcion_equipo_id`),
  ADD KEY `fk_pagos_participante` (`participante_id`),
  ADD KEY `fk_pagos_validado_por` (`validado_por`),
  ADD KEY `idx_pagos_estado_fecha` (`estado`,`fecha_pago`);

--
-- Indices de la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_participantes_usuario` (`usuario_id`);

--
-- Indices de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tokens_recuperacion` (`token_hash`),
  ADD KEY `fk_tokens_recuperacion_usuario` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuarios_correo` (`correo`),
  ADD UNIQUE KEY `uq_usuarios_usuario` (`usuario`),
  ADD UNIQUE KEY `uq_usuarios_cedula` (`cedula_pasaporte`),
  ADD KEY `fk_usuarios_creado_por` (`creado_por`),
  ADD KEY `idx_usuarios_rol_activo` (`rol`,`activo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `academias`
--
ALTER TABLE `academias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `actividad_arbitros`
--
ALTER TABLE `actividad_arbitros`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `arbitros`
--
ALTER TABLE `arbitros`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `calendario_actividad_fechas`
--
ALTER TABLE `calendario_actividad_fechas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `claves_rsa_usuario`
--
ALTER TABLE `claves_rsa_usuario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `deportes`
--
ALTER TABLE `deportes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entrenadores`
--
ALTER TABLE `entrenadores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `evaluaciones_arbitros`
--
ALTER TABLE `evaluaciones_arbitros`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `factura_detalles`
--
ALTER TABLE `factura_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historial_passwords`
--
ALTER TABLE `historial_passwords`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `incidentes_deportivos`
--
ALTER TABLE `incidentes_deportivos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones_equipos`
--
ALTER TABLE `inscripciones_equipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones_individuales`
--
ALTER TABLE `inscripciones_individuales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `instalaciones`
--
ALTER TABLE `instalaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `invitaciones`
--
ALTER TABLE `invitaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `organizadores`
--
ALTER TABLE `organizadores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `academia_deportes`
--
ALTER TABLE `academia_deportes`
  ADD CONSTRAINT `fk_academia_deportes_academia` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_academia_deportes_deporte` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `fk_actividades_deporte` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_actividades_entrenador` FOREIGN KEY (`entrenador_id`) REFERENCES `entrenadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_actividades_instalacion` FOREIGN KEY (`instalacion_id`) REFERENCES `instalaciones` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_actividades_organizador` FOREIGN KEY (`organizador_id`) REFERENCES `organizadores` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_actividades_origen` FOREIGN KEY (`actividad_origen_id`) REFERENCES `actividades` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `actividad_arbitros`
--
ALTER TABLE `actividad_arbitros`
  ADD CONSTRAINT `fk_actividad_arbitros_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_actividad_arbitros_arbitro` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `arbitro_deportes`
--
ALTER TABLE `arbitro_deportes`
  ADD CONSTRAINT `fk_arbitro_deportes_arbitro` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_arbitro_deportes_deporte` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `fk_bitacora_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `calendario_actividad_fechas`
--
ALTER TABLE `calendario_actividad_fechas`
  ADD CONSTRAINT `fk_calendario_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_calendario_instalacion` FOREIGN KEY (`instalacion_id`) REFERENCES `instalaciones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `claves_rsa_usuario`
--
ALTER TABLE `claves_rsa_usuario`
  ADD CONSTRAINT `fk_claves_rsa_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD CONSTRAINT `fk_devoluciones_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devoluciones_factura` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devoluciones_pago` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devoluciones_procesada_por` FOREIGN KEY (`procesada_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devoluciones_solicitada_por` FOREIGN KEY (`solicitada_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `entrenadores`
--
ALTER TABLE `entrenadores`
  ADD CONSTRAINT `fk_entrenadores_academia` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entrenadores_organizador` FOREIGN KEY (`organizador_id`) REFERENCES `organizadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `entrenador_deportes`
--
ALTER TABLE `entrenador_deportes`
  ADD CONSTRAINT `fk_entrenador_deportes_deporte` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entrenador_deportes_entrenador` FOREIGN KEY (`entrenador_id`) REFERENCES `entrenadores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `fk_equipos_academia` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_equipos_deporte` FOREIGN KEY (`deporte_id`) REFERENCES `deportes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_equipos_participante` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `evaluaciones_arbitros`
--
ALTER TABLE `evaluaciones_arbitros`
  ADD CONSTRAINT `fk_evaluaciones_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluaciones_arbitro` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evaluaciones_organizador` FOREIGN KEY (`organizador_id`) REFERENCES `organizadores` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `fk_facturas_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_facturas_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_facturas_pago` FOREIGN KEY (`pago_id`) REFERENCES `pagos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_facturas_participante` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura_detalles`
--
ALTER TABLE `factura_detalles`
  ADD CONSTRAINT `fk_factura_detalles_factura` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_passwords`
--
ALTER TABLE `historial_passwords`
  ADD CONSTRAINT `fk_historial_password_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `incidentes_deportivos`
--
ALTER TABLE `incidentes_deportivos`
  ADD CONSTRAINT `fk_incidentes_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_incidentes_arbitro` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_incidentes_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_incidentes_jugador` FOREIGN KEY (`jugador_id`) REFERENCES `jugadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_incidentes_reportado_por` FOREIGN KEY (`reportado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `inscripciones_equipos`
--
ALTER TABLE `inscripciones_equipos`
  ADD CONSTRAINT `fk_inscripcion_equipo_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscripcion_equipo_aprobado` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscripcion_equipo_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `inscripciones_individuales`
--
ALTER TABLE `inscripciones_individuales`
  ADD CONSTRAINT `fk_inscripcion_ind_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscripcion_ind_aprobado` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscripcion_ind_participante` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `invitaciones`
--
ALTER TABLE `invitaciones`
  ADD CONSTRAINT `fk_invitaciones_academia` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invitaciones_actividad` FOREIGN KEY (`actividad_id`) REFERENCES `actividades` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invitaciones_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `fk_jugadores_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mensajes_contacto`
--
ALTER TABLE `mensajes_contacto`
  ADD CONSTRAINT `fk_mensajes_atendido_por` FOREIGN KEY (`atendido_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `organizadores`
--
ALTER TABLE `organizadores`
  ADD CONSTRAINT `fk_organizadores_academia` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_organizadores_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_organizadores_verificado_por` FOREIGN KEY (`verificado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pagos_inscripcion_equipo` FOREIGN KEY (`inscripcion_equipo_id`) REFERENCES `inscripciones_equipos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pagos_inscripcion_individual` FOREIGN KEY (`inscripcion_individual_id`) REFERENCES `inscripciones_individuales` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pagos_participante` FOREIGN KEY (`participante_id`) REFERENCES `participantes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pagos_validado_por` FOREIGN KEY (`validado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD CONSTRAINT `fk_participantes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD CONSTRAINT `fk_tokens_recuperacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
