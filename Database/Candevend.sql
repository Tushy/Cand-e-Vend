-- phpMyAdmin SQL Dump
-- version 4.2.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2017 at 01:20 PM
-- Server version: 5.6.29
-- PHP Version: 5.5.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nickspc146_candy`
--

-- --------------------------------------------------------

--
-- Table structure for table `bestelling`
--

CREATE TABLE IF NOT EXISTS `bestelling` (
  `bestel_nummer` int(11) NOT NULL,
  `product_ID` int(11) NOT NULL,
  `kwantiteit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bestelling`
--

INSERT INTO `bestelling` (`bestel_nummer`, `product_ID`, `kwantiteit`) VALUES
(1, 1, 2),
(1, 2, 3),
(2, 2, 1),
(6, 1, 8),
(6, 2, 8),
(7, 1, 2),
(7, 2, 3),
(8, 1, 3),
(11, 1, 2),
(11, 2, 2),
(12, 2, 1),
(13, 2, 1),
(14, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bestellingen`
--

CREATE TABLE IF NOT EXISTS `bestellingen` (
`bestel_nummer` int(11) NOT NULL,
  `geplaatst_op` datetime DEFAULT CURRENT_TIMESTAMP,
  `klantnummer` int(11) DEFAULT NULL,
  `voltooid` tinyint(1) NOT NULL DEFAULT '0',
  `QR_code` varchar(16) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bestellingen`
--

INSERT INTO `bestellingen` (`bestel_nummer`, `geplaatst_op`, `klantnummer`, `voltooid`, `QR_code`) VALUES
(1, '2017-02-01 15:39:03', 1, 1, 'Lx8QHcSEnv5xz4CD'),
(2, '2017-02-01 15:42:01', 2, 1, 't0ndbk1a1sIWgB7u'),
(6, '2017-02-01 17:15:24', 2, 1, 'KpaaqhvW1Fl4ObUh'),
(7, '2017-02-02 09:37:09', 2, 1, '0mCTLov7q0Td6ykb'),
(8, '2017-02-02 10:32:35', 7, 0, '0SelYQ2faAK5skYB'),
(11, '2017-02-02 12:01:21', 12, 1, 'VUCiQwycSLegAnmw'),
(12, '2017-02-02 12:07:11', 1, 0, '65o9v4T2fb3RcNK1'),
(13, '2017-02-02 12:07:42', 12, 1, 'NOdTdSkaTvosbMgg'),
(14, '2017-02-02 15:36:17', 13, 0, 'FxlKX23zYBRuul4f');

-- --------------------------------------------------------

--
-- Table structure for table `klanten`
--

CREATE TABLE IF NOT EXISTS `klanten` (
`klantnummer` int(11) NOT NULL,
  `email_adres` varchar(60) DEFAULT NULL,
  `wachtwoord` varchar(255) DEFAULT NULL,
  `voornaam` varchar(20) DEFAULT NULL,
  `achternaam` varchar(30) DEFAULT NULL,
  `credits` decimal(10,2) NOT NULL DEFAULT '0.00',
  `datum_creatie` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `klanten`
--

INSERT INTO `klanten` (`klantnummer`, `email_adres`, `wachtwoord`, `voornaam`, `achternaam`, `credits`, `datum_creatie`) VALUES
(1, 'nick_swaerdens@outlook.com', '$2y$10$NxTZEJ3wWBTn/XwU80LeWOkgzpZf556MfA4TlBcHUjiIrfqjR7U9u', 'Nick', 'Swaerdens', '65.00', '2017-02-01 15:32:56'),
(2, 'sander.wiggers@student.hu.nl', '$2y$10$E3XrTVVYKIQy0Hm/3pNZ0OUxOhaEvIF5OUxupsqRUql/ZHKhA3STm', 'Sander', 'Wiggers', '100.00', '2017-02-01 15:40:19'),
(7, 'lennart.adriaansenn@student.hu.nl', '$2y$10$P99.OxlJ6xNz2H29qKI2L.9W3Qxz6JvbuC85ODfr/e/muawm5fBQK', 'Lennart', 'Adriaansen', '10.00', '2017-02-02 10:31:18'),
(12, 'marwan.bellazghari@student.hu.nl', '$2y$10$mvNWYpBx1052OqSK0SrYDey2HjwiP4gW0CD6JtIh5oPC/eNbOZgUK', 'Marwan', 'bellazghari', '93.00', '2017-02-02 11:59:48'),
(13, 'lennart.adriaansen@gmail.com', '$2y$10$Xxh17AYf8/X/WIwhg5dP8uKYmaVZCa1XftGoqhIyvTi7.B1Di5StO', 'Lennart', 'Adriaansen', '20.00', '2017-02-02 15:35:21');

-- --------------------------------------------------------

--
-- Table structure for table `producten`
--

CREATE TABLE IF NOT EXISTS `producten` (
`product_ID` int(11) NOT NULL,
  `product_code` varchar(4) DEFAULT NULL,
  `product_naam` varchar(60) DEFAULT NULL,
  `voorraad` int(11) NOT NULL DEFAULT '10',
  `prijs` decimal(4,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `producten`
--

INSERT INTO `producten` (`product_ID`, `product_code`, `product_naam`, `voorraad`, `prijs`) VALUES
(1, 'B5c6', 'Haribo Dragibus', 8, '1.25'),
(2, 'P0cX', 'Haribo Goudberen', 10, '1.50'),
(3, '8iU7', 'MAOAM Pinballs', 0, '3.90');

-- --------------------------------------------------------

--
-- Table structure for table `product_info`
--

CREATE TABLE IF NOT EXISTS `product_info` (
  `product_ID` int(11) NOT NULL,
  `product_omschrijving` varchar(255) DEFAULT NULL,
  `afbeelding` varchar(2048) DEFAULT NULL,
  `inhoud` int(11) DEFAULT NULL,
  `gram` int(11) DEFAULT NULL,
  `ingredienten` varchar(500) DEFAULT NULL,
  `allergieen` varchar(255) DEFAULT 'Geen'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_info`
--

INSERT INTO `product_info` (`product_ID`, `product_omschrijving`, `afbeelding`, `inhoud`, `gram`, `ingredienten`, `allergieen`) VALUES
(1, 'Deze zure ''bite'' fruitgom is zeer populair in het kantine-assortiment, het is een van de toppers!', 'img/haribo-dragibus.png', 10, 500, 'Glucosesiroop, suiker, gelatine, dextrose, voedingszuren: citroenzuur, appelzuur, karamelstroop, vruchten- en plantenconcentraten: appel, aronia, zwarte bes, wortel, vlierbes, druif, hibiscus, kiwi, citroen, mango, sinaasappel, passievrucht, saffloer, spirulina, aroma, vlierbesextract.', 'Geen'),
(2, 'Wist je dat er dagelijks wereldwijd 100.000.000 Haribo Goudberen geproduceerd worden? Dat zegt natuurlijk al direct hoe populair dit artikel is.', 'img/haribo-sweets-goudberen.png', 10, 500, 'Glucosestroop, Suiker, Gelatine, Dextrose, Citroenzuur (E330), Citroenconcentraat, Sinaasappelconcentraat, Appelconcentraat, Kiwiconcentraat, Vlierbesconcentraat, Zwarte besconcentraat, Aroniaconcentraat, Druifconcentraat, Spinazieconcentraat, Brandnetelconcentraat, Passievruchtconcentraat, Mangoconcentraat, Wortelconcentraat, Distelconcentraat, Rode besconcentraat, Aroma, Bijenwas (E901), Carnaubawas (E903), Karamelsuikerstroop, Fruitsuiker, Invertsuikersiroop', 'Geen');

-- --------------------------------------------------------

--
-- Table structure for table `voedingswaarden`
--

CREATE TABLE IF NOT EXISTS `voedingswaarden` (
  `product_ID` int(11) NOT NULL,
  `energie` int(11) DEFAULT NULL,
  `vet` decimal(4,2) DEFAULT NULL,
  `waarvan_verzadigd` decimal(4,2) DEFAULT NULL,
  `koolhydraten` decimal(4,2) DEFAULT NULL,
  `waarvan_suikers` decimal(4,2) DEFAULT NULL,
  `eiwitten` decimal(4,2) DEFAULT NULL,
  `zout` decimal(4,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `voedingswaarden`
--

INSERT INTO `voedingswaarden` (`product_ID`, `energie`, `vet`, `waarvan_verzadigd`, `koolhydraten`, `waarvan_suikers`, `eiwitten`, `zout`) VALUES
(1, 1472, '0.50', '0.10', '80.00', '50.00', '6.60', '0.03'),
(2, 1469, '0.50', '0.10', '77.00', '46.00', '6.90', '0.07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bestelling`
--
ALTER TABLE `bestelling`
 ADD PRIMARY KEY (`bestel_nummer`,`product_ID`), ADD KEY `product_ID` (`product_ID`);

--
-- Indexes for table `bestellingen`
--
ALTER TABLE `bestellingen`
 ADD PRIMARY KEY (`bestel_nummer`), ADD KEY `klantnummer` (`klantnummer`);

--
-- Indexes for table `klanten`
--
ALTER TABLE `klanten`
 ADD PRIMARY KEY (`klantnummer`);

--
-- Indexes for table `producten`
--
ALTER TABLE `producten`
 ADD PRIMARY KEY (`product_ID`);

--
-- Indexes for table `product_info`
--
ALTER TABLE `product_info`
 ADD PRIMARY KEY (`product_ID`);

--
-- Indexes for table `voedingswaarden`
--
ALTER TABLE `voedingswaarden`
 ADD PRIMARY KEY (`product_ID`), ADD KEY `product_ID` (`product_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bestellingen`
--
ALTER TABLE `bestellingen`
MODIFY `bestel_nummer` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `klanten`
--
ALTER TABLE `klanten`
MODIFY `klantnummer` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `producten`
--
ALTER TABLE `producten`
MODIFY `product_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `bestelling`
--
ALTER TABLE `bestelling`
ADD CONSTRAINT `bestelling_ibfk_1` FOREIGN KEY (`bestel_nummer`) REFERENCES `bestellingen` (`bestel_nummer`),
ADD CONSTRAINT `bestelling_ibfk_2` FOREIGN KEY (`product_ID`) REFERENCES `producten` (`product_ID`);

--
-- Constraints for table `bestellingen`
--
ALTER TABLE `bestellingen`
ADD CONSTRAINT `bestellingen_ibfk_1` FOREIGN KEY (`klantnummer`) REFERENCES `klanten` (`klantnummer`);

--
-- Constraints for table `product_info`
--
ALTER TABLE `product_info`
ADD CONSTRAINT `product_info_ibfk_1` FOREIGN KEY (`product_ID`) REFERENCES `producten` (`product_ID`);

--
-- Constraints for table `voedingswaarden`
--
ALTER TABLE `voedingswaarden`
ADD CONSTRAINT `voedingswaarden_ibfk_1` FOREIGN KEY (`product_ID`) REFERENCES `product_info` (`product_ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
