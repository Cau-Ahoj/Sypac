<?php
session_start();
require_once '../database.php';
$db = new DB();

// Uživatel musí být přihlášen
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../login/");
    exit;
}

// Získání kreditu z DB
$user = $db->getOne("SELECT money FROM users WHERE id = ?", [$user_id]);
$currentMoney = $user['money'] ?? 0;

// Výsledky a poslední výhra
$result = $_SESSION['result'] ?? '';
$slots = $_SESSION['slots'] ?? [];
$spinCost = $_SESSION['spinCost'] ?? 50;
$winAmount = $_SESSION['winAmount'] ?? 0;  // Poslední výhra

// Vyčištění, ale ponecháme spinCost
unset($_SESSION['result'], $_SESSION['slots']);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="global.css">
    <link rel="stylesheet" href="slots.css">
    <title>Slot Automat</title>
</head>
<body>
    <div class= layout-container>
        <header>
            <?php require "../header/index.php"?>
        </header>
        <aside>
            <?php require "../aside/index.php"?>
        </aside>
        <main>  
            <h1> Vítej ve hře automaty </h1>
            <div class="mainPart">
                <div class="credits">
                    <h2>WIN: <strong><?= $winAmount ?>,-</strong></h2>
                    <h2><strong>BET</strong>: <?= $spinCost ?>,-</h2>
                    <h2><strong>CREDIT</strong>: <?= $currentMoney ?>,-</h2>
                </div>
                <div class="slots-layout">    
                    <section class="slot-reel">
                        <div class="slot-reel"><?= $slots[0] ?? "?" ?></div>
                    </section>
                    <section class="slot-reel">
                        <div class="slot-reel"><?= $slots[1] ?? "?" ?></div>
                    </section>
                    <section class="slot-reel">
                        <div class="slot-reel"><?= $slots[2] ?? "?" ?></div>
                    </section>
                </div>
                <section class="controls"> 
                    <form method="post" action="slot.php">
                        <div class="bet-control">
                            <label for="spinCost">Sázka:</label>
                            <input type="number" id="spinCost" name="spinCost" value="<?= $spinCost ?>" min="10" max="200000" step="10">
                            <button type="submit">SPIN</button>
                        </div>
                    </form>
                    <?php if(!empty($result)): ?>
                        <p class="result"><?= $result ?></p>
                    <?php endif; ?>
                </section>
            </div>
        </main>
        <footer>
            <?php require "../footer/index.php"?>
        </footer>
    </div>
</body>
</html>