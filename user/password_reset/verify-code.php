<?php
session_start();
require_once 'database.php';

$db = new DB();

$code = $_POST['code'] ?? null;
$email = $_POST['email'] ?? null;

if (!$email || !$code) { // Zjednodušeno pro lepší čitelnost
    die("❌ Chybí e-mail nebo kód.");
}   

// Ověření v databázi
$uzivatel = $db->getOne("SELECT * FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()", [
    $email, $code
]);

if (!empty($uzivatel)) {
    $_SESSION['verified'] = true;
    $_SESSION['email'] = $email;

    // Důležité: Zneplatnění tokenu po úspěšném ověření
    $db->run("UPDATE users SET reset_token = NULL, reset_expires = NULL WHERE email = ?", [$email]);

    header("Location: new-password.php");
    exit;
} else {
    // Neúspěšné ověření
    echo "❌ Neplatný nebo expirovaný kód. Zkuste to prosím znovu.";
    // Pro produkci: Vyhněte se vypisování citlivých detailů (jako je $check)
    // Pro debugování na dev prostředí můžete nechat, ale na produkci odstranit!
    /*
    echo "<pre>";
    echo "Zadaný e-mail: " . htmlspecialchars($email) . "\n";
    echo "Zadaný kód: " . htmlspecialchars($code) . "\n";
    $check = $db->get("SELECT reset_token, reset_expires FROM users WHERE email = ?", [$email]);
    print_r($check);
    echo "</pre>";
    */
}
?>