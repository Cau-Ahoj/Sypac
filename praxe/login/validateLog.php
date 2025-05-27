<?php
require_once "../database.php";
$db = new DB();

session_start();

// ✅ 1. Povolit pouze POST metodu
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ./?error=invalid_access");
    exit;
}

// ✅ 2. Ověřit CSRF token
if (!isset($_POST["csrf_token"]) || $_SESSION["csrf_token"] !== $_POST["csrf_token"]) {
    die("Neplatný CSRF token.");
}

// ✅ 3. Získat a zkontrolovat vstupy
$username = htmlspecialchars($_POST['username']) ?? null;
$password = htmlspecialchars($_POST['password']) ?? null;

if (!$username || !$password) {
    header("Location: ./?error=missing&username=" . urlencode($username));
    exit;
}

// ✅ 4. Najít uživatele
$user = $db->getOne("SELECT * FROM users WHERE username = ?", [$username]);

if ($user) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $db->log($user['id'], 'login', 'Uživatel se přihlásil.');
        header("Location: ../slots/");
        exit;
    } else {
        header("Location: ./?error=wrongpass&username=" . urlencode($username));
        exit;
    }
} else {
    header("Location: ./?error=nouser&username=" . urlencode($username));
    exit;
}
?>
