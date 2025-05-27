<?php
session_start();
require_once 'blackjack_logic.php';

processGame();

$hrac = $_SESSION['hrac'] ?? [];
if (!is_array($hrac)) $hrac = [];

$krupier = $_SESSION['krupier'] ?? [];
if (!is_array($krupier)) $krupier = [];

$zprava = $_SESSION['zprava'] ?? '';
$gameID = $_SESSION['game_id'] ?? 'neznámé';

$konec = $_SESSION['konec'] ?? false;
$player_stood = $_SESSION['player_stood'] ?? false;
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <title>Blackjack – gameID: <?= htmlspecialchars($gameID) ?></title>
</head>
<body>
    <h1>Blackjack – gameID: <?= htmlspecialchars($gameID) ?></h1>

    <h2>Hráč</h2>
    <?php foreach ($hrac as $k): ?>
        <?= htmlspecialchars($k['value'] ?? '') ?> <?= htmlspecialchars($k['suit'] ?? '') ?><br>
    <?php endforeach; ?>
    <b>Skóre: <?= spocitejSkore($hrac) ?></b>

    <h2>Krupiér</h2>
    <?php foreach ($krupier as $k): ?>
        <?= htmlspecialchars($k['value'] ?? '') ?> <?= htmlspecialchars($k['suit'] ?? '') ?><br>
    <?php endforeach; ?>
    <b>Skóre: <?= spocitejSkore($krupier) ?></b>

    <h3><?= htmlspecialchars($zprava) ?></h3>

    <form method="post">
    <!-- Nová hra je povolena jen pokud je konec hry -->
    <button name="nova" <?= !$konec ? 'disabled' : '' ?>>Nová hra</button>

    <!-- Hit a Stand jsou povoleny jen pokud hra není u konce a hráč nestál -->
    <button name="hit" <?= ($konec || $player_stood) ? 'disabled' : '' ?>>Hit (karta navíc)</button>
    <button name="stand" <?= ($konec || $player_stood) ? 'disabled' : '' ?>>Stand (čekám)</button>
    </form>
</body>
</html>