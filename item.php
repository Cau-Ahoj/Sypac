<?php 
require "database.php";
session_start();
$_SESSION["user_id"] = 1; // <-- sem vlož pro test

// Kontrola přihlášení
if (!isset($_SESSION["user_id"])) {
    $_SESSION["user_id"] = 1; // Testovací User ID
}
// --- Konec simulace DB a Session ---

$db = new DB();

$user_id = $_SESSION["user_id"];
$item_id = $_GET["id"] ?? null;

if (!$item_id) {
    // Tuto zprávu také nastylovat, pokud by k ní došlo
    echo "<p class='critical-error-message'>Chyba: ID položky nebylo specifikováno.</p>";
    exit;
}

$item = $db->getOne("SELECT * FROM items WHERE id = ?", [$item_id]);
$alreadyBought = null;
if ($item) {
    $alreadyBought = $db->getOne("SELECT * FROM purchases WHERE user_id = ? AND item_id = ?", [$user_id, $item_id]);
}

$info = $_SESSION['info'] ?? '';
unset($_SESSION['info']);

if ($_SERVER["REQUEST_METHOD"] === "POST" && $item) { // Přidána kontrola $item pro POST
    $user = $db->getOne("SELECT money FROM users WHERE id = ?", [$user_id]);

    if (isset($_POST['buy']) && !$alreadyBought) {
        if ($user && $user['money'] < $item['price']) {
            $_SESSION['info'] = "Nemáš dostatek peněz na nákup.";
        } elseif ($user) {
            $new_money = $user['money'] - $item['price'];
            $db->run("UPDATE users SET money = ? WHERE id = ?", [$new_money, $user_id]);
            $db->run("INSERT INTO purchases (user_id, item_id) VALUES (?, ?)", [$user_id, $item_id]);
            $_SESSION['info'] = "Položka úspěšně zakoupena!";
        } else {
            $_SESSION['info'] = "Chyba při načítání uživatele.";
        }
    }

    if (isset($_POST['sell']) && $alreadyBought) {
        if ($user) {
            $new_money = $user['money'] + $item['sellprice'];
            $db->run("UPDATE users SET money = ? WHERE id = ?", [$new_money, $user_id]);
            $db->run("DELETE FROM purchases WHERE user_id = ? AND item_id = ?", [$user_id, $item_id]);
            $_SESSION['info'] = "Položka úspěšně prodána.";
        } else {
            $_SESSION['info'] = "Chyba při načítání uživatele.";
        }
    }
    header("Location: item.php?id=" . urlencode($item_id));
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Položka - <?= $item ? htmlspecialchars($item['name']) : 'Nenalezena' ?></title>
    <link rel="stylesheet" href="style.css"> <!-- Link na stejný CSS soubor -->
</head>
<body id="page-item-detail"> <!-- ID pro specifické stylování této stránky -->

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
                <h1>Detail Položky</h1>
            </div>

            <?php if ($item): ?>
                <div class="item-detail-container">
                    <div class="item-visual-column">
                        <div class="item-image-placeholder">
                            <span>Náhled Produktu</span>
                        </div>
                    </div>
                    <div class="item-info-column">
                        <h2 class="item-name-heading"><?= htmlspecialchars($item['name']) ?></h2>
                        
                        <div class="item-attributes">
                            <p class="item-description"><strong>Popis:</strong> <?= htmlspecialchars($item['description']) ?></p>
                            <p class="item-price"><strong>Cena:</strong> <?= (int)$item['price'] ?> Kč</p>
                            <p class="item-bonus"><strong>Bonus:</strong> <?= htmlspecialchars($item['bonus_type']) ?> (<?= (int)$item['bonus_value'] ?>)</p>
                            <p class="item-sellprice"><strong>Prodejní cena:</strong> <?= (int)$item['sellprice'] ?> Kč</p>
                            <!-- <p>Obrázek: <?= htmlspecialchars($item['image']) ?></p> -->
                        </div>

                        <div class="item-actions">
                            <?php if ($alreadyBought): ?>
                                <p class="ownership-status">Tuto položku již vlastníš.</p>
                                <form method="post" class="action-form">
                                    <button type="submit" name="sell" class="styled-button sell-button">
                                        PRODAT za <?= (int)$item['sellprice'] ?> Kč
                                    </button>
                                </form>
                            <?php else: ?>
                                <p class="ownership-status">Tuto položku nevlastníš.</p>
                                <form method="post" class="action-form">
                                    <button type="submit" name="buy" class="styled-button buy-button">
                                        KOUPIT za <?= (int)$item['price'] ?> Kč
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($info): ?>
                    <p class="info-message general-info"><?= htmlspecialchars($info) ?></p>
                <?php endif; ?>

            <?php else: ?>
                <p class="error-message-standalone">Položka s ID <?= htmlspecialchars($item_id) ?> nebyla nalezena.</p>
            <?php endif; ?>

            <!-- Můžeš ponechat pro debugování, nebo odstranit/skrýt přes CSS -->
            <!-- <p class="debug-info">Uživatel ID: <?= htmlspecialchars($user_id) ?></p> -->

        </main>
    </div>
</body>
</html>