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
</head>
<body>
    <a href="purchased_items.php">
    <button>Zobrazit zakoupené položky</button>
    </a>
    <h1>Seznam položek</h1>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                
                <a href="./item.php?id=<?= urlencode($item['id']) ?>">
                    <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                    Popis: <?= htmlspecialchars($item['description']) ?><br>
                    Cena: <?= (int)$item['price'] ?> Kč<br>
                    Bonus: <?= htmlspecialchars($item['bonus_type']) ?> (<?= (int)$item['bonus_value'] ?>)<br>
                    Obrázek: <?= htmlspecialchars($item['image']) ?><br>
                    Prodejní cena: <?= (int)$item['sellprice'] ?> Kč
                 
                </a>
                
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
</body>
</html>
