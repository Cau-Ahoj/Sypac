<?php 
session_start();
require "../../database.php";
$db = new DB();

if ($_SESSION["csrf_token"] == $_POST["csrf_token"]) {
    if (isset($_POST["username"], $_POST["password"], $_POST["password_confirm"], $_POST["email"], $_POST["phone"])) {

        if ($_POST["password"] !== $_POST["password_confirm"]) {
            header("Location: register.php?error=password_mismatch"
                . "&username=" . urlencode($_POST["username"])
                . "&email=" . urlencode($_POST["email"])
                . "&phone=" . urlencode($_POST["phone"])
            );
            exit();
        }

        $username = htmlspecialchars(trim($_POST["username"]));
        $email = htmlspecialchars(trim($_POST["email"]));
        $phone = htmlspecialchars(trim($_POST["phone"]));
        $hashed_password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
        $initial_money = 200;

        $success = $db->run("INSERT INTO users (username, email, telefon, password, money) VALUES (?, ?, ?, ?, ?)", [
            $username,
            $email,
            $phone,
            $hashed_password,
            $initial_money
        ]);

        if ($success) {
            header("Location: ../login/login.php?success=1");
            exit();
        } else {
            header("Location: register.php?error=db&username=" . urlencode($username) . "&email=" . urlencode($email) . "&phone=" . urlencode($phone));
            exit();
        }

    } else {
        header("Location: register.php?error=missing_fields");
        exit();
    }
} else {
    die("Neplatný CSRF token.");
}

?>
