<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db/database.php';
$db = new DB();

if (!isset($_SESSION['username'], $_SESSION['game_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$user   = $_SESSION['username'];
$gameId = $_SESSION['game_id'];

// POST – uložení zprávy
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Neplatný JSON vstup']);
        exit;
    }

    $text = trim($data['text'] ?? '');

    if ($text !== '') {
        $db->run("INSERT INTO chat_messages (game_id, user_name, text, time) VALUES (?, ?, ?, NOW())", [$gameId, $user, $text]);
    }
    exit;
}

// GET – načtení zpráv
header('Content-Type: application/json; charset=utf-8');
$messages = $db->getAll("SELECT user_name AS user, text FROM chat_messages WHERE game_id = ? ORDER BY time ASC", [$gameId]);
echo json_encode($messages);