<?php

session_start();
if (!defined('BASE_URL')) {
  define('BASE_URL', '/');
}
require_once(__DIR__ . '/../database.php');



$db = new DB();

$menuItems = $db->getAll("SELECT * FROM main_menu ORDER BY id ASC");
?>
<aside>
  <div class="logo">
   <a href="<?= BASE_URL ?>"> <h1>CASINO <br> SYPÁČ</h1></a>
  </div>
  <div class="navigation">
    <nav>
      <ul>
      <?php foreach ($menuItems as $item): ?>
        <li>
          <a href="<?= BASE_URL . htmlspecialchars($item['path']) ?>/">
            <button><?= htmlspecialchars($item['name']) ?></button>
          </a>
        </li>
      <?php endforeach; ?>
      </ul>
    </nav>
  </div>
  <div class="active_time">
    Active time:
  </div>
</aside>
<header>
