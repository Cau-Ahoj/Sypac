<?php
session_start();
require_once '../database.php';
$db = new DB();

$code = $_POST['code'] ?? null;
$email = $_POST['email'] ?? null;

if (!$email || !$code) {
    $db->log(null, 'code_verification_failed', "Chybí kód nebo e-mail");
    die("❌ Chybí e-mail nebo kód.");
}

$uzivatel = $db->getOne("SELECT * FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()", [
    $email, $code
]);

if (!empty($uzivatel)) {
    $_SESSION['verified'] = true;
    $_SESSION['email'] = $email;

    $db->run("UPDATE users SET reset_token = NULL, reset_expires = NULL WHERE email = ?", [$email]);

    $db->log($uzivatel['id'], 'code_verified', "Kód $code úspěšně ověřen pro $email");

    header("Location: ./new-password.php");
    exit;
} else {
    $db->log(null, 'code_verification_failed', "Neplatný nebo expirovaný kód: $code pro e-mail: $email");
    echo "❌ Neplatný nebo expirovaný kód. Zkuste to prosím znovu.";
}
exit;
