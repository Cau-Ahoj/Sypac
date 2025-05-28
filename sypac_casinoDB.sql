-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u2
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost:3306
-- Vytvořeno: Stř 28. kvě 2025, 08:50
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
-- Databáze: `sypac_casinoDB`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `blackjack`
--

CREATE TABLE `blackjack` (
  `id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `suit` varchar(10) NOT NULL,
  `picture` varchar(50) DEFAULT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `bonus_type` varchar(50) DEFAULT NULL,
  `bonus_value` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `detail` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `detail`, `timestamp`, `ip_address`, `user_agent`) VALUES
(1, 5, 'login', 'Uživatel se přihlásil.', '2025-05-27 08:28:57', NULL, NULL),
(2, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 09:11:56', NULL, NULL),
(3, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-27 09:12:02', NULL, NULL),
(4, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 09:14:35', NULL, NULL),
(5, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-27 09:15:35', NULL, NULL),
(6, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 09:16:04', NULL, NULL),
(7, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-27 09:16:20', NULL, NULL),
(8, 1, 'login', 'Uživatel se přihlásil. | IP: 82.202.118.189 | Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', '2025-05-27 09:18:51', NULL, NULL),
(9, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-27 09:26:18', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(10, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 09:26:31', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(11, 8, 'login', 'Uživatel se přihlásil.', '2025-05-27 09:35:34', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(12, 9, 'login', 'Uživatel se přihlásil.', '2025-05-27 09:43:53', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(13, 5, 'reset_code_generated', 'Reset kód 973662 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:03', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(14, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:03', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(15, 5, 'reset_code_generated', 'Reset kód 950588 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:11', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(16, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:11', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(17, 5, 'reset_code_generated', 'Reset kód 652593 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:12', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(18, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:12', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(19, 5, 'reset_code_generated', 'Reset kód 498542 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:13', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(20, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:13', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(21, 5, 'reset_code_generated', 'Reset kód 414091 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:13', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(22, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:13', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(23, 5, 'reset_code_generated', 'Reset kód 310587 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:14', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(24, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:14', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(25, 5, 'reset_code_generated', 'Reset kód 709955 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:15', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(26, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:15', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(27, 5, 'reset_code_generated', 'Reset kód 658842 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:15', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(28, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:16', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(29, 5, 'reset_code_generated', 'Reset kód 506563 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:17', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(30, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:17', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(31, 5, 'reset_code_generated', 'Reset kód 619157 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:17', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(32, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:17', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(33, 5, 'reset_code_generated', 'Reset kód 543598 vygenerován a uložen pro e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:18', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(34, 5, 'reset_code_sent', 'Kód odeslán na e-mail: s.varga@zsskalice.cz', '2025-05-27 10:11:18', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(35, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-27 10:37:52', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(36, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 10:37:59', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(37, 5, 'login', 'Uživatel se přihlásil.', '2025-05-27 10:45:31', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(38, 5, 'login', 'Uživatel se přihlásil.', '2025-05-27 10:46:18', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(39, 5, 'login', 'Uživatel se přihlásil.', '2025-05-27 10:54:22', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0'),
(40, 5, 'code_verified', 'Kód 614897 úspěšně ověřen pro s.varga@zsskalice.cz', '2025-05-27 11:17:18', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(41, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 11:20:36', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(42, 5, 'password_change_attempt', 'Pokus o změnu hesla', '2025-05-27 11:22:18', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(43, 5, 'password_changed', 'Heslo změněno', '2025-05-27 11:22:19', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(44, 12, 'login', 'Uživatel se přihlásil.', '2025-05-27 11:39:54', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(45, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 13:16:30', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(46, 1, 'password_reset_request', 'Žádost o reset hesla (e-mail: poledna.s@ssmg.cz, kód: 334070)', '2025-05-27 13:29:45', NULL, NULL),
(47, 1, 'password_reset_request', 'Žádost o reset hesla (e-mail: poledna.s@ssmg.cz, kód: 957468)', '2025-05-27 13:36:51', NULL, NULL),
(48, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 13:37:15', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(49, 11, 'password_reset_request', 'Žádost o reset hesla (e-mail: stepan.poledna@gmail.com, kód: 228823)', '2025-05-27 13:38:52', NULL, NULL),
(50, 5, 'login', 'Uživatel se přihlásil.', '2025-05-27 13:42:14', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(51, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-27 13:54:15', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(52, 1, 'login', 'Uživatel se přihlásil.', '2025-05-27 13:59:17', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(53, 13, 'login', 'Uživatel se přihlásil.', '2025-05-27 14:22:08', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:136.0) Gecko/20100101 Firefox/136.0'),
(54, 13, 'login', 'Uživatel se přihlásil.', '2025-05-27 14:22:57', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(55, 13, 'login', 'Uživatel se přihlásil.', '2025-05-27 14:38:54', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(56, 5, 'login', 'Uživatel se přihlásil.', '2025-05-27 16:08:19', '109.81.167.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0'),
(57, 14, 'login', 'Uživatel se přihlásil.', '2025-05-28 07:53:46', '89.24.32.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(58, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 07:57:55', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0'),
(59, 1, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:01:31', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(60, 1, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:03:49', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(61, 15, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:09:21', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(62, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:13:26', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(63, 1, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:14:54', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(64, 16, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:16:56', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(65, 16, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:25:49', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(66, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:27:16', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(67, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:28:52', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(68, 5, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:29:14', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(69, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:30:56', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(70, 5, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:31:08', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(71, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:31:15', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(72, 5, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:32:17', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(73, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:32:22', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(74, 5, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:32:35', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(75, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:33:40', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(76, 5, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:34:38', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(77, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:35:14', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(78, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:35:15', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(79, 5, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:35:22', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(80, 1, 'LOGOUT', 'Uživatel se odhlásil.', '2025-05-28 08:37:35', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0'),
(81, 1, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:37:57', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36'),
(82, 5, 'login', 'Uživatel se přihlásil.', '2025-05-28 08:48:59', '82.202.118.189', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Struktura tabulky `main_menu`
--

CREATE TABLE `main_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `path` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `main_menu`
--

INSERT INTO `main_menu` (`id`, `name`, `path`) VALUES
(1, 'klikač', 'clicker'),
(2, 'skořápky', 'shells'),
(3, 'sloty', 'slots');

-- --------------------------------------------------------

--
-- Struktura tabulky `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `purchased_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `slot_results`
--

CREATE TABLE `slot_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `slot1` varchar(10) DEFAULT NULL,
  `slot2` varchar(10) DEFAULT NULL,
  `slot3` varchar(10) DEFAULT NULL,
  `result_text` text DEFAULT NULL,
  `win_amount` int(11) DEFAULT NULL,
  `spin_cost` int(11) DEFAULT NULL,
  `credit_after` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `slot_results`
--

INSERT INTO `slot_results` (`id`, `user_id`, `slot1`, `slot2`, `slot3`, `result_text`, `win_amount`, `spin_cost`, `credit_after`, `created_at`) VALUES
(3351, 1, '3', '2', '5', '❌ Nic si nevyhrál.', 0, 10, 0, '2025-05-27 14:29:40'),
(3352, 5, '5', '4', '5', '❌ Nic si nevyhrál.', 0, 50, 264829, '2025-05-27 16:08:21'),
(3353, 5, '5', '3', '1', '❌ Nic si nevyhrál.', 0, 50, 264779, '2025-05-27 16:08:22'),
(3354, 5, '3', '0', '4', '❌ Nic si nevyhrál.', 0, 50, 264729, '2025-05-27 16:08:22'),
(3355, 5, '3', '2', '3', '❌ Nic si nevyhrál.', 0, 50, 264679, '2025-05-27 16:08:22'),
(3356, 5, '3', '2', '4', '❌ Nic si nevyhrál.', 0, 50, 264629, '2025-05-27 16:08:22'),
(3357, 5, '6', '4', '5', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 264579, '2025-05-27 16:08:23'),
(3358, 5, '2', '3', '2', '❌ Nic si nevyhrál.', 0, 50, 264529, '2025-05-27 16:08:23'),
(3359, 5, '6', '3', '6', 'Výhra díky jokerovi!', 200, 50, 264679, '2025-05-28 07:58:22'),
(3360, 5, '5', '2', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 264629, '2025-05-28 07:58:23'),
(3361, 5, '3', '0', '1', '❌ Nic si nevyhrál.', 0, 50, 264579, '2025-05-28 07:58:23'),
(3362, 5, '5', '2', '4', '❌ Nic si nevyhrál.', 0, 50, 264529, '2025-05-28 07:58:24'),
(3363, 5, '6', '3', '5', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 264479, '2025-05-28 07:58:25'),
(3364, 5, '5', '3', '5', '❌ Nic si nevyhrál.', 0, 50, 264429, '2025-05-28 07:58:25'),
(3365, 5, '1', '5', '5', '❌ Nic si nevyhrál.', 0, 50, 264379, '2025-05-28 07:58:26'),
(3366, 5, '2', '3', '5', '❌ Nic si nevyhrál.', 0, 50, 264329, '2025-05-28 07:58:26'),
(3367, 5, '5', '2', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 264279, '2025-05-28 07:58:27'),
(3368, 5, '0', '3', '5', '❌ Nic si nevyhrál.', 0, 50, 264229, '2025-05-28 07:58:27'),
(3369, 5, '1', '6', '1', 'Výhra díky jokerovi!', 200, 50, 264379, '2025-05-28 07:58:45'),
(3370, 5, '6', '4', '5', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 264329, '2025-05-28 07:58:46'),
(3371, 5, '0', '3', '0', '❌ Nic si nevyhrál.', 0, 50, 264279, '2025-05-28 07:58:47'),
(3372, 5, '2', '0', '2', '❌ Nic si nevyhrál.', 0, 50, 264229, '2025-05-28 07:58:48'),
(3373, 5, '2', '0', '4', '❌ Nic si nevyhrál.', 0, 50, 264179, '2025-05-28 07:58:49'),
(3374, 5, '3', '1', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 264129, '2025-05-28 07:58:50'),
(3375, 5, '1', '4', '5', '❌ Nic si nevyhrál.', 0, 50, 264079, '2025-05-28 07:58:51'),
(3376, 5, '3', '6', '6', 'Výhra díky jokerovi!', 200, 50, 264229, '2025-05-28 07:58:52'),
(3377, 5, '2', '0', '4', '❌ Nic si nevyhrál.', 0, 50, 264179, '2025-05-28 07:58:55'),
(3378, 5, '3', '0', '1', '❌ Nic si nevyhrál.', 0, 50, 264129, '2025-05-28 07:58:59'),
(3379, 5, '5', '1', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 264079, '2025-05-28 07:58:59'),
(3380, 5, '6', '3', '5', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 264029, '2025-05-28 07:59:00'),
(3381, 5, '1', '0', '0', '❌ Nic si nevyhrál.', 0, 50, 263979, '2025-05-28 07:59:00'),
(3382, 5, '5', '3', '2', '❌ Nic si nevyhrál.', 0, 50, 263929, '2025-05-28 07:59:01'),
(3383, 5, '0', '3', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 263879, '2025-05-28 07:59:01'),
(3384, 5, '5', '5', '0', '❌ Nic si nevyhrál.', 0, 50, 263829, '2025-05-28 07:59:02'),
(3385, 5, '4', '1', '4', '❌ Nic si nevyhrál.', 0, 50, 263779, '2025-05-28 07:59:02'),
(3386, 5, '6', '0', '6', 'Výhra díky jokerovi!', 200, 50, 263929, '2025-05-28 07:59:03'),
(3387, 5, '0', '3', '4', '❌ Nic si nevyhrál.', 0, 50, 263879, '2025-05-28 08:02:46'),
(3388, 5, '3', '0', '3', '❌ Nic si nevyhrál.', 0, 50, 263829, '2025-05-28 08:02:46'),
(3389, 5, '5', '6', '4', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 263779, '2025-05-28 08:02:47'),
(3390, 5, '4', '1', '2', '❌ Nic si nevyhrál.', 0, 50, 263729, '2025-05-28 08:02:47'),
(3391, 5, '4', '3', '5', '❌ Nic si nevyhrál.', 0, 50, 263679, '2025-05-28 08:02:47'),
(3392, 5, '2', '6', '1', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 263629, '2025-05-28 08:02:48'),
(3393, 5, '0', '2', '2', '❌ Nic si nevyhrál.', 0, 50, 263579, '2025-05-28 08:02:48'),
(3394, 5, '3', '3', '0', '❌ Nic si nevyhrál.', 0, 50, 263529, '2025-05-28 08:02:48'),
(3395, 5, '0', '0', '5', '❌ Nic si nevyhrál.', 0, 50, 263479, '2025-05-28 08:02:49'),
(3396, 5, '3', '2', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 263429, '2025-05-28 08:02:49'),
(3397, 5, '2', '6', '3', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 263379, '2025-05-28 08:02:49'),
(3398, 5, '1', '2', '5', '❌ Nic si nevyhrál.', 0, 50, 263329, '2025-05-28 08:02:50'),
(3399, 5, '3', '5', '2', '❌ Nic si nevyhrál.', 0, 50, 263279, '2025-05-28 08:02:50'),
(3400, 5, '2', '2', '3', '❌ Nic si nevyhrál.', 0, 50, 263229, '2025-05-28 08:02:52'),
(3401, 5, '3', '5', '5', '❌ Nic si nevyhrál.', 0, 50, 263179, '2025-05-28 08:02:55'),
(3402, 5, '0', '0', '3', '❌ Nic si nevyhrál.', 0, 50, 263129, '2025-05-28 08:02:58'),
(3403, 5, '3', '0', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 263079, '2025-05-28 08:03:01'),
(3404, 5, '1', '2', '5', '❌ Nic si nevyhrál.', 0, 50, 263029, '2025-05-28 08:03:04'),
(3405, 5, '0', '4', '0', '❌ Nic si nevyhrál.', 0, 50, 262979, '2025-05-28 08:03:05'),
(3406, 5, '0', '2', '5', '❌ Nic si nevyhrál.', 0, 50, 262929, '2025-05-28 08:03:05'),
(3407, 5, '6', '5', '1', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 262879, '2025-05-28 08:03:06'),
(3408, 5, '5', '4', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 262829, '2025-05-28 08:03:06'),
(3409, 5, '0', '4', '3', '❌ Nic si nevyhrál.', 0, 50, 262779, '2025-05-28 08:03:09'),
(3410, 5, '3', '3', '5', '❌ Nic si nevyhrál.', 0, 50, 262729, '2025-05-28 08:03:09'),
(3411, 5, '3', '2', '1', '❌ Nic si nevyhrál.', 0, 50, 262679, '2025-05-28 08:03:09'),
(3412, 5, '6', '4', '6', '💎 Diamanty s jokerem 💎!', 250, 50, 262879, '2025-05-28 08:03:10'),
(3413, 5, '6', '5', '5', 'JACKPOOT 💰 s jokerem!', 300, 50, 263129, '2025-05-28 08:03:10'),
(3414, 5, '5', '5', '1', '❌ Nic si nevyhrál.', 0, 50, 263079, '2025-05-28 08:03:15'),
(3415, 14, '3', '4', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 190, '2025-05-28 08:16:19'),
(3416, 14, '4', '6', '5', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 140, '2025-05-28 08:16:23'),
(3417, 14, '1', '2', '4', '❌ Nic si nevyhrál.', 0, 50, 90, '2025-05-28 08:16:27'),
(3418, 14, '2', '3', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 40, '2025-05-28 08:16:28'),
(3419, 14, '0', '3', '5', '❌ Nic si nevyhrál.', 0, 50, 48, '2025-05-28 08:17:27'),
(3420, 14, '6', '3', '3', 'Výhra díky jokerovi!', 40, 10, 78, '2025-05-28 08:17:32'),
(3421, 14, '0', '2', '0', '❌ Nic si nevyhrál.', 0, 10, 68, '2025-05-28 08:17:35'),
(3422, 14, '4', '5', '3', '❌ Nic si nevyhrál.', 0, 10, 58, '2025-05-28 08:17:36'),
(3423, 14, '3', '6', '2', 'Dva různé symboly a joker, žádná výhra.', 0, 10, 48, '2025-05-28 08:17:38'),
(3424, 14, '5', '0', '2', '❌ Nic si nevyhrál.', 0, 10, 38, '2025-05-28 08:17:40'),
(3425, 14, '2', '4', '2', '❌ Nic si nevyhrál.', 0, 10, 28, '2025-05-28 08:17:41'),
(3426, 14, '5', '5', '6', 'JACKPOOT 💰 s jokerem!', 60, 10, 78, '2025-05-28 08:17:43'),
(3427, 14, '6', '4', '1', 'Dva různé symboly a joker, žádná výhra.', 0, 10, 68, '2025-05-28 08:17:49'),
(3428, 14, '5', '3', '1', '❌ Nic si nevyhrál.', 0, 10, 58, '2025-05-28 08:17:50'),
(3429, 14, '1', '6', '6', 'Výhra díky jokerovi!', 40, 10, 88, '2025-05-28 08:17:52'),
(3430, 14, '4', '6', '6', '💎 Diamanty s jokerem 💎!', 50, 10, 128, '2025-05-28 08:17:55'),
(3431, 14, '0', '4', '0', '❌ Nic si nevyhrál.', 0, 10, 118, '2025-05-28 08:17:59'),
(3432, 14, '2', '3', '1', '❌ Nic si nevyhrál.', 0, 10, 108, '2025-05-28 08:18:01'),
(3433, 14, '3', '1', '1', '❌ Nic si nevyhrál.', 0, 10, 98, '2025-05-28 08:18:03'),
(3434, 14, '3', '6', '5', 'Dva různé symboly a joker, žádná výhra.', 0, 10, 88, '2025-05-28 08:18:04'),
(3435, 14, '3', '6', '0', 'Dva různé symboly a joker, žádná výhra.', 0, 10, 78, '2025-05-28 08:18:05'),
(3436, 14, '5', '0', '2', '❌ Nic si nevyhrál.', 0, 10, 68, '2025-05-28 08:18:06'),
(3437, 14, '0', '0', '5', '❌ Nic si nevyhrál.', 0, 10, 58, '2025-05-28 08:18:07'),
(3438, 14, '2', '5', '1', '❌ Nic si nevyhrál.', 0, 10, 48, '2025-05-28 08:18:10'),
(3439, 14, '5', '2', '4', '❌ Nic si nevyhrál.', 0, 10, 38, '2025-05-28 08:18:12'),
(3440, 14, '1', '3', '2', '❌ Nic si nevyhrál.', 0, 10, 28, '2025-05-28 08:18:13'),
(3441, 14, '0', '5', '2', '❌ Nic si nevyhrál.', 0, 10, 18, '2025-05-28 08:18:14'),
(3442, 14, '2', '3', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 10, 8, '2025-05-28 08:18:16'),
(3443, 5, '4', '2', '5', '❌ Nic si nevyhrál.', 0, 50, 263029, '2025-05-28 08:29:57'),
(3444, 5, '3', '5', '2', '❌ Nic si nevyhrál.', 0, 50, 262979, '2025-05-28 08:29:57'),
(3445, 5, '5', '0', '2', '❌ Nic si nevyhrál.', 0, 50, 262929, '2025-05-28 08:29:58'),
(3446, 5, '2', '3', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 262879, '2025-05-28 08:35:32'),
(3447, 5, '1', '0', '3', '❌ Nic si nevyhrál.', 0, 50, 262829, '2025-05-28 08:35:33'),
(3448, 5, '6', '0', '5', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 262779, '2025-05-28 08:35:33'),
(3449, 5, '2', '2', '5', '❌ Nic si nevyhrál.', 0, 50, 262729, '2025-05-28 08:35:34'),
(3450, 5, '3', '3', '1', '❌ Nic si nevyhrál.', 0, 50, 262679, '2025-05-28 08:35:34'),
(3451, 5, '4', '0', '3', '❌ Nic si nevyhrál.', 0, 50, 262629, '2025-05-28 08:35:35'),
(3452, 5, '1', '5', '4', '❌ Nic si nevyhrál.', 0, 50, 262579, '2025-05-28 08:35:35'),
(3453, 5, '1', '3', '5', '❌ Nic si nevyhrál.', 0, 50, 262529, '2025-05-28 08:35:36'),
(3454, 5, '6', '2', '1', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 262479, '2025-05-28 08:35:36'),
(3455, 5, '1', '4', '2', '❌ Nic si nevyhrál.', 0, 50, 262429, '2025-05-28 08:35:36'),
(3456, 5, '5', '5', '1', '❌ Nic si nevyhrál.', 0, 50, 262379, '2025-05-28 08:35:37'),
(3457, 5, '3', '1', '6', 'Dva různé symboly a joker, žádná výhra.', 0, 50, 262329, '2025-05-28 08:35:37'),
(3458, 5, '3', '0', '5', '❌ Nic si nevyhrál.', 0, 50, 262279, '2025-05-28 08:35:38'),
(3459, 5, '4', '5', '1', '❌ Nic si nevyhrál.', 0, 50, 262229, '2025-05-28 08:35:38'),
(3460, 5, '1', '5', '5', '❌ Nic si nevyhrál.', 0, 50, 262179, '2025-05-28 08:35:39'),
(3461, 5, '3', '4', '0', '❌ Nic si nevyhrál.', 0, 50, 262129, '2025-05-28 08:35:39'),
(3462, 5, '0', '4', '4', '❌ Nic si nevyhrál.', 0, 50, 262079, '2025-05-28 08:35:40'),
(3463, 5, '5', '1', '4', '❌ Nic si nevyhrál.', 0, 50, 262029, '2025-05-28 08:35:40');

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
(1, 'admin', 'poledna.s@ssmg.cz', '1111111111', '$2y$10$hmRo/HwO4FbA.LGa7v31E.z6Fmdw0XMSO0yhKQIpvUhJTwKTfaKZ2', 1212, 26441, 10, 'user', NULL, NULL, 10, 0, 15, 0, 19, 880),
(3, 'admin1', 'tghh@df.df', '1111111111', '$2y$10$Xp04Mpgx5Saj5MJAbCaMxe0YwEB5XyiRl.ZHF6aPR/PYFieddRZgC', 200, 0, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(4, 'admin2', 'tghh@df.d', '1111111111', '$2y$10$FzF8fVJugpyJEZXHLOL7Iem6NoKdZ9VCDIYWC0vQYo72mz1c9V3Nu', 200, 0, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(5, 'Štefik', 's.varga@zsskalice.cz', '731458932', '$2y$10$9kB/j3wBigc8.T3MSXPrR.rYt73UnzPhfI/qC9Bqw04dY23ccJgam', 262059, 2405, 0, 'user', '725188', '2025-05-27 11:52:42', 0, 0, 0, 0, 1, 0),
(8, 'ottonepracuje', 'ottonepracuje@email.cz', '777777777', '$2y$10$XmVwbhQVfAhgqIRWpXlw/e9MpOMwJWPvhM1klM3PdRRxlBSpjB/GS', 0, 828, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(9, 'Otto by chtěl programovat', 'otto@programator.cz', '654446447', '$2y$10$eQgXna2Yk/GGr4DLPOJNG.hSDkqVPsqRM1wG4L34is0lMNlCpwHX.', 100334400, 201, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(10, 'derco.d', 'TheF.student@ssmg.cz', '1554452141', '$2y$10$e6po/WVADKb5Eo1wB4d1.e9InSygEWkgIJZpWghFM/8ARdzjUx/Rq', 200, 0, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(11, 'admin1234', 'stepan.poledna@gmail.com', '1111111111', '$2y$10$1DlLlYSBTyVOvK5rr2/84O0Jha.gU95VGJKx4ccMYrpdDwRQNfe.u', 200, 0, 0, 'user', '228823', '2025-05-27 14:08:52', 0, 0, 0, 0, 1, 0),
(12, 'domes', 'hokr.d@ssmg.cz', '75458456', '$2y$10$iw6waF1JoR1ue9OZm8BH5OgaW2McZbJlw9k72ZtSaSx84RfK7mTlm', 100, 0, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(13, 'ottanechcedomu', 'dobryden@seznam.cz', '123', '$2y$10$I8TkYWPcJDfcl0M3JFIol.hN84/9dCQ5QH7iwjv/pGG3DkSGX3Woa', 174, 190, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(14, 'bít černý', 'becomiv487@daxiake.com', '605808617', '$2y$10$dmgexu3BslP6W1IKEkSrxOGpVEx0c13vuybf9A7PU1bqpg4/A0/fO', 8, 141, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(15, 'standa', 'standa@seznam.cz', '111222333', '$2y$10$rqanOMe6AuJeACk4FLBKKOllaZM4Hjd.Yyk7QBqPL4fPKxNZRGXne', 200, 0, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0),
(16, 'sypacek', 'sdsdss@sdds.cz', '686879778', '$2y$10$xeGmAQ4nv5RpzzYXIHq00OOEkAdqamQJqZMLLLFwCfeM9f.X9vSdu', 200, 0, 0, 'user', NULL, NULL, 0, 0, 0, 0, 1, 0);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `blackjack`
--
ALTER TABLE `blackjack`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Klíče pro tabulku `main_menu`
--
ALTER TABLE `main_menu`
  ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Klíče pro tabulku `slot_results`
--
ALTER TABLE `slot_results`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT pro tabulku `blackjack`
--
ALTER TABLE `blackjack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT pro tabulku `main_menu`
--
ALTER TABLE `main_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pro tabulku `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `slot_results`
--
ALTER TABLE `slot_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3464;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
