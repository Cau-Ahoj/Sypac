<?php
session_start();
require_once 'db/database.php';
$db = new DB();

$userId = $_SESSION['user_id'] ?? null;
$gameId = $_SESSION['game_id'] ?? null;

if ($userId && $gameId) {
    // Označ hráče jako odpojeného
    $db->run("UPDATE players SET `left` = 1 WHERE game_id = ? AND user_id = ?", [$gameId, $userId]);

    // Pokud lobby je teď prázdná, smaž ji
    $count = $db->getOne("SELECT COUNT(*) AS cnt FROM players WHERE game_id = ? AND `left` = 0", [$gameId]);
    if (($count['cnt'] ?? 0) == 0) {
        $db->run("DELETE FROM players WHERE game_id = ?", [$gameId]);
        $db->run("DELETE FROM games WHERE id = ?", [$gameId]);
    }
}

// Vyčisti session
session_unset();
session_destroy();

// Přesměrování zpět na login
header('Location: login.php');
exit;