<?php
session_start();

$message = $_SESSION['message'] ?? ''; // Načti zprávu ze session
unset($_SESSION['message']); // A ihned ji smaž, aby se nezobrazovala při dalším načtení


if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obnova hesla</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Obnova hesla</h1> 

    <?php if ($message): ?>
        <p class="
            <?php
            // Určení třídy pro zprávu
            if (strpos($message, '❌') !== false) {
                echo 'error';
            } else if (strpos($message, '✅') !== false) {
                echo 'success';
            }
            ?>
        "><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="send-code.php" method="post">
        <input type="email" name="email" required placeholder="Zadej e-mail">
        <button type="submit">Odeslat kód</button>
    </form>
</body>
</html>