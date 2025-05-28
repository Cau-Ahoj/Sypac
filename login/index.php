<?php
session_start();
$username = $_GET['username'] ?? '';
$error = $_GET['error'] ?? '';

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

if (isset($_SESSION['user_id'])) {
  header("Location: ../slots/slot.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"><title>Login</title></head>
<link rel="stylesheet" href="../globalstyle.css">
<link rel="stylesheet" href="style.css">
<body>
<?php require "../header/index.php"?>
<?php require "../aside/index.php"?>
  <?php if ($error === 'wrongpass'): ?>
    <p style="color:red;">❌ Wrong password.</p>
  <?php elseif ($error === 'nouser'): ?>
    <p style="color:red;">❌ User not found.</p>
  <?php elseif ($error === 'missing'): ?>
    <p style="color:red;">❌ Fill all fields.</p>
  <?php endif; ?>

  <form method="POST" action="validateLog.php">
    <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username) ?>" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token) ?>">

    <button type="submit">Login</button>
  </form>
  <?php require "../footer/index.php"?>
</body>
</html>

