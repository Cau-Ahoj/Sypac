<?php
session_start();
require_once '../database.php';
$db = new DB();
session_start();    


if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$message = $_SESSION['message'] ?? '';
$email_for_verification = $_SESSION['email_for_verification'] ?? '';

unset($_SESSION['message']); // Smaž zprávu po zobrazení

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ověření kódu</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Ověření kódu</h1>

    <?php if ($message): ?>
        <p class="
            <?php
            if (strpos($message, '❌') !== false) {
                echo 'error';
            } else if (strpos($message, '✅') !== false) {
                echo 'success';
            }
            ?>
        "><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if ($email_for_verification): ?>
        <p>Na zadanou e-mailovou adresu byl odeslán ověřovací kód. Zadejte jej prosím níže.</p>
        <form action="verify-code.php" method="post">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_for_verification); ?>">
            <input type="text" name="code" placeholder="Zadej ověřovací kód" required>
            <button type="submit">Ověřit</button>
        </form>
    <?php else: ?>
        <p>Pro ověření kódu prosím nejprve zadejte svůj e-mail na <a href="./">hlavní stránce obnovy hesla</a>.</p> <?php endif; ?>
</body>
</html>