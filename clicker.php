<?php
require_once 'database.php';
session_start();

$db = new DB();

// Provizorní nastavení user_id do session (jen pro test)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2; // např. 1 nebo zkus změnit na neexistující
}

$user_id = $_SESSION['user_id']; // <-- vždy nastavíme

// Zkontroluj, zda uživatel existuje
$user = $db->get("SELECT * FROM users WHERE id = ?", [$user_id]);

if (!$user) {
    die("❌ Uživatel s ID $user_id neexistuje.");
}

// Když bylo odesláno tlačítko:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_money = $user[0]['money'];
    $new_money = $current_money + 1;

    $db->run("UPDATE users SET money = ? WHERE id = ?", [$new_money, $user_id]);

    // Refresh pro zobrazení nové hodnoty
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Clicker hra</title>
</head>
<body>
    <h1>Clicker</h1>
    <form method="POST">
        <button type="submit">💰 Přidat peníze</button>
    </form>
    <p>
        <?php
        $updated_user = $db->get("SELECT money FROM users WHERE id = ?", [$user_id]);
        echo "Aktuální money pro uživatele s ID $user_id: " . $updated_user[0]['money'];
        ?>
    </p>
</body>
</html>
