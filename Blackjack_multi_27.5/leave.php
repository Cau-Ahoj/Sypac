<?php
session_start();
const GAMEFILE = __DIR__ . '/games.json';

if (!isset($_SESSION['username'], $_SESSION['game_id'])) {
    exit;
}

$username = $_SESSION['username'];
$gameId = $_SESSION['game_id'];

if (!file_exists(GAMEFILE)) exit;
$games = json_decode(file_get_contents(GAMEFILE), true);

if (!isset($games[$gameId])) exit;

// Označíme hráče jako opuštěného
$games[$gameId]['left'][] = $username;
$games[$gameId]['chat'][] = ['from' => 'SYSTEM', 'text' => "$username opustil hru."];

file_put_contents(GAMEFILE, json_encode($games));
?>