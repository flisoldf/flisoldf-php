-- phpMyAdmin SQL Dump
-- version 3.2.5deb2
-- http://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 25-Mar-2010 às 01:18
-- Versão do servidor: 5.1.41
-- versão do PHP: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `flisol`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `atividade`
--

CREATE TABLE IF NOT EXISTS `atividade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_palestrante` int(11) NOT NULL,
  `id_sala` int(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `dt_cadastro` datetime NOT NULL,
  `situacao` char(1) NOT NULL,
  `qt_horas` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_atividade_usuario1` (`id_palestrante`),
  KEY `fk_atividade_sala1` (`id_sala`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `inscricao`
--

CREATE TABLE IF NOT EXISTS `inscricao` (
  `id_atividade` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `dt_cadastro` datetime NOT NULL,
  `presenca` varchar(45) NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id_atividade`,`id_usuario`),
  KEY `fk_atividade_has_usuario_atividade` (`id_atividade`),
  KEY `fk_atividade_has_usuario_usuario1` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `perfil`
--

CREATE TABLE IF NOT EXISTS `perfil` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `sala`
--

CREATE TABLE IF NOT EXISTS `sala` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  `bloco` varchar(4) DEFAULT NULL,
  `complemento` varchar(45) DEFAULT NULL,
  `qt_pessoas` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `uf`
--

CREATE TABLE IF NOT EXISTS `uf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  `codigo` char(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha` char(32) NOT NULL,
  `dt_cadastro` datetime NOT NULL,
  `cpf` char(14) NOT NULL,
  `cep` char(9) DEFAULT NULL,
  `cidade` varchar(45) DEFAULT NULL,
  `site` varchar(45) DEFAULT NULL,
  `perfil_id` int(11) NOT NULL,
  `uf_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuario_perfil1` (`perfil_id`),
  KEY `fk_usuario_uf1` (`uf_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=146 ;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `atividade`
--
ALTER TABLE `atividade`
  ADD CONSTRAINT `fk_atividade_sala1` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_atividade_usuario1` FOREIGN KEY (`id_palestrante`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `inscricao`
--
ALTER TABLE `inscricao`
  ADD CONSTRAINT `fk_atividade_has_usuario_atividade` FOREIGN KEY (`id_atividade`) REFERENCES `atividade` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_atividade_has_usuario_usuario1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limitadores para a tabela `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_perfil1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usuario_uf1` FOREIGN KEY (`uf_id`) REFERENCES `uf` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
