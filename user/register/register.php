<?php
session_start();


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

$username = isset($_GET["username"]) ? urldecode($_GET["username"]) : "";
$email = isset($_GET["email"]) ? urldecode($_GET["email"]) : "";
$phone = isset($_GET["phone"]) ? urldecode($_GET["phone"]) : "";

if (isset($_GET["error"])) {
    if ($_GET["error"] === "password_mismatch") {
        echo '<p style="color:red;">❌ Hesla se neshodují.</p>';
    }
    // další chyby ...
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
        <div>
            <form action= "validateReg.php" method="post">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username) ?>" required>

                <input type="email" name="email" placeholder="E-mail" value="<?php echo htmlspecialchars($email) ?>" required>

                <input type="tel" name="phone" placeholder="Phone number" value="<?php echo htmlspecialchars($phone) ?>" required>

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token) ?>">

                <input type="password" name="password" placeholder="Password" required>

                <input type="password" name="password_confirm" placeholder="Confirm password" required>

                    <button type="submit">
                        Done
                    </button>
                </div>
                <p>Already have an account? <br>
                    <a href="../login/login.php">Login</a></p>
            </form>
        </div>
</body>
</html>
