<?php
require_once '../database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/");
    exit;
}

$db = new DB();
$user_id = $_SESSION['user_id'];
$user = $db->getOne("SELECT * FROM users WHERE id = ?", [$user_id]);

$level = max(0, $user['lvl']);
$multiplier = pow(1.2, $level);

$estimatedMoney = round(1 * $multiplier);
$estimatedXp = round(1 * $multiplier);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../globalstyle.css">
    <title>Casino Clicker</title>
</head>
<body class="clicker_page-body">
    <header>
        <?php require "../header/index.php"?>
    </header>
    <?php require "../aside/index.php"?>
    <main class ="clicker_page-main">
        <article>
            <h1>Casino Clicker</h1>
            <section class="clicker_page-play_area">
                <div class="clicker_page-play_area-item-1">
                    <p><strong>Peníze:</strong> <?= $user['money'] ?> Kč</p>
                    <p><strong>XP:</strong> <?= $user['xp'] ?></p>
                    <p><strong>Level:</strong> <?= $user['lvl'] ?></p>
                </div>

                <div class="clicker_page-play_area-item-2">
                    <p> Klikni pro žetony!</p>
                    <form method="POST" action="clicker_logic.php">
                        <button type="submit" name="click">Klikni pro výdělek</button>
                    </form>
                    <p>Získáš <strong><?= $estimatedMoney ?> Kč</strong> a <strong><?= $estimatedXp ?> XP</strong> za každý klik.</p>
                </div>

                <div class="clicker_page-play_area-item-3">
                    <button>Base Click</button>
                    <button>Crit Chance</button>
                    <button>Crit Amount</button>
                    <button>Auto click Rate</button>
                    <button>Auto click Amount</button>
                    <button>Bar Cashout Amount</button>
                </div>
            </section>
        </article>
    </main>
    <footer>
        <?php require "../footer/index.php"?>
    </footer>
</body>
</html>
