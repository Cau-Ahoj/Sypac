<?php
session_start();
require_once 'blackjack_logic.php';

processGame();

/* Bezpečné načtení stavů */
$hrac   = $_SESSION['hrac']           ?? [];
$krupier= $_SESSION['krupier']        ?? [];
$zprava = $_SESSION['zprava']         ?? '';
$gameID = $_SESSION['game_id']        ?? 'neznámé';
$konec  = $_SESSION['konec']          ?? false;
$stale  = $_SESSION['player_stood']   ?? false;

/* pomocná funkce (už definovaná v logic) */
function skore($k){ return spocitejSkore($k); }
?>
<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="utf-8">
<title>Blackjack – gameID: <?= htmlspecialchars($gameID) ?></title>
<style>button[disabled]{opacity:.5}</style>
</head>
<body>
<h1>Blackjack – gameID: <?= htmlspecialchars($gameID) ?></h1>

<h2>Hráč</h2>
<?php foreach ($hrac as $k): ?>
    <?= htmlspecialchars($k['value']).' '.htmlspecialchars($k['suit']) ?><br>
<?php endforeach; ?>
<strong>Skóre: <?= skore($hrac) ?></strong>

<h2>Krupiér</h2>
<?php foreach ($krupier as $k): ?>
    <?= htmlspecialchars($k['value']).' '.htmlspecialchars($k['suit']) ?><br>
<?php endforeach; ?>
<strong>Skóre: <?= skore($krupier) ?></strong>

<h3><?= htmlspecialchars($zprava) ?></h3>

<form method="post">
    <!-- Nová hra je povolená, jen když je konec -->
    <button name="nova"  <?= $konec ? '' : 'disabled' ?>>Nová hra</button>
    <!-- Hit / Stand jen dokud hráč nestál a hra neskončila -->
    <button name="hit"   <?= (!$konec && !$stale) ? '' : 'disabled' ?>>Hit</button>
    <button name="stand" <?= (!$konec && !$stale) ? '' : 'disabled' ?>>Stand</button>
</form>
</body>
</html>