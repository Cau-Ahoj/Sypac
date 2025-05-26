<?php
session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: ./login/login.php");
}

session_destroy();
?>
<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"><title>Přihlášení</title></head>  
<body>
  <form method="POST" action="logout.php">
    <button type="submit">LOGOUT</button>
  </form>
</body>
</html>

