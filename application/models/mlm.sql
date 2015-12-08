-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 15, 2015 at 09:45 AM
-- Server version: 5.1.62
-- PHP Version: 5.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mlm`
--

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `mailid` int(11) NOT NULL,
  `path` char(100) NOT NULL,
  `mimetype` char(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lists`
--

CREATE TABLE IF NOT EXISTS `lists` (
  `listid` int(11) NOT NULL AUTO_INCREMENT,
  `listname` char(20) NOT NULL,
  `blurb` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`listid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `lists`
--

INSERT INTO `lists` (`listid`, `listname`, `blurb`) VALUES
(1, 'Insurance', 'For those who are likely to need insurance'),
(2, 'Special', 'Special orders');

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `mailid` int(11) NOT NULL AUTO_INCREMENT,
  `email` char(100) NOT NULL,
  `subject` char(100) NOT NULL,
  `listid` int(11) NOT NULL,
  `status` char(10) NOT NULL,
  `sent` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mailid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE IF NOT EXISTS `subscribers` (
  `email` char(100) NOT NULL,
  `realname` char(100) NOT NULL,
  `mimetype` char(1) NOT NULL,
  `password` char(40) NOT NULL,
  `admin` tinyint(4) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`email`, `realname`, `mimetype`, `password`, `admin`) VALUES
('admin@localhost', 'Administrative User', 'H', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1),
('laura_xt@optusnet.com.au', 'Administrative User', 'H', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sub_lists`
--

CREATE TABLE IF NOT EXISTS `sub_lists` (
  `email` char(100) NOT NULL,
  `listid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sub_lists`
--

INSERT INTO `sub_lists` (`email`, `listid`) VALUES
('zeats@hotmail.com', 1),
('alex@alangman.com', 1),
('aliebaba19@hotmail.com', 1),
('odetta_barry@yahoo.com', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
