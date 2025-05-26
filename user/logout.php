<?php
session_start();

require "../database.php";
$db = new DB();

if(!isset($_SESSION['user_id'])) {
  header("Location: ./login/login.php");
}
if($_SERVER["REQUEST_METHOD"] === 'POST') {
  $db->log($user['id'], 'LOGOUT', 'Uživatel se odhlásil.');
  session_destroy();
  header("Location: ./login/login.php");
  exit;

}


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

