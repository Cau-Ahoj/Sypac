<?php
require_once "../../database.php";
$db = new DB();

session_start();
if ($_SESSION["csrf_token"] == $_POST["csrf_token"]) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = htmlspecialchars($_POST['username'])  ?? null;
        $password = htmlspecialchars($_POST['password'])  ?? null;

        if (!$username || !$password) {
            header("Location: login.php?error=missing&username=" . urlencode($username));
            exit;
        }

        $user = $db->getOne("SELECT * FROM users WHERE username = ?", [$username]);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $db->log($user['id'], 'login', 'Uživatel se přihlásil.');
                header("Location: ../../games/slots/slot.php");
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
} else {
    die("Neplatný CSRF token.");
}
?>
