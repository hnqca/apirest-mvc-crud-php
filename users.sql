-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16-Jun-2023 às 17:26
-- Versão do servidor: 10.4.25-MariaDB
-- versão do PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `apirest`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(65) NOT NULL,
  `last_name` varchar(65) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Doe', 'john.doe@email.com', '2023-06-13 08:51:30', '2023-06-16 12:22:27'),
(2, 'Maria', 'Oliveira', 'maria.oliveira@email.com', '2023-06-13 08:52:02', NULL),
(3, 'Juan', 'Silveira', 'teste@gmail.com', '2023-06-13 08:52:30', '2023-06-13 16:17:31'),
(8, 'Jubiscleuda', 'Lima', 'teste@gmail.com', '2023-06-13 16:19:18', NULL),
(9, 'Robson', 'Silva', 'robson@gmail.com', '2023-06-13 16:22:45', NULL),
(10, 'irineu', 'soares', 'irineu@gmail.com', '2023-06-13 16:50:43', NULL),
(11, 'lucas', 'soares', 'lucas@gmail.com', '2023-06-13 17:03:49', NULL),
(13, 'Carlos', 'Andrade', 'carlitos22@gmail.com', '2023-06-14 12:34:07', '2023-06-14 12:54:13'),
(14, 'Mario', 'Cardoso', 'mario@email.com', '2023-06-14 12:56:10', '2023-06-14 13:31:58'),
(15, 'Caio', 'Andrade', 'caio.andrade@email.com', '2023-06-16 12:13:27', '2023-06-16 12:17:11');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
