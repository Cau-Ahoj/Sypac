<?php 
require "database.php";
session_start();

// Kontrola přihlášení
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit;
}

$db = new DB();

$user_id = $_SESSION["user_id"];
$item_id = $_GET["id"] ?? null;

if (!$item_id) {
    echo "<p>Položka nebyla nalezena.</p>";
    exit;
}

// Načtení položky
$item = $db->getOne("SELECT * FROM items WHERE id = ?", [$item_id]);

// Zjisti, zda už uživatel položku vlastní
$alreadyBought = null;
if ($item) {
    $alreadyBought = $db->getOne("SELECT * FROM purchases WHERE user_id = ? AND item_id = ?", [$user_id, $item_id]);
}

// Info zpráva po přesměrování
$info = $_SESSION['info'] ?? '';
unset($_SESSION['info']);

// Zpracování nákupu
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $db->getOne("SELECT money FROM users WHERE id = ?", [$user_id]);

    // NÁKUP
    if (isset($_POST['buy']) && $item && !$alreadyBought) {
        if ($user['money'] < $item['price']) {
            $_SESSION['info'] = "Nemáš dostatek peněz na nákup.";
        } else {
            $new_money = $user['money'] - $item['price'];
            $db->run("UPDATE users SET money = ? WHERE id = ?", [$new_money, $user_id]);
            $db->run("INSERT INTO purchases (user_id, item_id) VALUES (?, ?)", [$user_id, $item_id]);

            $_SESSION['info'] = "Položka úspěšně zakoupena!";
        }
    }

    // PRODEJ
    if (isset($_POST['sell']) && $item && $alreadyBought) {
        $new_money = $user['money'] + $item['sellprice'];
        $db->run("UPDATE users SET money = ? WHERE id = ?", [$new_money, $user_id]);
        $db->run("DELETE FROM purchases WHERE user_id = ? AND item_id = ?", [$user_id, $item_id]);

        $_SESSION['info'] = "Položka úspěšně prodána.";
    }

    // Přesměrování (PRG)
    header("Location: item.php?id=" . urlencode($item_id));
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Položka</title>
</head>
<body>
    <h1>POLOŽKA</h1>

    <p><strong>Uživatel ID:</strong> <?= htmlspecialchars($user_id) ?></p>

    <?php if ($item): ?>
        <strong><?= htmlspecialchars($item['name']) ?></strong><br>
        Popis: <?= htmlspecialchars($item['description']) ?><br>
        Cena: <?= (int)$item['price'] ?> Kč<br>
        Bonus: <?= htmlspecialchars($item['bonus_type']) ?> (<?= (int)$item['bonus_value'] ?>)<br>
        Obrázek: <?= htmlspecialchars($item['image']) ?><br>
        Prodejní cena: <?= (int)$item['sellprice'] ?> Kč<br><br>

        <?php if ($alreadyBought): ?>
            <p><strong>Tuto položku už vlastníš.</strong></p>
            <form method="post">
                <button type="submit" name="sell">PRODAT za <?= (int)$item['sellprice'] ?> Kč</button>
            </form>
        <?php else: ?>
            <form method="post">
                <button type="submit" name="buy">BUY</button>
            </form>
        <?php endif; ?>

        <?php if ($info): ?>
            <p><strong><?= htmlspecialchars($info) ?></strong></p>
        <?php endif; ?>

    <?php else: ?>
        <p>Položka nebyla nalezena.</p>
    <?php endif; ?>
</body>
</html>
