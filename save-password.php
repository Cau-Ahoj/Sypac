<?php
session_start();
require_once 'database.php';
$db = new DB();

if (!isset($_SESSION['verified'], $_SESSION['email'])) {
    header("Location: forgot.php");
    exit;
}

$email = $_SESSION['email'];
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['confirm'] ?? '';

if ($pass1 !== $pass2) {
    $_SESSION['error'] = "Hesla se neshodují.";
    header("Location: new-password.php");
    exit;
}

$hashed = password_hash($pass1, PASSWORD_DEFAULT);

$db->run("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?", [
    $hashed, $email
]);

session_destroy();
header("Location: forgot.php?status=done");
exit;
?>
