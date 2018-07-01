-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  Dim 01 juil. 2018 à 15:04
-- Version du serveur :  5.7.19-log
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `structure`
--
CREATE DATABASE IF NOT EXISTS `structure` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `structure`;

-- --------------------------------------------------------

--
-- Structure de la table `authcookieremember`
--

DROP TABLE IF EXISTS `authcookieremember`;
CREATE TABLE IF NOT EXISTS `authcookieremember` (
  `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ID_user` int(11) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expire` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `authsess`
--

DROP TABLE IF EXISTS `authsess`;
CREATE TABLE IF NOT EXISTS `authsess` (
  `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ID_user` int(11) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `IP` varchar(100) NOT NULL,
  `PHPSESSID` varchar(255) NOT NULL,
  `expire` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `chat`
--

DROP TABLE IF EXISTS `chat`;
CREATE TABLE IF NOT EXISTS `chat` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ID_user` int(10) UNSIGNED NOT NULL,
  `content` varchar(255) NOT NULL,
  `date_post` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `error`
--

DROP TABLE IF EXISTS `error`;
CREATE TABLE IF NOT EXISTS `error` (
  `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `error` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `path` text NOT NULL,
  `extension` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `pdf`
--

DROP TABLE IF EXISTS `pdf`;
CREATE TABLE IF NOT EXISTS `pdf` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `path` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` varchar(255) CHARACTER SET ascii NOT NULL DEFAULT 'Bob',
  `name` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `date_register` datetime NOT NULL,
  `avatar_path` varchar(255) DEFAULT '1',
  `description` text,
  `rank` enum('user','admin','webmaster') NOT NULL DEFAULT 'user',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
