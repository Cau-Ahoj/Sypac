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

        <div class="character-container">
    <div class="equipment">
        <div class="item-slot" title="Helma"></div>
        <div class="item-slot" title="Zbroj"></div>
        <div class="item-slot" title="Kalhoty"></div>
        <div class="item-slot" title="Boty"></div>
    </div>

    <div class="inventory">
        <?php
        for ($i = 0; $i < 16; $i++):
            echo "<div class='item-slot' title='Slot $i'></div>";
        endfor;
        ?>
    </div>
</div>



</body>
<?php require "../footer/index.php"; ?>

</html>
