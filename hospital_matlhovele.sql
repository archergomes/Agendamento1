-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 09, 2026 at 07:41 AM
-- Server version: 8.0.31
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_matlhovele`
--

-- --------------------------------------------------------

--
-- Table structure for table `agendamentos`
--

DROP TABLE IF EXISTS `agendamentos`;
CREATE TABLE IF NOT EXISTS `agendamentos` (
  `ID_Agendamento` int NOT NULL AUTO_INCREMENT,
  `ID_Paciente` int NOT NULL,
  `ID_Medico` int DEFAULT NULL,
  `Data_Agendamento` date NOT NULL,
  `Hora_Agendamento` time DEFAULT NULL,
  `Motivo` varchar(255) DEFAULT NULL,
  `Status` enum('Confirmado','Pendente','Cancelado') NOT NULL DEFAULT 'Pendente',
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Agendamento`),
  KEY `ID_Paciente` (`ID_Paciente`),
  KEY `ID_Medico` (`ID_Medico`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `agendamentos`
--

INSERT INTO `agendamentos` (`ID_Agendamento`, `ID_Paciente`, `ID_Medico`, `Data_Agendamento`, `Hora_Agendamento`, `Motivo`, `Status`, `Criado_Em`, `created_at`) VALUES
(1, 1, NULL, '2025-09-23', '10:00:00', 'Consulta de rotina', 'Confirmado', '2025-09-22 06:19:37', '2025-10-23 16:32:24'),
(2, 1, NULL, '2025-09-23', '10:00:00', 'Consulta de rotina', 'Confirmado', '2025-09-22 06:24:07', '2025-10-23 16:32:24'),
(18, 10, 1345365, '2025-10-23', '10:00:00', NULL, 'Cancelado', '2025-10-22 12:06:39', '2025-10-23 16:32:24'),
(16, 10, 1345374, '2025-10-23', '17:00:00', 'Dores de cabeça muito fortes', 'Pendente', '2025-10-22 10:01:49', '2025-10-23 16:32:24'),
(9, 1, 3, '2025-10-22', '13:00:00', 'Dor de cabeça crônica', 'Pendente', '2025-10-20 07:46:40', '2025-10-23 16:32:24'),
(10, 1, 3, '2025-10-26', '08:30:00', 'Problemas de memória', 'Cancelado', '2025-10-20 07:46:40', '2025-10-23 16:32:24'),
(11, 1, 3, '2025-10-27', '16:00:00', 'Exame neurológico', 'Pendente', '2025-10-20 07:46:40', '2025-10-23 16:32:24'),
(12, 1, 4, '2025-10-23', '11:30:00', 'Dor nas costas', 'Pendente', '2025-10-20 07:46:40', '2025-10-23 16:32:24'),
(13, 1, 4, '2025-10-28', '14:30:00', 'Lesão no joelho', 'Pendente', '2025-10-20 07:46:40', '2025-10-23 16:32:24'),
(14, 1, 4, '2025-10-29', '09:00:00', 'Fisioterapia', 'Pendente', '2025-10-20 07:46:40', '2025-10-23 16:32:24'),
(15, 9, 1345360, '2025-10-24', '08:30:00', NULL, 'Pendente', '2025-10-22 10:00:31', '2025-10-23 16:32:24'),
(19, 9, 1345352, '2025-10-29', '09:30:00', NULL, 'Pendente', '2025-10-24 06:05:00', '2025-10-24 06:05:00'),
(21, 1234568, 1345352, '2026-08-04', '11:00:00', 'Dores de cabeça', 'Cancelado', '2026-08-02 14:46:33', '2026-08-02 14:46:33'),
(22, 1234568, 1345353, '2026-08-06', '15:31:00', 'testesaaaa', 'Cancelado', '2026-08-02 15:03:14', '2026-08-02 15:03:14'),
(23, 1234568, 1345352, '2026-08-30', '11:00:00', NULL, 'Cancelado', '2026-08-26 10:26:06', '2026-08-26 10:26:06'),
(24, 1234568, 1345352, '2026-09-18', '11:00:00', NULL, 'Cancelado', '2026-09-09 05:59:50', '2026-09-09 05:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `configuracoes`
--

DROP TABLE IF EXISTS `configuracoes`;
CREATE TABLE IF NOT EXISTS `configuracoes` (
  `ID_Configuracao` int NOT NULL AUTO_INCREMENT,
  `Chave` varchar(100) NOT NULL,
  `Valor` text NOT NULL,
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Configuracao`),
  UNIQUE KEY `Chave` (`Chave`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `configuracoes`
--

INSERT INTO `configuracoes` (`ID_Configuracao`, `Chave`, `Valor`, `Criado_Em`) VALUES
(1, 'max_consultas_dia', '20', '2025-09-20 22:24:59'),
(2, 'horario_funcionamento', '07:30-16:30', '2025-09-20 22:24:59'),
(3, 'hospital_name', 'Hospital Público de Matlhovelea', '2026-08-03 09:02:36'),
(4, 'hospital_address', 'Av. 25 de Setembro, Maputo', '2026-08-03 09:02:36'),
(5, 'hospital_phone', '+258 84 123 4567', '2026-08-03 09:02:36'),
(6, 'hospital_email', 'contato@hospitalmatlhovele.mz', '2026-08-03 09:02:36'),
(7, 'hospital_description', 'Hospital público dedicado ao cuidado de qualidade em Maputo.', '2026-08-03 09:02:36');

-- --------------------------------------------------------

--
-- Table structure for table `consultas`
--

DROP TABLE IF EXISTS `consultas`;
CREATE TABLE IF NOT EXISTS `consultas` (
  `ID_Consulta` int NOT NULL AUTO_INCREMENT,
  `ID_Paciente` int NOT NULL,
  `ID_Medico` int NOT NULL,
  `Data_Consulta` datetime NOT NULL,
  `Motivo` text,
  `Sala` varchar(50) DEFAULT NULL,
  `Status` enum('Agendada','Concluida','Cancelada') DEFAULT 'Agendada',
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Consulta`),
  KEY `ID_Paciente` (`ID_Paciente`),
  KEY `ID_Medico` (`ID_Medico`),
  KEY `idx_consulta_data` (`Data_Consulta`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `consultas`
--

INSERT INTO `consultas` (`ID_Consulta`, `ID_Paciente`, `ID_Medico`, `Data_Consulta`, `Motivo`, `Sala`, `Status`, `Criado_Em`) VALUES
(1, 1, 1, '2025-09-16 10:00:00', 'Consulta de rotina', 'Sala 101', 'Agendada', '2025-09-20 22:24:59'),
(2, 2, 2, '2025-09-17 09:30:00', 'Avaliação pediátrica', 'Sala 102', 'Agendada', '2025-09-20 22:24:59');

-- --------------------------------------------------------

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
CREATE TABLE IF NOT EXISTS `departamentos` (
  `ID_Departamento` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(100) NOT NULL,
  `Chefe_ID` int DEFAULT NULL,
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Departamento`),
  UNIQUE KEY `Nome` (`Nome`),
  KEY `fk_departamento_chefe` (`Chefe_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departamentos`
--

INSERT INTO `departamentos` (`ID_Departamento`, `Nome`, `Chefe_ID`, `Criado_Em`) VALUES
(1, 'Emergência', 1, '2025-09-20 22:24:59'),
(2, 'Clínica Geral', NULL, '2025-09-20 22:24:59');

-- --------------------------------------------------------

--
-- Table structure for table `especialidades`
--

DROP TABLE IF EXISTS `especialidades`;
CREATE TABLE IF NOT EXISTS `especialidades` (
  `ID_Especialidade` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(100) NOT NULL,
  `Descricao` text,
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Especialidade`),
  UNIQUE KEY `Nome` (`Nome`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `especialidades`
--

INSERT INTO `especialidades` (`ID_Especialidade`, `Nome`, `Descricao`, `Criado_Em`) VALUES
(1, 'Cardiologia', 'Especialidade em doenças do coração', '2025-09-20 22:24:59'),
(2, 'Pediatria', 'Cuidados médicos para crianças', '2025-09-20 22:24:59'),
(3, 'Ortopedia', 'Tratamento de ossos e articulações', '2025-09-20 22:24:59');

-- --------------------------------------------------------

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
CREATE TABLE IF NOT EXISTS `horarios` (
  `ID_Horario` int NOT NULL AUTO_INCREMENT,
  `ID_Medico` int NOT NULL,
  `Dia_Semana` enum('Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo') NOT NULL,
  `Hora_Inicio` time NOT NULL,
  `Hora_Fim` time NOT NULL,
  `Intervalo_Inicio` time DEFAULT NULL,
  `Intervalo_Fim` time DEFAULT NULL,
  `Sala` varchar(50) DEFAULT NULL,
  `Duracao_Consulta` int DEFAULT '30',
  `Data_Inicio_Vigencia` date DEFAULT NULL,
  `Data_Fim_Vigencia` date DEFAULT NULL,
  `Observacoes` text,
  `Status` varchar(20) DEFAULT 'ativo',
  PRIMARY KEY (`ID_Horario`),
  KEY `ID_Medico` (`ID_Medico`)
) ENGINE=MyISAM AUTO_INCREMENT=249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `horarios`
--

INSERT INTO `horarios` (`ID_Horario`, `ID_Medico`, `Dia_Semana`, `Hora_Inicio`, `Hora_Fim`, `Intervalo_Inicio`, `Intervalo_Fim`, `Sala`, `Duracao_Consulta`, `Data_Inicio_Vigencia`, `Data_Fim_Vigencia`, `Observacoes`, `Status`) VALUES
(2, 1, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(3, 2, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(238, 1345425, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(5, 1, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(6, 1, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(7, 1, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(8, 1, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(9, 1, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(10, 1, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(11, 1, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(12, 1, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(13, 1, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(14, 2, 'Terça', '09:00:00', '17:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(15, 2, 'Quinta', '09:00:00', '17:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(16, 1345352, 'Segunda', '10:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(17, 1345352, 'Terça', '10:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(18, 1345352, 'Quarta', '10:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(19, 1345354, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(20, 1345354, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(21, 1345354, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(22, 1345354, 'Sábado', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(23, 1345352, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(24, 1345352, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(25, 1345352, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(26, 1345352, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(27, 1345352, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(28, 1345353, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(29, 1345353, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(30, 1345353, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(31, 1345353, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(32, 1345353, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(33, 1345354, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(34, 1345354, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(35, 1345354, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(36, 1345354, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(37, 1345354, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(38, 1345355, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(39, 1345355, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(40, 1345355, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(41, 1345355, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(42, 1345355, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(43, 1345356, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(44, 1345356, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(45, 1345356, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(46, 1345356, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(47, 1345356, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(48, 1345357, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(49, 1345357, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(50, 1345357, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(51, 1345357, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(52, 1345357, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(53, 1345358, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(54, 1345358, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(55, 1345358, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(56, 1345358, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(57, 1345358, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(58, 1345359, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(59, 1345359, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(60, 1345359, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(61, 1345359, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(62, 1345359, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(63, 1345360, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(64, 1345360, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(65, 1345360, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(66, 1345360, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(67, 1345360, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(68, 1345361, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(69, 1345361, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(70, 1345361, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(71, 1345361, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(72, 1345361, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(73, 1345362, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(74, 1345362, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(75, 1345362, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(76, 1345362, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(77, 1345362, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(78, 1345363, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(79, 1345363, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(80, 1345363, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(81, 1345363, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(82, 1345363, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(83, 1345364, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(84, 1345364, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(85, 1345364, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(86, 1345364, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(87, 1345364, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(88, 1345365, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(89, 1345365, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(90, 1345365, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(91, 1345365, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(92, 1345365, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(93, 1345366, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(94, 1345366, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(95, 1345366, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(96, 1345366, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(97, 1345366, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(98, 1345367, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(99, 1345367, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(100, 1345367, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(101, 1345367, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(102, 1345367, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(103, 1345368, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(104, 1345368, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(105, 1345368, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(106, 1345368, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(107, 1345368, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(108, 1345369, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(109, 1345369, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(110, 1345369, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(111, 1345369, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(112, 1345369, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(113, 1345370, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(114, 1345370, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(115, 1345370, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(116, 1345370, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(117, 1345370, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(118, 1345371, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(119, 1345371, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(120, 1345371, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(121, 1345371, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(122, 1345371, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(123, 1345352, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(124, 1345352, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(125, 1345352, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(126, 1345352, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(127, 1345352, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(128, 1345353, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(129, 1345353, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(130, 1345353, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(131, 1345353, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(132, 1345353, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(133, 1345354, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(134, 1345354, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(135, 1345354, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(136, 1345354, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(137, 1345354, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(138, 1345355, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(139, 1345355, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(140, 1345355, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(141, 1345355, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(142, 1345355, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(143, 1345356, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(144, 1345356, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(145, 1345356, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(146, 1345356, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(147, 1345356, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(148, 1345357, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(149, 1345357, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(150, 1345357, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(151, 1345357, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(152, 1345357, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(153, 1345358, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(154, 1345358, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(155, 1345358, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(156, 1345358, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(157, 1345358, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(158, 1345359, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(159, 1345359, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(160, 1345359, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(161, 1345359, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(162, 1345359, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(163, 1345360, 'Segunda', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(164, 1345360, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(165, 1345360, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(166, 1345360, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(167, 1345360, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(168, 1345361, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(169, 1345361, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(170, 1345361, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(171, 1345361, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(172, 1345361, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(173, 1345362, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(174, 1345362, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(175, 1345362, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(176, 1345362, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(177, 1345362, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(178, 1345363, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(179, 1345363, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(180, 1345363, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(181, 1345363, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(182, 1345363, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(183, 1345364, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(184, 1345364, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(185, 1345364, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(186, 1345364, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(187, 1345364, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(188, 1345365, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(189, 1345365, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(190, 1345365, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(191, 1345365, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(192, 1345365, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(193, 1345366, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(194, 1345366, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(195, 1345366, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(196, 1345366, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(197, 1345366, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(198, 1345367, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(199, 1345367, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(200, 1345367, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(201, 1345367, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(202, 1345367, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(203, 1345368, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(204, 1345368, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(205, 1345368, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(206, 1345368, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(207, 1345368, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(208, 1345369, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(209, 1345369, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(210, 1345369, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(211, 1345369, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(212, 1345369, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(213, 1345370, 'Segunda', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(214, 1345370, 'Terça', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(215, 1345370, 'Quarta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(216, 1345370, 'Quinta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(217, 1345370, 'Sexta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(218, 1345371, 'Segunda', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(219, 1345371, 'Terça', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(220, 1345371, 'Quarta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(221, 1345371, 'Quinta', '09:00:00', '13:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(222, 1345371, 'Sexta', '15:00:00', '19:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(223, 1345372, 'Segunda', '09:30:00', '12:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(224, 1345372, 'Terça', '15:30:00', '18:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(225, 1345372, 'Quarta', '09:30:00', '12:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(226, 1345372, 'Quinta', '15:30:00', '18:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(227, 1345372, 'Sexta', '09:30:00', '12:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(228, 1345373, 'Segunda', '15:30:00', '18:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(229, 1345373, 'Terça', '09:30:00', '12:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(230, 1345373, 'Quarta', '15:30:00', '18:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(231, 1345373, 'Quinta', '09:30:00', '12:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(232, 1345373, 'Sexta', '15:30:00', '18:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(233, 1345374, 'Segunda', '09:30:00', '12:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(234, 1345374, 'Terça', '15:30:00', '18:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(235, 1345374, 'Quarta', '09:30:00', '12:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(236, 1345374, 'Quinta', '15:30:00', '18:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(237, 1345374, 'Sexta', '09:30:00', '12:30:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(239, 1345425, 'Segunda', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(240, 1345425, 'Terça', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(241, 1345425, 'Terça', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(242, 1345425, 'Quarta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(243, 1345425, 'Quarta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(244, 1345425, 'Quinta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(245, 1345425, 'Quinta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(246, 1345425, 'Sexta', '08:00:00', '12:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(247, 1345425, 'Sexta', '14:00:00', '18:00:00', NULL, NULL, NULL, 30, NULL, NULL, NULL, 'ativo'),
(248, 1345352, 'Domingo', '10:00:00', '13:00:00', '11:30:00', '11:45:00', 'Sala 1', 20, '2026-08-20', '2026-08-31', 'Estará a fazer horas extras', 'ativo');

-- --------------------------------------------------------

--
-- Table structure for table `medicos`
--

DROP TABLE IF EXISTS `medicos`;
CREATE TABLE IF NOT EXISTS `medicos` (
  `ID_Medico` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(50) NOT NULL,
  `Sobrenome` varchar(50) NOT NULL,
  `Especialidade` varchar(100) NOT NULL,
  `ID_Especialidade` int NOT NULL,
  `ID_Departamento` int DEFAULT NULL,
  `Telefone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `Data_Inicio` date DEFAULT NULL,
  `Numero_Licenca` varchar(50) DEFAULT NULL,
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_Usuario` int DEFAULT NULL,
  PRIMARY KEY (`ID_Medico`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `Numero_Licenca` (`Numero_Licenca`),
  KEY `idx_medico_email` (`Email`),
  KEY `fk_medico_especialidade` (`ID_Especialidade`),
  KEY `fk_medico_departamento` (`ID_Departamento`)
) ENGINE=MyISAM AUTO_INCREMENT=1345428 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medicos`
--

INSERT INTO `medicos` (`ID_Medico`, `Nome`, `Sobrenome`, `Especialidade`, `ID_Especialidade`, `ID_Departamento`, `Telefone`, `Email`, `Data_Inicio`, `Numero_Licenca`, `Criado_Em`, `ID_Usuario`) VALUES
(1345352, 'Archer', 'Gomes', 'Medicina Geral', 0, NULL, '+258871914705', 'archer@gmail.com', NULL, '8739275023', '2025-10-18 20:09:23', NULL),
(1345427, 'Medico', '4', 'Cardiologia', 1, NULL, '+258873443438', 'medico4@hospital.com', NULL, 'LIC-034', '2026-08-21 08:02:13', 14),
(1345426, 'Medico', '1', 'Cardiologia', 1, NULL, '+258843423232', 'medico1@hospital.com', NULL, 'LIC-0032', '2026-08-21 09:47:04', NULL),
(1345390, 'Paula', 'Lima', 'Ortopedia', 4, 2, '+258846789015', 'paula.lima@hospital.com', '2020-03-12', 'LIC-036', '2025-10-22 09:24:08', NULL),
(1345391, 'Quim', 'Mota', 'Ortopedia', 4, 2, '+258847890126', 'quim.mota@hospital.com', '2019-11-28', 'LIC-037', '2025-10-22 09:24:08', NULL),
(1345392, 'Rita', 'Nobre', 'Ortopedia', 4, 2, '+258848901237', 'rita.nobre@hospital.com', '2021-05-16', 'LIC-038', '2025-10-22 09:24:08', NULL),
(1345393, 'Sergio', 'Oliveira', 'Ortopedia', 4, 2, '+258849012348', 'sergio.oliveira@hospital.com', '2018-08-04', 'LIC-039', '2025-10-22 09:24:08', NULL),
(1345394, 'Teresa', 'Pinto', 'Ortopedia', 4, 2, '+258840123459', 'teresa.pinto@hospital.com', '2023-04-20', 'LIC-040', '2025-10-22 09:24:08', NULL),
(1345395, 'Ursula', 'Queiroz', 'Ginecologia', 5, 1, '+258841234571', 'ursula.queiroz@hospital.com', '2020-12-07', 'LIC-041', '2025-10-22 09:24:08', NULL),
(1345396, 'Victor', 'Ramos', 'Ginecologia', 5, 1, '+258842345682', 'victor.ramos@hospital.com', '2019-09-25', 'LIC-042', '2025-10-22 09:24:08', NULL),
(1345397, 'Wanda', 'Santos', 'Ginecologia', 5, 1, '+258843456783', 'wanda.santos@hospital.com', '2022-07-13', 'LIC-043', '2025-10-22 09:24:08', NULL),
(1345398, 'Xavier', 'Tavares', 'Ginecologia', 5, 1, '+258844567894', 'xavier.tavares@hospital.com', '2017-10-19', 'LIC-044', '2025-10-22 09:24:08', NULL),
(1345399, 'Yara', 'Urbano', 'Ginecologia', 5, 1, '+258845678905', 'yara.urbano@hospital.com', '2021-03-31', 'LIC-045', '2025-10-22 09:24:08', NULL),
(1345400, 'Zeca', 'Vasco', 'Ginecologia', 5, 1, '+258846789016', 'zeca.vasco@hospital.com', '2018-01-08', 'LIC-046', '2025-10-22 09:24:08', NULL),
(1345411, 'Katia', 'Guerreiro', 'Neurologia', 6, 2, '+258847890128', 'katia.guerreiro@hospital.com', '2018-12-27', 'LIC-057', '2025-10-22 09:24:08', NULL),
(1345412, 'Luis', 'Horta', 'Neurologia', 6, 2, '+258848901239', 'luis.horta@hospital.com', '2020-03-04', 'LIC-058', '2025-10-22 09:24:08', NULL),
(1345413, 'Marta', 'Inacio', 'Neurologia', 6, 2, '+258849012350', 'marta.inacio@hospital.com', '2023-01-15', 'LIC-059', '2025-10-22 09:24:08', NULL),
(1345414, 'Nuno', 'Junqueira', 'Neurologia', 6, 2, '+258840123461', 'nuno.junqueira@hospital.com', '2019-06-22', 'LIC-060', '2025-10-22 09:24:08', NULL),
(1345416, 'Paulo', 'Lencastre', 'Cirurgia Geral', 7, 2, '+258842345684', 'paulo.lencastre@hospital.com', '2017-11-18', 'LIC-062', '2025-10-22 09:24:08', NULL),
(1345417, 'Quintino', 'Macedo', 'Cirurgia Geral', 7, 2, '+258843456785', 'quintino.macedo@hospital.com', '2022-07-26', 'LIC-063', '2025-10-22 09:24:08', NULL),
(1345418, 'Raul', 'Neto', 'Cirurgia Geral', 7, 2, '+258844567896', 'raul.neto@hospital.com', '2019-01-03', 'LIC-064', '2025-10-22 09:24:08', NULL),
(1345419, 'Sara', 'Oliveira', 'Cirurgia Geral', 7, 2, '+258845678907', 'sara.oliveira@hospital.com', '2020-09-10', 'LIC-065', '2025-10-22 09:24:08', NULL),
(1345420, 'Tomas', 'Pires', 'Cirurgia Geral', 7, 2, '+258846789018', 'tomas.pires@hospital.com', '2018-05-17', 'LIC-066', '2025-10-22 09:24:08', NULL),
(1345421, 'Ursula', 'Quintela', 'Cirurgia Geral', 7, 2, '+258847890129', 'ursula.quintela@hospital.com', '2021-12-24', 'LIC-067', '2025-10-22 09:24:08', NULL),
(1345422, 'Vasco', 'Ribeiro', 'Cirurgia Geral', 7, 2, '+258848901240', 'vasco.ribeiro@hospital.com', '2017-02-09', 'LIC-068', '2025-10-22 09:24:08', NULL),
(1345423, 'Wilma', 'Santos', 'Cirurgia Geral', 7, 2, '+258849012351', 'wilma.santos@hospital.com', '2023-03-18', 'LIC-069', '2025-10-22 09:24:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pacientes`
--

DROP TABLE IF EXISTS `pacientes`;
CREATE TABLE IF NOT EXISTS `pacientes` (
  `ID_Paciente` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(50) NOT NULL,
  `Sobrenome` varchar(50) NOT NULL,
  `Data_Nascimento` date NOT NULL,
  `Genero` enum('Masculino','Feminino','Outro') NOT NULL,
  `Endereco` varchar(255) DEFAULT NULL,
  `Telefone` varchar(20) NOT NULL,
  `Contato_Emergencia` varchar(100) DEFAULT NULL,
  `BI` varchar(50) NOT NULL,
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_Usuario` int DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID_Paciente`),
  UNIQUE KEY `BI` (`BI`),
  KEY `idx_paciente_bi` (`BI`)
) ENGINE=MyISAM AUTO_INCREMENT=1234573 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pacientes`
--

INSERT INTO `pacientes` (`ID_Paciente`, `Nome`, `Sobrenome`, `Data_Nascimento`, `Genero`, `Endereco`, `Telefone`, `Contato_Emergencia`, `BI`, `Criado_Em`, `ID_Usuario`, `email`) VALUES
(1, 'Teste', 'Usuario', '1990-01-01', 'Masculino', 'Rua Exemplo, Maputo', '+258 87 1914380', '+258 84 9999999', '123456789TEST', '2025-09-20 22:24:59', NULL, NULL),
(8, 'Archer', 'Gomes', '2003-05-01', 'Masculino', NULL, '+258871914705', NULL, '257985229875', '2025-10-08 06:52:24', NULL, NULL),
(10, 'Janna', 'Sheinil', '2004-05-04', 'Feminino', NULL, '865757465', NULL, '6275628562', '2025-10-20 07:04:12', NULL, NULL),
(1234567, 'Maria', 'Santos', '1995-05-15', 'Feminino', 'Maputo', '+258841234567', NULL, '', '2025-10-26 17:30:55', NULL, NULL),
(1234569, 'Testando', '', '2000-01-04', 'Masculino', 'Matola', '+258844343844', NULL, '837483743s', '2026-08-03 07:59:25', NULL, 'teste@hospital.com'),
(1234572, 'paciente', '', '2012-06-09', 'Masculino', 'Matola', '+258875757577', NULL, '24343434s', '2026-09-09 07:20:39', NULL, 'paciente@hospital.com'),
(1234571, 'Archer', 'Gomes', '2026-08-01', 'Masculino', 'Matola', '+258841234567', NULL, '837482343s', '2026-08-27 08:37:09', NULL, 'gomesarcher3@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `recuperacao_senha`
--

DROP TABLE IF EXISTS `recuperacao_senha`;
CREATE TABLE IF NOT EXISTS `recuperacao_senha` (
  `ID_Recuperacao` int NOT NULL AUTO_INCREMENT,
  `ID_Usuario` int NOT NULL,
  `Token` varchar(255) NOT NULL,
  `Expiracao` datetime NOT NULL,
  `Criado_Em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Recuperacao`),
  KEY `ID_Usuario` (`ID_Usuario`),
  KEY `Token` (`Token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `relatorios`
--

DROP TABLE IF EXISTS `relatorios`;
CREATE TABLE IF NOT EXISTS `relatorios` (
  `ID_Relatorio` int NOT NULL AUTO_INCREMENT,
  `Tipo_Relatorio` enum('Atendimentos','Financeiro','Desempenho') NOT NULL,
  `Data_Geracao` date NOT NULL,
  `Conteudo` json DEFAULT NULL,
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Relatorio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `secretarios`
--

DROP TABLE IF EXISTS `secretarios`;
CREATE TABLE IF NOT EXISTS `secretarios` (
  `ID_Secretario` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(50) NOT NULL,
  `Sobrenome` varchar(50) NOT NULL,
  `Telefone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `Cargo` varchar(100) DEFAULT 'Secretário',
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_Usuario` int DEFAULT NULL,
  PRIMARY KEY (`ID_Secretario`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=MyISAM AUTO_INCREMENT=2147483648 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `secretarios`
--

INSERT INTO `secretarios` (`ID_Secretario`, `Nome`, `Sobrenome`, `Telefone`, `Email`, `Cargo`, `Criado_Em`, `ID_Usuario`) VALUES
(79573352, 'Archer', 'Gomess', '+258873873823', 'archer3@gmail.com', 'Secretário', '2025-10-08 15:33:19', NULL),
(2147483647, 'Secretario', 'Archer', '+258841234567', 'sarcher@hospital.com', 'Secretário', '2026-08-19 11:21:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID_Usuario` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(100) NOT NULL,
  `Senha` varchar(255) NOT NULL,
  `Tipo_Usuario` enum('Paciente','Medico','Secretario','Admin') NOT NULL,
  `ID_Referencia` int NOT NULL,
  `Criado_Em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID_Usuario`),
  UNIQUE KEY `Email` (`Email`),
  KEY `idx_usuario_email` (`Email`),
  KEY `fk_usuario_paciente` (`ID_Referencia`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`ID_Usuario`, `Email`, `Senha`, `Tipo_Usuario`, `ID_Referencia`, `Criado_Em`) VALUES
(1, 'teste@exemplo.com', '$2y$10$exemplo_senha_hash', 'Paciente', 1, '2025-09-20 22:24:59'),
(2, 'joao.silva@hospital.com', '$2y$10$exemplo_senha_hash', 'Medico', 1, '2025-09-20 22:24:59'),
(3, 'ana.costa@hospital.com', '$2y$10$exemplo_senha_hash', 'Secretario', 1, '2025-09-20 22:24:59'),
(4, 'admin@hospital.com', '$2y$12$3xzlUIGtUBRonFs1FyGkseni7xoZ6fNA89/UQHUoRnNTIyGOQuOYy', 'Admin', 0, '2025-09-20 22:24:59'),
(7, 'archer3@gmail.com', '$2y$10$qYxdPnuP95KRZMyQ0BWhk.ebWmxnrFliHIMY5D7h3m853GAJ3tpTG', 'Paciente', 8, '2025-10-08 06:52:24'),
(6, 'beatriz.lima@exemplo.com', 'TEMPORARY_HASH', 'Paciente', 2, '2025-09-22 06:32:02'),
(8, 'archergomes@gmail.com', '$2y$10$dTCWz43cN/71ydI09YgThugPmGWl9pEUyyfQlB4l/i3/oZF2.BwRO', 'Paciente', 9, '2025-10-20 05:29:18'),
(9, 'janna3@gmail.com', '$2y$10$VTWuaEKjIK1q74GfJNpL.OAnJb9.jS1aBhBnNMFtrErSIOkI2R2tu', 'Paciente', 10, '2025-10-20 07:04:12'),
(15, 'sec3@hospital.com', '$2y$10$ATTfTuPckxNzCsuuyNoBA.aVmENH2DVf9yGX/ynutfT37eQTTMCO6', 'Secretario', 6765776, '2026-09-09 06:45:56'),
(11, 'sec@hospital.com', '$2y$10$W3dhuqFb856OZD/re2BOu.xWXaTZH5vS1ziFF3qiJ6.rX9IczQS1.', 'Secretario', 2147483647, '2026-08-03 08:26:44'),
(12, 'medico@hospital.com', '$2y$10$fZ8ChOga/PAmZbPqfGWr8e7DikaKr73.EMRcuwI0M11xby6.0lewC', 'Medico', 1345425, '2026-08-18 06:38:35'),
(13, 'sarcher@hospital.com', '$2y$10$mHjBz8Pozze6pp0QjVHoteQSJaYq.D2arV5MdRcrQ7YIFyCHLAWXm', 'Secretario', 2147483647, '2026-08-19 11:21:11'),
(14, 'medico4@hospital.com', '$2y$10$URw7.bDuKKrelEZvIQ3enutn4kjhGO7wvKl58/Un93prKKR/U29uq', 'Medico', 1345427, '2026-08-21 08:02:13');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
