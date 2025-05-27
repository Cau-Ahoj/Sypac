<?php
session_start();
require_once '../database.php';
$db = new DB();

if (!isset($_SESSION['verified']) || !isset($_SESSION['email'])) {
    $db->log(null, 'password_change_failed', 'Neoprávněný přístup bez session');
    die("Neoprávněný přístup.");
}

$email = $_SESSION['email'];
$pass1 = $_POST['password'];
$pass2 = $_POST['confirm'];

if ($pass1 !== $pass2) {
    $db->log(null, 'password_change_failed', "Hesla se neshodují pro e-mail: $email");
    die("Hesla se neshodují.");
}

$hashed = password_hash($pass1, PASSWORD_DEFAULT);

$db->run("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?", [
    $hashed, $email
]);

$db->log(null, 'password_changed', "Heslo změněno pro e-mail: $email");

session_destroy();
echo "Heslo bylo úspěšně změněno.";
