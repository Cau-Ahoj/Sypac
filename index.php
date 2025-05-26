<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../global.css">
    <title>Dices</title>
</head>
<body>

    <aside class="main_page-aside">
    <?php require "../aside/index.php"  ?>
    </aside>

    <header class="main_page-header">
    <?php require "../header/index.php"  ?>
    </header>
    <main class="dice_page-main">
        <section class = "dice_page-title">
            DICE
        </section>
        <section class="dice_page-game">
            <div class="dice_page-game-variables">
                <div class = "dice_page-score"></div>
                <div class = "dice_page-goal"></div>
                <div class = "dice_page-multiplier"></div>
            </div>
            <div class="dice_page-game-dice">
                <div class = "dice_page-dice1"></div>
                <div class = "dice_page-dice2"></div>
                <div class = "dice_page-dice3"></div>
                <div class = "dice_page-dice4"></div>
                <div class = "dice_page-dice5"></div>
                <div class = "dice_page-dice6"></div>
            </div>
            <div class="dice_page-game-buttons">
                <div class = "dice_page_button-roll"></div>
                <div class = "dice_page_button-cash-out"></div>
            </div>
            <div class="dice_page-game-help"></div>
        </section>
    </main>
    
</body>
</html>