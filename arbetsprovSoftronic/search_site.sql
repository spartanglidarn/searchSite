-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 15 feb 2017 kl 22:51
-- Serverversion: 5.7.14
-- PHP-version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `search_site`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `search_results`
--

CREATE TABLE `search_results` (
  `id` int(20) NOT NULL,
  `string_id` int(20) NOT NULL,
  `result_url` varchar(255) NOT NULL,
  `result_name` varchar(255) NOT NULL,
  `search_engine` varchar(255) NOT NULL,
  `rank` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `search_results`
--

INSERT INTO `search_results` (`id`, `string_id`, `result_url`, `result_name`, `search_engine`, `rank`) VALUES
(1743, 231, 'http://qunitjs.com/', 'QUnit - Official Site', 'Bing', 8),
(1744, 231, 'http://jsfiddle.net/', 'Create a new fiddle - JSFiddle', 'Bing', 9),
(1745, 231, 'http://www.test.com/', 'Online tests and testing software | Test.com', 'Bing', 10),
(1746, 232, 'https://en.wikipedia.org/wiki/Test_script', 'Test script - Wikipedia', 'Google', 1),
(1747, 232, 'http://softwaretestingfundamentals.com/test-script/', 'Test Script &#8211; Software Testing Fundamentals', 'Google', 2),
(1748, 232, 'http://www.riceconsulting.com/home/index.php/Web-Testing/how-to-develop-test-cases-and-test-scripts-for-web-testing.html', 'How to Develop Test Cases and Test Scripts for Web Testing | Web ...', 'Google', 3);

-- --------------------------------------------------------

--
-- Tabellstruktur `search_strings`
--

CREATE TABLE `search_strings` (
  `id` int(10) NOT NULL,
  `search_string` varchar(255) NOT NULL,
  `reg_date` timestamp NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `search_strings`
--

INSERT INTO `search_strings` (`id`, `search_string`, `reg_date`) VALUES
(143, 'testing script', '2017-02-12 23:15:02'),
(3, 'testing', '2017-02-10 13:35:30'),
(142, 'testing script', '2017-02-12 23:11:29'),
(7, 'for honor', '2017-02-12 20:43:41');

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `search_results`
--
ALTER TABLE `search_results`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `search_strings`
--
ALTER TABLE `search_strings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `search_results`
--
ALTER TABLE `search_results`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1980;
--
-- AUTO_INCREMENT för tabell `search_strings`
--
ALTER TABLE `search_strings`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
