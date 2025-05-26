<?php
require_once "database.php";
$db = new DB();

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$username || !$password) {
        header("Location: login.php?error=missing&username=" . urlencode($username));
        exit;
    }

    $users = $db->get("SELECT * FROM users WHERE username = ?", [$username]);

    if (count($users) === 1) {
        $user = $users[0];

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $db->log($user['id'], 'login', 'Uživatel se přihlásil.');

            echo "Přihlášení úspěšné. Vítej, " . htmlspecialchars($user['username']) . "!";
            exit;
        } else {
            header("Location: login.php?error=wrongpass&username=" . urlencode($username));
            exit;
        }
    } else {
        header("Location: login.php?error=nouser&username=" . urlencode($username));
        exit;
    }
} else {
    header("Location: login.php?error=invalid");
    exit;
}
?>
