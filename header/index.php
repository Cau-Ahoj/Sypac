<?php
session_start();
if (!defined('BASE_URL')) {
  define('BASE_URL', '/');
}
?>
<header>
  <div class="search_bar">
    <input type="search">
  </div>
  <div class="header_buttons">
  <div class="header_buttons_box">
  <?php if(isset($_SESSION["user_id"])):?>
    <a href="<?= BASE_URL ?>user">
      <button>Profil</button>
    </a>
    <a href="<?= BASE_URL ?>logout">
          <button>Odhlásit se</button>
        </a>
  <?php elseif(!isset($_SESSION["user_id"])):?>
    <a href="<?= BASE_URL ?>login">
      <button>Přihlášení</button>
    </a>
<?php endif;?>

  </div>
  </div>
</header>