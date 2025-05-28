<?php
session_start();
require_once 'db/database.php';
$db = new DB();

$username = $_SESSION['username'] ?? null;
if (!$username) {
    header('Location: login.php');
    exit;
}

// Pokus se připojit k poslední vytvořené veřejné hře, kde je méně než 2 hráči a není dokončena
$game = $db->getOne("
    SELECT g.id
    FROM games g
    LEFT JOIN players p ON g.id = p.game_id AND p.`left` = 0
    WHERE g.public = 1 AND g.finish = 0
    GROUP BY g.id
    HAVING COUNT(p.id) < 2
    ORDER BY g.id DESC
    LIMIT 1
");

if ($game) {
    // Existuje volná hra – připoj se
    $gameId = $game['id'];
} else {
    // Jinak vytvoř novou
    $db->run("INSERT INTO games (public) VALUES (1)");
    $gameId = $db->getOne("SELECT LAST_INSERT_ID() AS id")['id'];
}

// Přidej hráče do nové/volné hry, pokud tam ještě není
$exists = $db->getOne("SELECT id FROM players WHERE game_id = ? AND user_name = ?", [$gameId, $username]);
if (!$exists) {
    $db->run("INSERT INTO players (game_id, user_name, is_dealer, hand, score, finished, `left`) VALUES (?, ?, 0, '[]', 0, 0, 0)", [$gameId, $username]);
}

// Aktualizuj session
$_SESSION['game_id'] = $gameId;

// Přesměruj na čekání
header('Location: wait.php');
exit;