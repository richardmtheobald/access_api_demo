-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 14, 2018 at 09:50 PM
-- Server version: 5.6.37
-- PHP Version: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rtDemo`
--
CREATE DATABASE IF NOT EXISTS `rtDemo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `rtDemo`;

-- --------------------------------------------------------

--
-- Table structure for table `rtAuthTokens`
--

CREATE TABLE `rtAuthTokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `hash` varchar(20) NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL,
  `createDateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rtLoginAttempts`
--

CREATE TABLE `rtLoginAttempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `userId` int(11) UNSIGNED NOT NULL,
  `successful` tinyint(4) NOT NULL,
  `createDateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rtTemps`
--

CREATE TABLE `rtTemps` (
  `id` int(10) UNSIGNED NOT NULL,
  `hash` varchar(20) NOT NULL,
  `postalCode` int(10) UNSIGNED NOT NULL,
  `weather` varchar(255) NOT NULL,
  `tempInF` int(11) NOT NULL,
  `createDateTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rtUsers`
--

CREATE TABLE `rtUsers` (
  `id` int(10) UNSIGNED NOT NULL,
  `hash` varchar(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `postalCode` varchar(20) NOT NULL,
  `createDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rtAuthTokens`
--
ALTER TABLE `rtAuthTokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rtLoginAttempts`
--
ALTER TABLE `rtLoginAttempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loginAttemptLockout` (`userId`,`successful`,`createDateTime`);

--
-- Indexes for table `rtTemps`
--
ALTER TABLE `rtTemps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`),
  ADD UNIQUE KEY `highs` (`postalCode`,`createDateTime`,`tempInF`);

--
-- Indexes for table `rtUsers`
--
ALTER TABLE `rtUsers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `hash` (`hash`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rtAuthTokens`
--
ALTER TABLE `rtAuthTokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rtLoginAttempts`
--
ALTER TABLE `rtLoginAttempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rtTemps`
--
ALTER TABLE `rtTemps`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rtUsers`
--
ALTER TABLE `rtUsers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
