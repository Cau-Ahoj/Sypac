<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['game_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$gameId = $_SESSION['game_id'];
$gamesFile = __DIR__ . '/games.json';

// Načti všechny hry
$games = file_exists($gamesFile) ? json_decode(file_get_contents($gamesFile), true) : [];

if (!isset($games[$gameId])) {
    echo "Hra nebyla nalezena.";
    exit;
}

$game = $games[$gameId];

// Ověření, že hráč je účastníkem hry
if (!in_array($username, $game['players'])) {
    echo "Nejste účastníkem této hry.";
    exit;
}

if (count($game['players']) < 2) {
    // Ještě chybí druhý hráč
    echo "<h1>Čekání na druhého hráče...</h1>";
    echo "<p>Hráč připojen: " . htmlspecialchars($game['players'][0]) . "</p>";
    echo "<p>Jakmile se připojí druhý hráč, hra začne.</p>";
    echo "<script>
        setTimeout(() => location.reload(), 3000);
    </script>";
    exit;
}

// Jsou 2 hráči, vypíšeme jejich jména a přesměrujeme
echo "<h1>Hra je připravena!</h1>";
echo "<p>Hráči:</p><ul>";
foreach ($game['players'] as $player) {
    echo "<li>" . htmlspecialchars($player) . "</li>";
}
echo "</ul>";
echo "<p>Probíhá přesměrování do hry...</p>";
echo "<script>setTimeout(() => { window.location.href = 'game.php'; }, 2000);</script>";
exit;