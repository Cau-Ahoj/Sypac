<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php 
    require "../header/index.php";
    require "../aside/index.php";


    // Placeholder
    $user = [
        'name' => 'John Doe',
        'money' => 123456,
        'role' => 'Fighter',
        'wins' => 10,
        'losses' => 5,
        'level' => 12,
        'xp' => 480,
        'xp_max' => 1000,
    ];
    ?>

    <div class="main-container">
        <div class="profile-section">
            <div class="top-grid">
                <div class="avatar"></div>
                <div class="info-box">
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                    <p><strong>Money:</strong> <?= number_format($user['money'], 0, '.', ' ') ?>$</p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                </div>
            </div>

            <div class="bottom-grid">
                <div class="stats-box">
                    <p><strong>Wins:</strong> <?= $user['wins'] ?></p>
                    <p><strong>Losses:</strong> <?= $user['losses'] ?></p>
                </div>
                <div class="level-box">
                    <p class="level-text">LVL: <?= $user['level'] ?></p>
                    <div class="xp-bar-container">
                        <div class="xp-bar">
                            <div class="xp-fill" style="width: <?= ($user['xp'] / $user['xp_max']) * 100 ?>%;"></div>
                        </div>
                        <span class="xp-text">
                            <?= $user['xp'] ?>/ <strong><?= $user['xp_max'] ?>xp</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    <div class="character-section">
    <div class="character-box">
        <div class="arrow">&#9664;</div>

        <div class="character-display">
            <div class="character-body"></div>
        </div>

        <div class="arrow">&#9654;</div>
    </div>

    <div class="equipped-item">
        <h3 class="item-title">Název předmětu</h3>
        <p class="item-desc">Popis předmětu, jeho bonusy, efekty atd. Bude se měnit podle equipnutého itemu.</p>
    </div>
</div>







</body>
<?php require "../footer/index.php"; ?>

</html>
