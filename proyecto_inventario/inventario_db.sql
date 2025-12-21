-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 12, 2025 at 02:40 PM
-- Server version: 8.0.42
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventario_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `preguntas_seguridad`
--

CREATE TABLE `preguntas_seguridad` (
  `id` int NOT NULL,
  `pregunta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `preguntas_seguridad`
--

INSERT INTO `preguntas_seguridad` (`id`, `pregunta`) VALUES
(1, '¿Cuál es el nombre de tu primera mascota?'),
(2, '¿Cuál es tu ciudad de nacimiento?'),
(3, '¿Cuál es el segundo nombre de tu padre?'),
(4, '¿Cuál era el modelo de tu primer coche?'),
(5, '¿En qué ciudad se conocieron tus padres?'),
(6, '¿Cuál es tu comida favorita?'),
(7, '¿Cuál es el nombre de tu abuela materna?'),
(8, '¿Cuál es tu película favorita?'),
(9, '¿Cuál es el nombre de la calle donde creciste?'),
(10, '¿Cuál es tu libro favorito?'),
(11, '¿A qué colegio fuiste en primaria?'),
(12, '¿Cuál es tu equipo deportivo favorito?'),
(13, '¿Cuál es el nombre de tu mejor amigo/a de la infancia?'),
(14, '¿Cuál es tu canción favorita?'),
(15, '¿Cuál fue tu primer trabajo?'),
(16, '¿Cuál es el nombre de tu primera mascota?'),
(17, '¿Cuál es tu ciudad de nacimiento?'),
(18, '¿Cuál es el segundo nombre de tu padre?'),
(19, '¿Cuál era el modelo de tu primer coche?'),
(20, '¿En qué ciudad se conocieron tus padres?'),
(21, '¿Cuál es tu comida favorita?'),
(22, '¿Cuál es el nombre de tu abuela materna?'),
(23, '¿Cuál es tu película favorita?'),
(24, '¿Cuál es el nombre de la calle donde creciste?'),
(25, '¿Cuál es tu libro favorito?'),
(26, '¿A qué colegio fuiste en primaria?'),
(27, '¿Cuál es tu equipo deportivo favorito?'),
(28, '¿Cuál es el nombre de tu mejor amigo/a de la infancia?'),
(29, '¿Cuál es tu canción favorita?'),
(30, '¿Cuál fue tu primer trabajo?');

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `id` int NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `detalle` text,
  `proveedor_id` int DEFAULT NULL,
  `precio` decimal(12,2) DEFAULT '0.00',
  `cantidad` int DEFAULT '0',
  `estado` varchar(50) DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int NOT NULL,
  `empresa` varchar(200) NOT NULL,
  `contacto` varchar(150) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `respuestas_seguridad_usuario`
--

CREATE TABLE `respuestas_seguridad_usuario` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `pregunta_id` int NOT NULL,
  `respuesta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Admin', 'Administrador con todos los permisos'),
(2, 'Vendedor', 'Usuario con acceso a ventas y productos'),
(3, 'Admin', 'Administrador con todos los permisos'),
(4, 'Vendedor', 'Usuario con acceso a ventas y productos');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `pregunta1` varchar(255) DEFAULT NULL,
  `respuesta1` varchar(255) DEFAULT NULL,
  `pregunta2` varchar(255) DEFAULT NULL,
  `respuesta2` varchar(255) DEFAULT NULL,
  `rol_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `cedula`, `nombres`, `apellidos`, `clave`, `correo`, `telefono`, `estado`, `pregunta1`, `respuesta1`, `pregunta2`, `respuesta2`, `rol_id`) VALUES
(2, '1805269063', 'David', 'Ojeda', '$2y$10$c1iidd9I8umdS6iWLC09WO0BwySvbt/9IClQjQYQulr9XJQfxorTO', 'jdojeda@pucesa.edu.ec', '1234567890', 'activo', NULL, NULL, NULL, NULL, 1),
(5, '1805129580', 'Jonathan', 'Acurio', '$2y$10$decbfICN8C0Em4ezJfVzuOoOQwCkG.c6LAKQPq6H/sdLM4uOj42P.', 'jacurio1@outlook.com', '0963410492', 'activo', '¿Cuál es el nombre de tu primera mascota?', 'Jonathan', '¿Cuál es el nombre de tu abuela materna?', 'Jonathan', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `preguntas_seguridad`
--
ALTER TABLE `preguntas_seguridad`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indexes for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `respuestas_seguridad_usuario`
--
ALTER TABLE `respuestas_seguridad_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_pregunta` (`usuario_id`,`pregunta_id`),
  ADD KEY `pregunta_id` (`pregunta_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `rol_id` (`rol_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `preguntas_seguridad`
--
ALTER TABLE `preguntas_seguridad`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `respuestas_seguridad_usuario`
--
ALTER TABLE `respuestas_seguridad_usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `respuestas_seguridad_usuario`
--
ALTER TABLE `respuestas_seguridad_usuario`
  ADD CONSTRAINT `respuestas_seguridad_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `respuestas_seguridad_usuario_ibfk_2` FOREIGN KEY (`pregunta_id`) REFERENCES `preguntas_seguridad` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
