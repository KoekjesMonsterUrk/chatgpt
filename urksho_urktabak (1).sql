-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: tb-nl01-linweb564.srv.teamblue-ops.net:3306
-- Gegenereerd op: 23 jun 2024 om 22:51
-- Serverversie: 8.0.36-28
-- PHP-versie: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `urksho_urktabak`
--
CREATE DATABASE IF NOT EXISTS `urksho_urktabak` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `urksho_urktabak`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bestellingen`
--

CREATE TABLE `bestellingen` (
  `id` int NOT NULL,
  `datum` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Nieuw','Besteld','Ontvangen') NOT NULL,
  `totaalprijs` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `bestellingen`
--

INSERT INTO `bestellingen` (`id`, `datum`, `status`, `totaalprijs`) VALUES
(2, '2024-05-30 00:28:29', 'Ontvangen', 1241.00),
(7, '2024-05-30 18:52:00', 'Ontvangen', 1698.60),
(8, '2024-06-01 16:23:03', 'Ontvangen', 1356.00),
(9, '2024-06-07 21:17:55', 'Ontvangen', 2642.20),
(13, '2024-06-22 12:49:11', 'Besteld', 7579.80),
(14, '2024-06-23 19:02:48', 'Besteld', 132.00);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bestelling_producten`
--

