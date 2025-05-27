<?php
session_start();
require_once '../database.php';
$db = new DB();


if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? null;

    if (!$email) {
        $_SESSION['error'] = "Chybí e-mail.";
        header("Location: ./");
        exit;
    }

    // Získání uživatele
    $uzivatel = $db->getOne("SELECT id FROM users WHERE email = ?", [$email]);
    if (empty($uzivatel)) {
        $_SESSION['error'] = "E-mail nebyl nalezen.";
        header("Location: ./");
        exit;
    }

    $user_id = $uzivatel['id'];
    $code = rand(100000, 999999);
    $expires = date("Y-m-d H:i:s", time() + 1800);

    // Uložení do DB
    $db->run("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?", [
        $code, $expires, $email
    ]);

    // Zalogování požadavku
    $logText = "Žádost o reset hesla (e-mail: $email, kód: $code)";
    $db->run("INSERT INTO logs (user_id, action, detail) VALUES (?, 'password_reset_request', ?)", [
        $user_id, $logText
    ]);

    // Odeslání e-mailu (volitelně zakomentuj pro vývoj)
    $to = $email;
    $subject = "Obnova hesla – ověřovací kód";
    $message = "Zde je tvůj ověřovací kód: $code\n\nPlatnost: 30 minut.";
    $headers = "From: noreply@tvojedomena.cz\r\n";
    mail($to, $subject, $message, $headers);

    $_SESSION['email'] = $email;
    $_SESSION['visible_code'] = $code; // dočasně zobrazíme kód

    header("Location: verify-code.php");
    exit;
}   
?>
