<?php
session_start();
require_once 'db/database.php';
$db = new DB();

if (!isset($_SESSION['user_id'], $_SESSION['game_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$gameId = $_SESSION['game_id'];

// Získání hráčů
$players = $db->getAll("SELECT u.username FROM players p JOIN users u ON p.user_id = u.id WHERE p.game_id = ? AND p.left = 0", [$gameId]);
$count = count($players);

?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <title>Čekání na soupeře</title>
</head>
<body>
<h1>Čekání na soupeře</h1>
<p><b>ID hry:</b> <?= htmlspecialchars($gameId) ?></p>
<p><b>Jsi:</b> <?= htmlspecialchars($username) ?></p>

<h3>Připojení hráči:</h3>
<ul>
    <?php foreach ($players as $p): ?>
        <li><?= htmlspecialchars($p['username']) ?></li>
    <?php endforeach; ?>
</ul>

<?php if ($count >= 2): ?>
<form method="post" action="game.php">
    <button type="submit">Začít hru</button>
</form>
<?php else: ?>
<p>Čeká se na dalšího hráče…</p>
<?php endif; ?>

<form method="post" action="single/index.php" style="margin-top: 1em;">
    <button type="submit">Privatní hra</button>
</form>

<form method="post" action="leave.php" style="margin-top: 1em;">
    <button type="submit">Zpět</button>
</form>

<script>
setTimeout(() => location.reload(), 3000);
</script>
</body>
</html>