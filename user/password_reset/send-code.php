<?php

session_start();
require_once 'database.php'; 

$db = new DB();

$email = $_POST['email'] ?? null;

if (!$email) {
    die("❌ Chybí e-mail.");
}

// 1. Krok: Ověření, zda e-mail existuje v databázi
$uzivatel = $db->get("SELECT id FROM users WHERE email = ?", [$email]);

if (empty($uzivatel)) {
    echo "<p>❌ E-mail nebyl nalezen. Zkontrolujte prosím zadanou adresu.</p>";
    exit; 
}

// 2. Krok: Pokud e-mail existuje, pokračujeme s generováním a odesíláním kódu
$code = rand(100000, 999999); // Kód 6-místný
$expires = date("Y-m-d H:i:s", time() + 1800); // Platnost 30 minut

// Uložení tokenu do databáze pro existujícího uživatele
$db->run("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?", [
    $code, $expires, $email
]);
/**
 * ZDE MÁ BÝT IMPLEMENTACE E-MAILOVÉHO AGENTA (NAPŘ. PHPMailer, Symfony Mailer)
 */
// Původní blok kódu pro odesílání e-mailu pomocí vestavěné funkce mail()
// Tento blok by byl nahrazen výše uvedeným kódem s PHPMailerem
// Odeslání e-mailu (zde můžeš vložit kód s PHPMailerem, jak jsme si ukazovali, nebo ponechat mail() )
$to = $email;
$subject = "Obnova hesla – ověřovací kód";
$message = "Zde je tvůj ověřovací kód: $code\n\nPlatnost: 30 minut.";
$headers = "From: noreply@tvojedomena.cz\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "<p>✅ Kód byl odeslán na $email.</p> (Může to trvat i několik minut než příjde email!!)";
} else {
    echo "<p>❌ Nepodařilo se odeslat e-mail. Zkuste to prosím později.</p>";
}
echo "<form action='verify-code.php' method='post'>
        <input type='hidden' name='email' value='" . htmlspecialchars($email) . "'>
        <input type='text' name='code' placeholder='Zadej ověřovací kód' required>
        <button type='submit'>Ověřit</button>
      </form>";

?> 
