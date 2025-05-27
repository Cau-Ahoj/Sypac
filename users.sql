-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u2
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost:3306
-- Vytvořeno: Úte 27. kvě 2025, 09:05
-- Verze serveru: 10.5.28-MariaDB-0+deb11u2
-- Verze PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `celikovsky_casino_db`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `money` int(11) DEFAULT 0,
  `xp` int(11) DEFAULT 0,
  `lvl` int(11) DEFAULT 0,
  `role` enum('user','admin','guest') DEFAULT 'user',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `crit_level` int(11) DEFAULT 0,
  `passive_level` int(11) DEFAULT 0,
  `crit_xp_bonus` int(11) DEFAULT 0,
  `xp_bar_completions` int(11) DEFAULT 0,
  `base_xp_gain` int(11) DEFAULT 1,
  `reward_bonus_percent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `telefon`, `password`, `money`, `xp`, `lvl`, `role`, `reset_token`, `reset_expires`, `crit_level`, `passive_level`, `crit_xp_bonus`, `xp_bar_completions`, `base_xp_gain`, `reward_bonus_percent`) VALUES
(1, 'hanz', 'c@sc.cz', NULL, 'bagr', 54, 67, 2, 'user', NULL, NULL, 40, 0, 0, 0, 1, 0),
(2, 'hh', 'hoc@sf.cz', NULL, 'fefef', 109724, 7334, 7, 'user', NULL, NULL, 0, 0, 19, 0, 386, 960),
(4, 'blem', 'hh@sd.cz', '111333222', 'ddada', 10, 0, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
