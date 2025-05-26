<?php
header('Content-Type: application/json');
require_once 'database.php';

session_start();

$db = new DB();

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'] ?? null;
$reward_id = $data['reward_id'] ?? null;

if (!$user_id || !$reward_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Chybí user_id nebo reward_id']);
    exit;
}

// Získat typ a hodnotu výhry
$reward = $db->get("SELECT type, amount FROM rewards WHERE id = ?", [$reward_id]);
if (empty($reward)) {
    http_response_code(404);
    echo json_encode(['error' => 'Výhra nenalezena']);
    exit;
}

$type = $reward[0]['type'];
$amount = (int)$reward[0]['amount'];

// Změnit hodnoty v tabulce usersS
if ($type === 'money') {
    $db->run("UPDATE users SET money = money + ? WHERE id = ?", [$amount, $user_id]);
} elseif ($type === 'xp') {
    $db->run("UPDATE users SET xp = xp + ? WHERE id = ?", [$amount, $user_id]);
}

// Zapsat do logu výher (volitelné)
$db->run("INSERT INTO user_rewards (user_id, reward_id) VALUES (?, ?)", [$user_id, $reward_id]);

echo json_encode(['success' => true]);
