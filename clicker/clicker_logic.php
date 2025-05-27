<?php
require_once '../database.php';
session_start();

$db = new DB();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = $db->getOne("SELECT * FROM users WHERE id = ?", [$user_id]);

$level = max(0, $user['lvl']);

// 🔁 Exponenciální násobek
$multiplier = pow(1.2, $level);

// 💰 Výpočet výdělků
$moneyGain = round(1 * $multiplier);  // základ je 1 Kč
$xpGain = round(1 * $multiplier);     // základ je 1 XP

// 📥 Uložení nových hodnot
$user['money'] += $moneyGain;
$user['xp'] += $xpGain;

$db->run("UPDATE users SET money = ?, xp = ? WHERE id = ?", [
    $user['money'], $user['xp'], $user_id
]);

header("Location: index.php");
exit;
