<?php
session_start();

require "../database.php"; // cesta k DB třídě
$db = new DB();

// Pokud není uživatel přihlášen, přesměruj ho na login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

// Když uživatel odešle formulář
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $user_id = $_SESSION['user_id'];

    // Zaloguj odhlášení
    $db->log($user_id, 'LOGOUT', 'Uživatel se odhlásil.');

    // Zruš session
    unset($_SESSION['user_id']);
    session_destroy();

    // Přesměrování na login
    header("Location: ../login/");
    exit;
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <title>Odhlášení</title>
</head>  
<body>
  <h1>Jste přihlášen(a)</h1>
  <form method="POST" action="index.php">
    <button type="submit">ODHLÁSIT SE</button>
  </form>
</body>
</html>
