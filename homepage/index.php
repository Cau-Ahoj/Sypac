<?php

session_start();
if (!defined('BASE_URL')) {
  define('BASE_URL', '/');
}
require_once(__DIR__ . '/../database.php');



$db = new DB();

$menuItems = $db->getAll("SELECT * FROM main_menu ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Casino Sypáč</title>
    <link rel="stylesheet" href="../globalstyle.css">
    <link rel="stylesheet" href="style.css">


</head>
<body>
    <?php require "../header/index.php"?>
    <main>
      <section class="welcome_box">
        Hi username, welcome to Sypáč !!
      </section>
      <section class=games>
        <?php foreach ($menuItems as $item): ?>
          <a class="game_box" href="<?= BASE_URL . htmlspecialchars($item['path']) ?>/">
            <?= htmlspecialchars($item['name']) ?>
          </a>
        <?php endforeach; ?>
      </section>    
    </main>
    <?php require "../aside/index.php"?>    
    <?php require "../footer/index.php"?>

    <script src="../global.js" defer></script>
</body>
</html>