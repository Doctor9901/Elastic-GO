-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 23, 2025 at 06:50 PM
-- Server version: 8.0.41
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elastic_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `alunos`
--

CREATE TABLE `alunos` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `curso` enum('básico','médio','intermediário') NOT NULL DEFAULT 'básico',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alunos`
--

INSERT INTO `alunos` (`id`, `usuario_id`, `curso`, `data_cadastro`) VALUES
(9, 18, 'básico', '2025-10-23 18:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `tempos_exercicios`
--

CREATE TABLE `tempos_exercicios` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `tipo_exercicio` enum('caminhada','alongamento','elastico') NOT NULL,
  `tempo_minutos` int NOT NULL,
  `data_registro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tempos_exercicios`
--

INSERT INTO `tempos_exercicios` (`id`, `usuario_id`, `tipo_exercicio`, `tempo_minutos`, `data_registro`) VALUES
(1, 3, 'caminhada', 2, '2025-10-23 18:47:51'),
(2, 18, 'caminhada', 2, '2025-10-23 18:48:14'),
(3, 18, 'caminhada', 1, '2025-10-23 18:49:04');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('comum','admin') DEFAULT 'comum',
  `status` enum('ativo','inativo','banido') DEFAULT 'ativo',
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `curso_nivel` enum('básico','médio','intermediário') NOT NULL DEFAULT 'básico'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `status`, `criado_em`, `atualizado_em`, `curso_nivel`) VALUES
(3, 'jose', 'jose@gmail.com', '$2y$10$QwlXCWK603zsHBY1sMhCVOaHJhD1e8CxM4oRbJJ3fCquVpNUAV0AC', 'admin', 'ativo', '2025-09-26 04:47:42', '2025-09-26 04:59:59', 'básico'),
(18, 'jeoge', 'douto@gmail.com', '$2y$10$MnK5yLjCN5Witq6DcJJgA.UFF/.DozNVxtOMnhSrHdSX.IT3FjFia', 'comum', 'ativo', '2025-10-23 18:07:00', '2025-10-23 18:07:00', 'básico');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `tempos_exercicios`
--
ALTER TABLE `tempos_exercicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tempos_exercicios`
--
ALTER TABLE `tempos_exercicios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_alunos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tempos_exercicios`
--
ALTER TABLE `tempos_exercicios`
  ADD CONSTRAINT `tempos_exercicios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
