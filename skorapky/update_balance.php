<?php
session_start();
header('Content-Type: application/json');
require_once 'database.php';

$response = ['success' => false];

try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        throw new Exception('Uživatel není přihlášen.');
    }

    $user_id = $_SESSION['user_id'];

    // Číst data z POST JSON
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Neplatná JSON data: ' . json_last_error_msg());
    }

    $amount = $data['amount'] ?? 0;
    if (!is_numeric($amount) || $amount == 0) {
        throw new Exception('Neplatná částka.');
    }

    $db = new DB();

    // Aktualizuj peníze (přičtení nebo odečtení)
    $result = $db->run("UPDATE users SET money = money + ? WHERE id = ? AND (money + ?) >= 0", [$amount, $user_id, $amount]);

    if ($result === 0) {
        throw new Exception('Aktualizace zůstatku selhala (nedostatek peněz nebo uživatel neexistuje).');
    }

    // Načti nový zůstatek
    $new_balance = $db->getOne("SELECT money FROM users WHERE id = ?", [$user_id]);

    $response['success'] = true;
    $response['new_balance'] = (int)$new_balance['money'];

} catch (Exception $e) {
    http_response_code(400);
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>
