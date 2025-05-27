<?php
// Soubor: validate.php

// Vždy nastavte Content-Type hlavičku na application/json
header('Content-Type: application/json');

// Potlačení zobrazení chyb pro produkční prostředí. Chyby by se měly logovat.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL); // Nastavte pro logování všech chyb

// Zahrňte soubor s třídou DB
require_once 'database.php'; // Cesta k database.php

// Spusťte session, abyste mohli přistupovat k $_SESSION
session_start();

$response = ['success' => false, 'error' => 'Neznámá chyba serveru.'];

try {
    $db = new DB();

    // Získání RAW POST dat (JSON)
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true); // true pro asociativní pole

    // Zkontrolujte, zda byla data úspěšně dekódována
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Neplatná JSON data odeslaná klientem: " . json_last_error_msg());
    }

    // --- KLÍČOVÁ ZMĚNA: Získání ID uživatele z $_SESSION['user_id'] ---
    // Toto odpovídá tomu, co ukládá váš validateLog.php.
    $user_id = $_SESSION['user_id'] ?? null;

    $reward_id = $data['reward_id'] ?? null;

    if (!$user_id) {
        http_response_code(401);
        throw new Exception('Uživatel není přihlášen. Prosím přihlaste se pro uplatnění odměny.');
    }
    if (!$reward_id) {
        http_response_code(400);
        throw new Exception('Chybí ID odměny.');
    }

    // Získat typ a hodnotu výhry z tabulky 'rewards'
    $reward = $db->getOne("SELECT type, amount FROM rewards WHERE id = ?", [$reward_id]);

    if (empty($reward)) {
        http_response_code(404);
        throw new Exception('Výhra s daným ID nenalezena v databázi.');
    }

    $type = $reward['type'];
    $amount = (int)$reward['amount'];

    // !!! KLÍČOVÁ LOGIKA PRO UKLÁDÁNÍ PENĚZ A XP K UŽIVATELI POUZE DO TABULKY USERS !!!
    if ($type === 'money') {
        $db->run("UPDATE users SET money = money + ? WHERE id = ?", [$amount, $user_id]);
        $db->log($user_id, 'reward_money', "Získal(a) $amount peněz.");
    } elseif ($type === 'xp') {
        $db->run("UPDATE users SET xp = xp + ? WHERE id = ?", [$amount, $user_id]);
        $db->log($user_id, 'reward_xp', "Získal(a) $amount XP.");
    } else {
        error_log("Neznámý typ výhry ve validate.php: " . $type . " pro reward_id: " . $reward_id);
    }

    $response['success'] = true;
    unset($response['error']);

} catch (Exception $e) {
    error_log("Chyba ve validate.php: " . $e->getMessage());
    if (http_response_code() === 200) {
        http_response_code(500);
    }
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>