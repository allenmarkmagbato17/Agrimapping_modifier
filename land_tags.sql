-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2024 at 10:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mylocal-farmers`
--

-- --------------------------------------------------------

--
-- Table structure for table `land_tags`
--

CREATE TABLE `land_tags` (
  `land_tag_id` bigint(20) NOT NULL,
  `land_tag_coord` varchar(100) DEFAULT NULL,
  `land_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `land_tags`
--

INSERT INTO `land_tags` (`land_tag_id`, `land_tag_coord`, `land_id`) VALUES
(10, '[7.8482492, 123.4287291]', 1),
(11, '[7.848115403048589, 123.42870759358087]', 1),
(12, '[7.848223009722592, 123.42902078787994]', 1),
(20, '[7.847192820696856, 123.42951211374351]', 2),
(21, '[7.846710456656804, 123.42956939885329]', 2),
(22, '[7.846795579763371, 123.43035706911274]', 2),
(23, '[7.847249569370643, 123.43037139039019]', 2),
(27, '[7.8482948, 123.429999]', 1),
(28, '[7.803771569469485, 123.41146806674766]', 6),
(29, '[7.8036097, 123.4119999]', 6),
(30, '[7.803527091445935, 123.41128567651943]', 6),
(31, '[7.9527936, 123.5976192]', 8),
(32, '[7.9527936, 123.5976192]', 8),
(33, '[7.9527936, 123.5976192]', 9),
(34, '[7.9527936, 123.5976192]', 9),
(35, '[7.9757312, 123.551744]', 10),
(36, '[8.4803584, 124.6461952]', 11),
(37, '[7.9757312, 123.5484672]', 12),
(38, '[7.9627409, 123.5647786]', 13),
(39, '[7.9462675, 123.5839874]', 14),
(40, '[7.9757312, 123.5484672]', 21),
(41, '[7.9757312, 123.5484672]', 21),
(42, '[7.9627341, 123.5647814]', 26),
(43, '[7.9627341, 123.5647814]', 26),
(44, '[8.011776, 123.568128]', 26),
(45, '[7.9628545, 123.5648819]', 26),
(46, '[7.9628545, 123.5648819]', 25),
(47, '[7.9628545, 123.5648819]', 25),
(48, '[7.9628545, 123.5648819]', 25),
(49, '[7.9628545, 123.5648819]', 25),
(50, '[7.9628545, 123.5648819]', 27),
(51, '[7.9628545, 123.5648819]', 27),
(52, '[7.9757312, 123.5484672]', 27),
(53, '[7.9628215, 123.5648622]', 27),
(54, '[7.9628215, 123.5648622]', 27),
(55, '[7.9460064, 123.5900357]', 28),
(56, '[7.9458283, 123.5887881]', 28),
(57, '[7.9458283, 123.5887881]', 28),
(58, '[7.9458283, 123.5887881]', 28),
(59, '[7.9458283, 123.5887881]', 28),
(60, '[7.9458283, 123.5887881]', 28),
(61, '[8.4803584, 124.6396416]', 28),
(62, '[8.4803584, 124.6396416]', 29),
(63, '[8.4803584, 124.6396416]', 30),
(64, '[8.4803584, 124.6396416]', 30),
(65, '[8.4803584, 124.6396416]', 30),
(66, '[8.4803584, 124.6396416]', 31),
(67, '[8.4803584, 124.6396416]', 32);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `land_tags`
--
ALTER TABLE `land_tags`
  ADD PRIMARY KEY (`land_tag_id`),
  ADD KEY `land_tags_lands_FK` (`land_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `land_tags`
--
ALTER TABLE `land_tags`
  MODIFY `land_tag_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
