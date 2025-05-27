<?php
session_start();
require_once '../database.php';
$db = new DB();


if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Pokud přišel POST, zpracuj ověření kódu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? null;
    $code = $_POST['code'] ?? null;

    if (!$email || !$code) {
        $_SESSION['error'] = "Chybí e-mail nebo kód.";
        header("Location: verify-code.php");
        exit;
    }

    // Ověření kódu v databázi
    $uzivatel = $db->getOne(
        "SELECT * FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()",
        [$email, $code]
    );

    if (!empty($uzivatel)) {
        $_SESSION['verified'] = true;
        $_SESSION['email'] = $email;

        // Zneplatni použitý token
        $db->run("UPDATE users SET reset_token = NULL, reset_expires = NULL WHERE email = ?", [$email]);

        header("Location: new-password.php");
        exit;
    } else {
        $_SESSION['error'] = "❌ Neplatný nebo expirovaný kód.";
        header("Location: verify-code.php");
        exit;
    }
}

// Pokud GET požadavek, zobraz formulář
$email = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Ověření kódu</title>
</head>
<body>
    <h2>Zadej ověřovací kód</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red"><?= $_SESSION['error'] ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['visible_code'])): ?>
        <p><strong>⚠️ Testovací kód:</strong> <?= htmlspecialchars($_SESSION['visible_code']) ?></p>
        <?php unset($_SESSION['visible_code']); ?>
    <?php endif; ?>

    <form action="verify-code.php" method="post">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="text" name="code" required placeholder="Zadej kód">
        <button type="submit">Ověřit</button>
    </form>
</body>
</html>