CREATE TABLE `bestelling_producten` (
  `id` int NOT NULL,
  `bestelling_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `bestelling_producten`
--

INSERT INTO `bestelling_producten` (`id`, `bestelling_id`, `product_id`, `quantity`) VALUES
(94, 2, 3, 4),
(95, 2, 11, 2),
(96, 2, 19, 1),
(97, 2, 20, 2),
(98, 2, 30, 5),
(128, 7, 3, 2),
(129, 7, 15, 1),
(130, 7, 19, 1),
(131, 7, 20, 1),
(132, 7, 21, 10),
(133, 7, 22, 1),
(134, 7, 24, 2),
(135, 7, 25, 2),
(136, 7, 27, 5),
(137, 7, 29, 1),
(138, 8, 30, 12),
(171, 9, 3, 4),
(172, 9, 11, 1),
(173, 9, 16, 2),
(174, 9, 19, 3),
(175, 9, 20, 12),
(176, 9, 21, 1),
(177, 9, 24, 4),
(178, 9, 31, 2),
(179, 9, 34, 1),
(342, 13, 3, 6),
(343, 13, 10, 2),
(344, 13, 11, 10),
(345, 13, 13, 2),
(346, 13, 14, 17),
(347, 13, 16, 1),
(348, 13, 17, 1),
(349, 13, 18, 1),
(350, 13, 19, 5),
(351, 13, 20, 10),
(352, 13, 21, 8),
(353, 13, 24, 13),
(354, 13, 26, 1),
(355, 13, 28, 1),
(356, 13, 30, 7),
(357, 13, 31, 6),
(358, 13, 35, 2),
(359, 13, 36, 4),
(360, 13, 37, 2),
(361, 13, 38, 1),
(362, 14, 25, 1),
(363, 14, 27, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `klantnaam` varchar(255) NOT NULL,
  `totaalprijs` decimal(10,2) NOT NULL,
  `status` enum('Onderweg','Bestellen','Afgerond betaald','Afgehaald nog niet betaald') NOT NULL,
  `extra_info` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `orders`
--

INSERT INTO `orders` (`id`, `klantnaam`, `totaalprijs`, `status`, `extra_info`, `created_at`) VALUES
(4, 'Marjo', 85.00, 'Afgerond betaald', '1 Juni informeren dat hij binnen is. Via snapchat', '2024-05-28 22:25:02'),
(5, 'Robert Jan de Vries', 85.00, 'Afgerond betaald', '3 Juni aan bert meegeven naar Visco. Tikkie', '2024-05-28 22:26:23'),
(6, 'Marjo', 80.00, 'Afgerond betaald', '', '2024-05-28 22:27:38'),
(7, 'Bert de Vries', 85.00, 'Afgerond betaald', '', '2024-05-28 22:28:10'),
(8, 'Meindert Jan', 85.00, 'Afgerond betaald', 'Via snapchat informeren dat hij binnen is.', '2024-05-28 22:29:07'),
(9, 'Anne Gre Loosman', 80.00, 'Afgerond betaald', '', '2024-05-28 22:30:33'),
(10, 'Minne Buis', 1150.00, 'Afgerond betaald', '1150 euro afgesproken', '2024-05-28 22:31:56'),
(12, 'Christiaan Korf', 85.00, 'Afgerond betaald', 'Meenemen naar de zaak wanneer aanwezig', '2024-05-28 22:36:32'),
(13, 'Minne Buis', 250.00, 'Afgerond betaald', 'Afgesproken 250 euro\r\n', '2024-05-29 22:18:47'),
(16, 'Luuk van Urk', 120.00, 'Afgerond betaald', 'Collega abbe', '2024-05-30 01:20:41'),
(18, 'Hendrik Brands', 390.00, 'Afgerond betaald', 'Via snapchat informeren', '2024-05-30 12:50:10'),
(19, 'Fokke buurman', 125.00, 'Afgerond betaald', '', '2024-05-30 13:34:39'),
(20, 'John Vd Broek', 200.00, 'Onderweg', '', '2024-05-30 15:08:41'),
(23, 'Bertus de groene', 120.00, 'Afgerond betaald', 'Het rif 14', '2024-05-30 15:38:52'),
(26, 'Louw looman', 135.00, 'Afgerond betaald', '', '2024-05-31 09:44:35'),
(27, 'Jacob bakker', 160.00, 'Afgerond betaald', '', '2024-05-31 12:41:17'),
(28, 'Cornelis', 135.00, 'Afgerond betaald', '', '2024-06-01 14:43:00'),
(29, 'Jj vd meulen', 135.00, 'Afgerond betaald', '', '2024-06-01 14:48:37'),
(30, 'Albert loosman', 270.00, 'Afgerond betaald', '', '2024-06-01 17:50:17'),
(31, 'Peter oromar', 135.00, 'Afgerond betaald', '', '2024-06-01 17:51:15'),
(32, 'Minne', 955.00, 'Afgerond betaald', '', '2024-06-02 12:18:34'),
(33, 'Johny ruizendaal', 110.00, 'Afgerond betaald', 'Is al betaald\r\nLijkant 38', '2024-06-03 09:15:25'),
(34, 'Gerben vd berg', 125.00, 'Afgerond betaald', '', '2024-06-03 10:01:12'),
(35, 'Lubbert pasterkamp', 125.00, 'Afgerond betaald', 'Schelpenhoek 29', '2024-06-03 13:30:36'),
(36, 'Peter Pasterkamp buur', 125.00, 'Afgerond betaald', '', '2024-06-03 14:10:32'),
(37, 'Lubbert van keulen', 425.00, 'Afgerond betaald', 'Waterhoen 34', '2024-06-03 15:41:10'),
(38, 'Jan de vries', 95.00, 'Afgerond betaald', '', '2024-06-04 14:24:54'),
(39, 'Tiede van jansen', 135.00, 'Afgerond betaald', '', '2024-06-04 16:11:21'),
(40, 'Albert kramer buur', 135.00, 'Afgerond betaald', '', '2024-06-05 10:44:21'),
(41, 'Teun kramer', 160.00, 'Afgerond betaald', 'Waterhoen 36', '2024-06-05 15:05:47'),
(42, 'Louw post', 80.00, 'Afgerond betaald', '', '2024-06-06 09:19:30'),
(45, 'Andries Hoogenhout', 135.00, 'Afgerond betaald', '', '2024-06-08 06:54:38'),
(47, 'Minne ', 105.00, 'Onderweg', '105 afgesproken ', '2024-06-09 20:19:25'),
(48, 'Minne', 390.00, 'Onderweg', '', '2024-06-10 09:57:27'),
(49, 'Davy Broers', 135.00, 'Afgerond betaald', '', '2024-06-10 09:59:19'),
(50, 'Tim van Urk', 95.00, 'Onderweg', 'Het dek 22\r\n', '2024-06-10 10:35:07'),
(51, 'Richard Huijpen', 150.00, 'Onderweg', '', '2024-06-10 10:43:57'),
(53, 'Marjan Woort', 150.00, 'Onderweg', 'Schelpenhoek 58', '2024-06-10 11:15:36'),
(54, 'Margreet', 260.00, 'Afgerond betaald', 'Kamperzand 31', '2024-06-10 12:10:55'),
(55, 'Carina', 135.00, 'Afgerond betaald', '', '2024-06-10 12:24:07'),
(56, 'Natascha franssen', 110.00, 'Onderweg', '', '2024-06-10 13:34:58'),
(57, 'Corine', 80.00, 'Afgehaald nog niet betaald', 'Vrouwenzand 34\r\nNog 20 euro schuldig', '2024-06-10 14:22:14'),
(58, 'JW Jansen', 80.00, 'Afgerond betaald', '', '2024-06-10 16:07:20'),
(59, 'Alida Kramer', 110.00, 'Onderweg', 'Achteronder 6', '2024-06-10 16:58:53'),
(60, 'Jelle bonte', 95.00, 'Onderweg', '', '2024-06-11 06:15:55'),
(62, 'Jelle bonte ', 130.00, 'Afgerond betaald', '', '2024-06-11 21:22:47'),
(64, 'Minne', 250.00, 'Onderweg', '', '2024-06-12 09:21:06'),
(65, 'Bert broer', 75.00, 'Afgehaald nog niet betaald', '', '2024-06-12 20:03:07'),
(66, 'Kapper shipyard', 75.00, 'Onderweg', '', '2024-06-13 10:25:48'),
(67, 'Minne', 288.00, 'Onderweg', '', '2024-06-13 15:08:21'),
(68, 'Jacob mansveld', 470.00, 'Onderweg', '', '2024-06-13 15:23:56'),
(69, 'Jacob mansveld', 270.00, 'Onderweg', '', '2024-06-13 15:24:23'),
(72, 'Jacob bakker', 160.00, 'Onderweg', '', '2024-06-14 16:42:45'),
(73, 'Cees Koffeman', 135.00, 'Onderweg', '', '2024-06-15 05:36:20'),
(74, 'Aaltje kaptein', 135.00, 'Onderweg', 'Wijk 7 123', '2024-06-15 05:36:59'),
(75, 'Minne', 140.00, 'Onderweg', '', '2024-06-15 19:09:27'),
(76, 'Dealine Romkes', 100.00, 'Onderweg', 'Lacon 10', '2024-06-17 08:36:58'),
(77, 'Katherina', 170.00, 'Onderweg', 'Wijk 5 16', '2024-06-17 10:05:18'),
(78, 'Pieter', 85.00, 'Onderweg', 'Houtrib 3', '2024-06-17 10:10:54'),
(79, 'Cornelis Kaptein', 220.00, 'Afgerond betaald', 'Margrietstraat 4', '2024-06-17 16:44:50'),
(80, 'Davey Jansen', 80.00, 'Afgerond betaald', '', '2024-06-17 17:21:36'),
(81, 'Geert Woort', 135.00, 'Onderweg', 'Keggehof 8a, espel', '2024-06-17 21:10:12'),
(82, 'Annagreet', 720.00, 'Onderweg', 'Kiekendief 30', '2024-06-17 21:14:45'),
(83, 'Roelie urk', 110.00, 'Onderweg', 'Wendakker 8 nagele', '2024-06-18 10:34:22'),
(84, 'Dennis vd wal', 215.00, 'Afgerond betaald', 'Nog 15 tikkie betalen', '2024-06-19 19:00:19'),
(85, 'Janske Overmars', 160.00, 'Onderweg', 'Steenbankpad 12c tollebeek', '2024-06-20 16:16:21'),
(86, 'Corine', 80.00, 'Onderweg', 'Vrouwenzand 34', '2024-06-20 18:14:21'),
(87, 'Marjan woort', 135.00, 'Onderweg', '', '2024-06-21 11:16:29'),
(88, 'Arjan zijlstra', 185.00, 'Onderweg', '', '2024-06-21 18:46:19'),
(89, 'Johny ruizendaal', 110.00, 'Onderweg', '', '2024-06-21 18:46:58'),
(90, 'Tromp baarssen', 80.00, 'Onderweg', 'Heerenkamp 40', '2024-06-21 19:36:55'),
(91, 'Okke de boer', 85.00, 'Onderweg', '', '2024-06-21 22:01:41'),
(92, 'Margreet', 135.00, 'Onderweg', '', '2024-06-22 16:55:13'),
(93, 'Louw post', 160.00, 'Onderweg', '', '2024-06-22 18:35:12'),
(94, 'Dirk kramer', 375.00, 'Onderweg', '', '2024-06-23 11:00:44'),
(95, 'Albert kramer buurman', 160.00, 'Onderweg', '', '2024-06-23 13:33:35'),
(96, 'Minne', 110.00, 'Onderweg', '', '2024-06-23 14:13:44'),
(97, 'Sijmen de boer', 135.00, 'Onderweg', '', '2024-06-23 15:48:17'),
(98, 'Alie janson', 380.00, 'Onderweg', '', '2024-06-23 18:16:26'),
(99, 'Minne', 147.00, 'Onderweg', '', '2024-06-23 18:54:02');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `order_products`
--

CREATE TABLE `order_products` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `order_products`
--

INSERT INTO `order_products` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(72, 13, 20, 2),
(73, 26, 30, 1),
(76, 28, 30, 1),
(77, 4, 3, 1),
(78, 27, 11, 2),
(80, 29, 30, 1),
(81, 8, 3, 1),
(82, 30, 30, 2),
(85, 9, 19, 1),
(87, 7, 3, 1),
(88, 31, 30, 1),
(91, 5, 3, 1),
(121, 23, 22, 1),
(122, 6, 19, 1),
(124, 10, 21, 10),
(125, 10, 25, 2),
(126, 10, 27, 5),
(127, 18, 3, 2),
(128, 18, 24, 2),
(132, 19, 20, 1),
(133, 45, 30, 1),
(135, 40, 30, 1),
(155, 12, 15, 1),
(161, 16, 29, 1),
(168, 62, 30, 1),
(173, 55, 30, 1),
(174, 49, 30, 1),
(190, 37, 16, 1),
(191, 37, 24, 3),
(192, 41, 3, 1),
(193, 41, 21, 1),
(195, 32, 20, 7),
(196, 32, 34, 1),
(198, 34, 20, 1),
(209, 33, 24, 1),
(210, 57, 19, 1),
(211, 39, 30, 1),
(212, 38, 16, 1),
(213, 54, 20, 2),
(214, 35, 20, 1),
(215, 58, 19, 1),
(218, 42, 11, 1),
(221, 36, 20, 1),
(234, 79, 3, 1),
(235, 79, 30, 1),
(241, 80, 3, 1),
(257, 91, 3, 1),
(258, 90, 11, 1),
(259, 89, 24, 1),
(260, 88, 14, 1),
(261, 88, 24, 1),
(262, 86, 18, 1),
(263, 69, 30, 2),
(264, 72, 11, 2),
(265, 73, 20, 1),
(266, 74, 20, 1),
(267, 20, 31, 2),
(268, 47, 28, 1),
(269, 48, 21, 6),
(270, 50, 17, 1),
(271, 51, 14, 2),
(272, 53, 14, 2),
(273, 56, 24, 1),
(274, 59, 24, 1),
(275, 60, 16, 1),
(276, 64, 35, 2),
(278, 65, 3, 1),
(279, 66, 14, 1),
(280, 67, 36, 4),
(281, 68, 21, 2),
(282, 68, 24, 2),
(283, 68, 31, 1),
(284, 75, 37, 2),
(285, 76, 31, 1),
(286, 77, 10, 2),
(287, 78, 38, 1),
(288, 81, 20, 1),
(289, 82, 14, 4),
(290, 82, 24, 2),
(291, 82, 31, 2),
(292, 83, 26, 1),
(295, 85, 13, 2),
(296, 87, 30, 1),
(297, 92, 20, 1),
(301, 95, 11, 2),
(302, 96, 24, 1),
(303, 97, 20, 1),
(305, 93, 11, 2),
(306, 94, 14, 5),
(307, 98, 20, 2),
(308, 98, 24, 1),
(313, 99, 25, 1),
(314, 99, 27, 1),
(315, 84, 19, 1),
(316, 84, 30, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `producten`
--

CREATE TABLE `producten` (
  `id` int NOT NULL,
  `omschrijving` varchar(255) NOT NULL,
  `productsoort` enum('Tabak','Sigaretten') NOT NULL,
  `prijs` decimal(10,2) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `inkoopprijs` decimal(10,2) NOT NULL DEFAULT '0.00',
  `voorraad` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `producten`
--

INSERT INTO `producten` (`id`, `omschrijving`, `productsoort`, `prijs`, `foto`, `inkoopprijs`, `voorraad`) VALUES
(3, 'Camel Geel 8*30', 'Sigaretten', 85.00, 'CAMELGEEL.jpg', 68.00, 0),
(10, 'Camel Blauw 8*30', 'Sigaretten', 85.00, 'CAMELBLUE.jpg', 68.00, 0),
(11, 'Gauloise Blondes Blauw 8*30', 'Sigaretten', 80.00, '100624.jpg', 63.20, 0),
(12, 'Gauloises Blondes Rood 8*30', 'Sigaretten', 80.00, '100616.jpg', 63.20, 0),
(13, 'L&M Blauw 8*30', 'Sigaretten', 80.00, '100075.jpg', 64.00, 0),
(14, 'L&M Rood 8*30', 'Sigaretten', 75.00, '100085.jpg', 58.40, 0),
(15, 'Lucky Strike Rood 8*30', 'Sigaretten', 85.00, '100183.jpg', 68.00, 0),
(16, 'Marlboro 8*30', 'Sigaretten', 95.00, '100117.jpg', 76.00, 0),
(17, 'Marlboro Gold 8*30', 'Sigaretten', 95.00, '100139.jpg', 76.00, 0),
(18, 'Pall Mall Blauw 8*30', 'Sigaretten', 80.00, '100067.jpg', 61.60, 0),
(19, 'Pall Mall Rood 8*30', 'Sigaretten', 80.00, '100066.jpg', 61.60, 0),
(20, 'Brandaris 10*50g', 'Tabak', 135.00, '072795.jpg', 112.00, 0),
(21, 'Camel klikbus 400Gram', 'Tabak', 75.00, '200760.jpg', 52.80, 0),
(22, 'Drum 10*50', 'Tabak', 120.00, '200275.jpg', 96.00, 0),
(23, 'Gauloises Melange Original Coupe Fine 10*50', 'Tabak', 115.00, '200570.jpg', 91.00, 0),
(24, 'Jps Zwaar XL 8*60g', 'Tabak', 110.00, 'Capture.JPG', 88.00, 0),
(25, 'L&M Volume Tobacco 500 Gram', 'Tabak', 85.00, '200638.jpg', 60.00, 0),
(26, 'Lucky Strike 10*50g', 'Tabak', 110.00, '00200126.jpg', 89.00, 0),
(27, 'Marlboro Volume Tobacco 500', 'Tabak', 80.00, '401062.jpg', 62.00, 0),
(28, 'Pall Mall Red Volume 700', 'Tabak', 120.00, '405039.jpg', 92.00, 0),
(29, 'Samson 10*50', 'Tabak', 120.00, '201310.jpg', 95.00, 0),
(30, 'Van Nelle Zware Shag 10*50', 'Tabak', 135.00, '201505.jpg', 113.00, 0),
(31, 'West Red Xxl 650', 'Tabak', 100.00, '427064.jpg', 79.00, 0),
(34, 'Winston Red bucket 500Gram', 'Tabak', 80.00, 'uploads/IMG-20240603-WA0001.jpg', 63.40, 0),
(35, 'Van Nelle Stevige Shag 10*50', 'Tabak', 135.00, 'uploads/Screenshot_20240611_185239_PDF Extra.jpg', 106.00, 0),
(36, 'Elixyr Red Mega Maxx 500', 'Tabak', 80.00, 'uploads/IMG_20240612_123412_334.webp', 59.00, 0),
(37, 'Camel Active 10*20', 'Sigaretten', 80.00, 'uploads/Snapchat-1076628864.jpg', 58.00, 0),
(38, 'Kent Silver 10*20', 'Sigaretten', 85.00, 'uploads/IMG-20240615-WA0014.jpg', 65.00, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$D6eiFR3YLOGROT4X3yG4XO4W6Lt7RS34DrKxqzA.fodgI15G6k.V.', 'admin');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `bestellingen`
--
ALTER TABLE `bestellingen`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `bestelling_producten`
--
ALTER TABLE `bestelling_producten`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bestelling_id` (`bestelling_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexen voor tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `order_products`
--
ALTER TABLE `order_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexen voor tabel `producten`
--
ALTER TABLE `producten`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `bestellingen`
--
ALTER TABLE `bestellingen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT voor een tabel `bestelling_producten`
--
ALTER TABLE `bestelling_producten`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=364;

--
-- AUTO_INCREMENT voor een tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT voor een tabel `order_products`
--
ALTER TABLE `order_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- AUTO_INCREMENT voor een tabel `producten`
--
ALTER TABLE `producten`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `bestelling_producten`
--
ALTER TABLE `bestelling_producten`
  ADD CONSTRAINT `bestelling_producten_ibfk_1` FOREIGN KEY (`bestelling_id`) REFERENCES `bestellingen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bestelling_producten_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `producten` (`id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `order_products`
--
ALTER TABLE `order_products`
  ADD CONSTRAINT `order_products_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `producten` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
