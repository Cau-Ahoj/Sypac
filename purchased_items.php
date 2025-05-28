<?php 
require "database.php";
session_start();

// Přihlášený uživatel
if (!isset($_SESSION["user_id"])) {
    $_SESSION["user_id"] = 1; // Testovací User ID
}
// --- Konec simulace DB a Session ---


// Přihlášený uživatel
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html"); // Toto by mělo být mimo HTML, ideálně na začátku souboru
    exit;
}

$user_id = $_SESSION["user_id"];
$db = new DB();

// Načtení zakoupených itemů
$items = $db->get("
    SELECT items.*
    FROM purchases
    JOIN items ON purchases.item_id = items.id
    WHERE purchases.user_id = ?
", [$user_id]);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zakoupené položky</title>
    <link rel="stylesheet" href="style.css"> <!-- Link na stejný CSS soubor -->
</head>
<body id="page-purchased-items"> <!-- ID pro specifické stylování této stránky -->

    <header class="site-header">
        <div class="header-bar"></div>
        <div class="header-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </header>

    <div class="page-wrapper">
        <aside class="equipment-panel">
            <div class="equipment-slot"></div>
            <div class="equipment-slot"></div>
            <div class="equipment-slot"></div>
            <div class="equipment-slot"></div>
            <div class="equipment-slot"></div>
            <div class="equipment-slot large"></div>
        </aside>

        <main class="main-content-area">
            <div class="content-header">
                 <h1>Zakoupené položky uživatele ID <?= htmlspecialchars($user_id) ?></h1>
            </div>

            <?php if (empty($items)): ?>
                <p class="empty-list-message">Nemáš zatím žádné zakoupené položky.</p>
            <?php else: ?>
                <ul class="purchased-items-list">
                    <?php foreach ($items as $item): ?>
                        <li class="purchased-item-card">
                            <a href="./item.php?id=<?= urlencode($item['id']) ?>" class="item-card-link">
                                <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                                Popis: <?= htmlspecialchars($item['description']) ?><br>
                                Původní cena: <?= (int)$item['price'] ?> Kč<br> <!-- "Cena" zde může být matoucí, lépe "Původní cena" nebo "Hodnota" -->
                                Bonus: <?= htmlspecialchars($item['bonus_type']) ?> (<?= (int)$item['bonus_value'] ?>)<br>
                                <!-- Obrázek: <?= htmlspecialchars($item['image']) ?><br> --> <!-- Obrázek zde nemusí být, pokud je to jen seznam -->
                                Možno prodat za: <?= (int)$item['sellprice'] ?> Kč
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="navigation-buttons">
                <a href="index.php" class="styled-button-link"> <!-- Upravte odkaz na hlavní stránku obchodu -->
                    <button class="styled-button">Zpět do obchodu</button>
                </a>
            </div>
        </main>
    </div>
</body>
</html>