<?php 
require "database.php";
$db = new DB();

// Načtení všech itemů z tabulky
$items = $db->get("SELECT * FROM items");
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Položky</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
                <h1>Seznam položek</h1>
                <a href="purchased_items.php" class="styled-button-link">
                    <button class="styled-button">Zobrazit zakoupené položky</button>
                </a>
            </div>
            
            <ul class="items-list-display">
                <?php foreach ($items as $item): ?>
                    <li class="item-card">
                        <a href="./item.php?id=<?= urlencode($item['id']) ?>" class="item-card-link">
                            <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                            Popis: <?= htmlspecialchars($item['description']) ?><br>
                            Cena: <?= (int)$item['price'] ?> Kč<br>
                            Bonus: <?= htmlspecialchars($item['bonus_type']) ?> (<?= (int)$item['bonus_value'] ?>)<br>
                            Obrázek: <?= htmlspecialchars($item['image']) ?><br>
                            Prodejní cena: <?= (int)$item['sellprice'] ?> Kč
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <li class="empty-list-message">Žádné položky k zobrazení.</li>
                <?php endif; ?>
            </ul>
        </main>
    </div>
</body>
</html>