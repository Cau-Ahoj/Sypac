<?php
$username = $_GET['username'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="cs">
<head><meta charset="UTF-8"><title>Přihlášení</title></head>  
<form method="POST" action="validateLog.php">
<body>
  <?php if ($error === 'wrongpass'): ?>
    <p style="color:red;">Špatné heslo.</p>
  <?php elseif ($error === 'nouser'): ?>
    <p style="color:red;">Uživatel nenalezen.</p>
  <?php elseif ($error === 'missing'): ?>
    <p style="color:red;">Vyplňte všechna pole.</p>
  <?php endif; ?>

  <form method="POST" action="index.php">
    <input type="text" name="username" placeholder="Uživatelské jméno" value="<?= htmlspecialchars($username) ?>" required>
    <input type="password" name="password" placeholder="Heslo" required>

    <button type="submit">Přihlásit se</button>
  </form>
</body>
</html>

