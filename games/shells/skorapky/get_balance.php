<?php
session_start();
header('Content-Type: application/json');

require_once '../database.php';

$response = ['success' => false];

try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        throw new Exception("Uživatel není přihlášen.");
    }

    $user_id = $_SESSION['user_id'];
    $db = new DB();
    $result = $db->getOne("SELECT money FROM users WHERE id = ?", [$user_id]);

    if (!$result) {
        throw new Exception("Uživatel nenalezen.");
    }

    $response['success'] = true;
    $response['balance'] = (int)$result['money'];

} catch (Exception $e) {
    error_log("Chyba v get_balance.php: " . $e->getMessage());
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>
