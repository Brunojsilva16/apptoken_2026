-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 31/12/2025 às 01:39
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tb_token`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `pacientes`
--

CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `nome_responsavel` varchar(150) DEFAULT NULL,
  `responsavel_financeiro` varchar(150) DEFAULT NULL,
  `cep` varchar(15) DEFAULT NULL,
  `logradouro` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `origem` varchar(50) DEFAULT NULL,
  `tags` varchar(100) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id_prof` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `registro` varchar(50) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `porcento` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `profissionais`
--

INSERT INTO `profissionais` (`id_prof`, `nome`, `especialidade`, `registro`, `ativo`, `porcento`) VALUES
(100, 'Alexciane Beatriz Vieira Paixão', NULL, NULL, 1, 80),
(102, 'Daniella Paes Barreto Bezerra', NULL, NULL, 1, 70),
(104, 'Talita Pacheco dos Santos', NULL, NULL, 1, 70),
(106, 'Karla Rodrigues', NULL, NULL, 1, 70),
(107, 'Dayane Souza Silva', NULL, NULL, 1, 80),
(108, 'Thaís Leão', NULL, NULL, 1, 80),
(109, 'Luan Henrique da Silva Arruda', NULL, NULL, 1, 70),
(114, 'Tatiane Silva de Moura', NULL, NULL, 1, 70),
(115, 'Jocelma Maria Andrade Marins', NULL, NULL, 1, 70),
(116, 'Giselle Mendonça de Medeiros', NULL, NULL, 1, 70),
(117, 'Iara Cysneiros Silva', NULL, NULL, 1, 80),
(118, 'Andreza Patricia Machado Pontes', NULL, 'CRFa4-11099', 1, 80),
(119, 'Paulo de Tarso Melo', NULL, '0213928', 1, 80),
(120, 'Beatriz Costa Praxedes', NULL, 'CREFITO 1937-6 TO', 1, 70),
(121, 'Ana Cristina Cavalcante Belfort', NULL, 'CRP/02-27328', 1, 70),
(122, 'Adriana Bezerra', NULL, '02/27302', 1, 75),
(124, 'Gabriela Agra', NULL, '02/26670', 1, 70),
(125, 'Stephanny Tavares Ferreira', NULL, '02/25667', 1, 70),
(126, 'Gabriela Grangeiro Dias', NULL, '02/20360', 1, 75),
(127, 'Dalila Dos Reis Gomes', NULL, '02/26111', 1, 70),
(128, 'Vanessa Rodrigues Barbosa', NULL, '02/26563', 1, 70),
(129, 'Rochanne Sonely de Lima Farias', NULL, '02/26032', 1, 80),
(130, 'Monike Maciel Barros Pontes', NULL, '0228888', 1, 70),
(132, 'Pedro Cerqueira Russo', NULL, 'CRM 22086', 1, 92),
(133, 'Aricia Medeiros', NULL, NULL, 0, 80),
(134, 'Amanda Morais Rodrigues', NULL, NULL, 1, 80),
(135, 'Raissa Guerra de Magalhães Melo', NULL, '02/30050', 1, 70),
(136, 'Augusto César Cordeiro Galindo', NULL, '02/22179', 1, 70),
(137, 'Nathália Karla Souza Cavalcanti', NULL, '02/23103', 1, 70),
(138, 'Rodolfo Cunha', NULL, NULL, 1, 70);

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessoes`
--

CREATE TABLE `sessoes` (
  `id_sessao` int(11) NOT NULL,
  `id_token` int(10) UNSIGNED NOT NULL,
  `data_sessao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tokens`
--

CREATE TABLE `tokens` (
  `id_token` int(10) UNSIGNED NOT NULL,
  `token` varchar(20) DEFAULT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `id_prof` int(10) UNSIGNED NOT NULL,
  `paciente` varchar(150) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `nome_resp` varchar(150) DEFAULT NULL,
  `responsavel_f` varchar(150) DEFAULT NULL,
  `origem` varchar(50) DEFAULT NULL,
  `nome_banco` varchar(80) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT 0.00,
  `formapag` varchar(50) DEFAULT NULL,
  `modalidadep` varchar(50) DEFAULT NULL,
  `vencimento` date DEFAULT NULL,
  `statuspag` varchar(20) DEFAULT 'efetuado',
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_a`
--

CREATE TABLE `usuarios_a` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` int(11) DEFAULT 1,
  `status` int(11) DEFAULT 1,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_paciente`);

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id_prof`);

--
-- Índices de tabela `sessoes`
--
ALTER TABLE `sessoes`
  ADD PRIMARY KEY (`id_sessao`),
  ADD KEY `fk_sessao_token` (`id_token`);

--
-- Índices de tabela `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD KEY `idx_token_user` (`id_user`),
  ADD KEY `idx_token_prof` (`id_prof`);

--
-- Índices de tabela `usuarios_a`
--
ALTER TABLE `usuarios_a`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id_prof` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT de tabela `sessoes`
--
ALTER TABLE `sessoes`
  MODIFY `id_sessao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id_token` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios_a`
--
ALTER TABLE `usuarios_a`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `sessoes`
--
ALTER TABLE `sessoes`
  ADD CONSTRAINT `fk_sessoes_token` FOREIGN KEY (`id_token`) REFERENCES `tokens` (`id_token`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `fk_tokens_prof` FOREIGN KEY (`id_prof`) REFERENCES `profissionais` (`id_prof`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tokens_user` FOREIGN KEY (`id_user`) REFERENCES `usuarios_a` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
