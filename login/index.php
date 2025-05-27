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
<head><meta charset="UTF-8"><title>Přihlášení</title></head>  
<body>
  <?php if ($error === 'wrongpass'): ?>
    <p style="color:red;">❌ Špatné heslo.</p>
  <?php elseif ($error === 'nouser'): ?>
    <p style="color:red;">❌ Uživatel nenalezen.</p>
  <?php elseif ($error === 'missing'): ?>
    <p style="color:red;">❌ Vyplňte všechna pole.</p>
  <?php endif; ?>

  <form method="POST" action="validateLog.php">
    <input type="text" name="username" placeholder="Uživatelské jméno" value="<?php echo htmlspecialchars($username) ?>" required>
    <input type="password" name="password" placeholder="Heslo" required>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token) ?>">

    <button type="submit">Přihlásit se</button>
  </form>
</body>
</html>

