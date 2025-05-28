<?php
// 📁 Struktura složky: shop/
// Tento index.php slouží jako hlavní stránka e-shopu

require_once("database.php");
$db = new DB();

// Načtení všech dostupných itemů z databáze přes metodu get()
$items = $db->get("SELECT * FROM items");

include_once('../../partials/header.php');
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop</title>
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="shop.css">
</head>
<body>
    <main>
        <h1>Obchod</h1>
        <div class="item-grid">
            <?php foreach ($items as $item): ?>
                <div class="item-card">
                    <h2><?= htmlspecialchars($item['name']) ?></h2>
                    <p><?= htmlspecialchars($item['description']) ?></p>
                    <p><strong><?= $item['price'] ?> Kč</strong></p>
                    <a href="detail.php?id=<?= urlencode($item['id']) ?>">Koupit</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    

    <script src="../../assets/js/global.js"></script>
    <script src="shop.js"></script>
</body>
</html>

<?php
include_once('../../partials/footer.php');
?>
