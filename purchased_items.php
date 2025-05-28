<?php 
require "database.php";
session_start();

// Přihlášený uživatel
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
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
</head>
<body>
    <h1>Zakoupené položky uživatele ID <?= htmlspecialchars($user_id) ?></h1>

    <?php if (empty($items)): ?>
        <p>Nemáš zatím žádné zakoupené položky.</p>
    <?php else: ?>
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
    <?php endif; ?>
</body>
</html>
