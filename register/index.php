<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit;
}

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

    if ($_GET["error"] === "duplicate") {
        echo '<p style="color:red;">❌ Uživatelské jméno nebo e-mail již existuje.</p>';
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../globalstyle.css">
    <title>Register</title>
</head>

    <body class = "register_page-body">
        <header>
            <?php require "../header/index.php"?>
        </header>
        <aside>
            <?php require "../aside/index.php"?>
        </aside>
        <main class = "register_page-main">
            <article>
                <h1 class = "register_page-h1-without-margin">REGISTER</h1>
                <form action= "validateReg.php" class ="register_page-form" method="post">
                    <div class="register_page-pole">
                        <label for="username" class="register_page-label">USERNAME</label>
                        <input type="text" name="username" class ="register_page-input" value="<?php echo htmlspecialchars($username) ?>" required>
                    </div>

                    <div class="register_page-pole">
                        <label for="email" class="register_page-label">EMAIL</label>
                        <input type="email" name="email" class ="register_page-input" value="<?php echo htmlspecialchars($email) ?>" required>
                    </div>

                    <div class="register_page-pole">
                        <label for="phone" class="register_page-label">TELEPHONE NUMBER</label>
                        <input type="tel" name="phone" class ="register_page-input" value="<?php echo htmlspecialchars($phone) ?>" required>
                    </div>

                    <input type="hidden" name="csrf_token" class ="register_page-input" value="<?php echo htmlspecialchars($token) ?>">

                    <div class="register_page-pole">
                        <label for="password" class="register_page-label">PASSWORD</label>
                        <input type="password" name="password" class ="register_page-input" required>
                    </div>

                    <div class="register_page-pole">
                        <label for="password_confirm" class="register_page-label">CONFIRM PASSWORD</label>
                        <input type="password" name="password_confirm" class ="register_page-input" required>
                    </div>
                    
                    <p class ="register_page-note">Already have an account?
                        <a href="../login/" class="register_page-link">Login</a>
                    </p>
                    <button type="submit" class ="register_page-button">
                        REGISTER
                    </button>
                </form>
            </article>
        </main>
        <footer>
            <?php require "../footer/index.php"?>
        </footer>
    </body>
</html>

