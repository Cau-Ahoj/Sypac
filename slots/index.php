<?php
session_start();
require_once '../database.php';
$db = new DB();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../login/");
    exit;
}

$user = $db->getOne("SELECT money, xp FROM users WHERE id = ?", [$user_id]);

// 💰 Kredit – použij ten z posledního točení, pokud existuje
$currentMoney = $_SESSION['currentMoney'] ?? ($user['money'] ?? 0);
$currentXp = $user['xp'] ?? 0;
$currentLevel = floor($currentXp / 1000);

$result = $_SESSION['result'] ?? '';
$slots = $_SESSION['slots'] ?? [];
$spinCost = $_SESSION['spinCost'] ?? 50;
$winAmount = $_SESSION['winAmount'] ?? 0;

// ✅ Vyčistíme pouze to, co nemá zůstat dál
unset($_SESSION['result'], $_SESSION['slots'], $_SESSION['winAmount']);
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
    <div class="layout-container">
        <header>
            <?php require "../header/index.php" ?>
        </header>
        <aside>
            <?php require "../aside/index.php" ?>
        </aside>
        <main>
            <h1 class="slots-title">Slots</h1>
            <div class="slots-mainPart">
                <div class="slots-background">
                <div class="slots-credits">
                    <h2>WIN: <strong><?= $winAmount ?>,-</strong></h2>
                    <h2><strong>BET</strong>: <?= $spinCost ?>,-</h2>
                    <h2><strong>CREDIT</strong>: <?= $currentMoney ?>,-</h2>
                    <h2><strong>LEVEL</strong>: <?= $currentLevel ?></h2>
                    <h2><strong>XP</strong>: <?= $currentXp ?></h2>
                </div>
                <div class="slots-layout">
                    <section class="slot-reel">
                        <div class="slot-reel"><?= isset($slots[0]) ? $slots[0] : "?" ?></div>
                    </section>
                    <section class="slot-reel">
                        <div class="slot-reel"><?= isset($slots[1]) ? $slots[1] : "?" ?></div>
                    </section>
                    <section class="slot-reel">
                        <div class="slot-reel"><?= isset($slots[2]) ? $slots[2] : "?" ?></div>
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
                    <?php if (!empty($result)): ?>
                        <p class="result"><?= $result ?></p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
        </main>
        <footer>
            <?php require "../footer/index.php" ?>
        </footer>
    </div>
</body>
</html>
