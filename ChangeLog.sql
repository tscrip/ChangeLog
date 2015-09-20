-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 20, 2015 at 02:25 PM
-- Server version: 5.1.73
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
--
-- Database: `ChangeLog`
--
-- --------------------------------------------------------
--
-- Table structure for table `Changer`
--

CREATE TABLE IF NOT EXISTS `Changer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Table structure for table `Environment Classifications`
--

CREATE TABLE IF NOT EXISTS `Environment Classifications` (
  `class_id` int(10) NOT NULL AUTO_INCREMENT,
  `name_long` varchar(25) NOT NULL,
  `name_short` varchar(10) NOT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `Environment Classifications`
--

INSERT INTO `Environment Classifications` (`class_id`, `name_long`, `name_short`) VALUES
(1, 'Production', 'prd'),
(2, 'Non Production', 'nonprd'),
(3, 'SandBox', 'sandbox');

-- --------------------------------------------------------

--
-- Table structure for table `Environments`
--

CREATE TABLE IF NOT EXISTS `Environments` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `env_name_full` varchar(25) NOT NULL,
  `env_name_short` varchar(10) NOT NULL,
  `class` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `Environments`
--

INSERT INTO `Environments` (`id`, `env_name_full`, `env_name_short`, `class`) VALUES
(1, 'Production', 'PRD', '1'),
(2, 'Test', 'TST', '2'),
(3, 'Proof of Concept', 'POC', '2');

-- --------------------------------------------------------

--
-- Table structure for table `Event Durations`
--

CREATE TABLE IF NOT EXISTS `Event Durations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `duration` varchar(25) NOT NULL,
  `duration_milliseconds` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `Event Durations`
--

INSERT INTO `Event Durations` (`id`, `duration`, `duration_milliseconds`) VALUES
(1, '30 Minutes', 1800000),
(2, '1 Hour', 3600000),
(3, '1.5 Hours', 5400000),
(4, '2 Hours', 7200000),
(5, '2.5 Hours', 9000000),
(6, '3 Hours', 10800000),
(7, '3.5 Hours', 12600000),
(8, '4 Hours', 14400000),
(9, '4.5 Hours', 16200000),
(10, '5 Hours', 18000000),
(11, '6 Hours', 21600000),
(12, '8 Hours', 28800000),
(13, '10 Hours', 36000000),
(14, '12 Hours', 43200000),
(15, '24 Hours', 86400000);

-- --------------------------------------------------------

--
-- Table structure for table `Events`
--

CREATE TABLE IF NOT EXISTS `Events` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `start_time` datetime NOT NULL,
  `change` mediumtext CHARACTER SET latin1 NOT NULL,
  `changer` varchar(50) CHARACTER SET latin1 NOT NULL,
  `environment` varchar(50) CHARACTER SET latin1 NOT NULL,
  `summary` varchar(100) CHARACTER SET latin1 NOT NULL,
  `system` int(100) NOT NULL,
  `duration` int(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=275 ;

--
-- Table structure for table `Systems`
--

CREATE TABLE IF NOT EXISTS `Systems` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `system_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
