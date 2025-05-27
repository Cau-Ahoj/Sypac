<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dices</title>
</head>
<body>
    <div class = "dice_page-popup-background"> 
        <div class = "dice_page-popup-window">Whoa hey a window</div>
    </div>
    <div class = "layout">
        <header class="main_page-header">
        <?php require "../header/index.php"  ?>
        </header>
        <aside class="main_page-aside">
        <?php require "../aside/index.php"  ?>
        </aside>

    
        <main class="dice_page-main">
            <section class = "dice_page-title">
                DICE
            </section>
            <section class="dice_page-game">
                <div class="dice_page-game-variables">
                    <div class = "dice_page-score">SCORE:</div>
                    <div class = "dice_page-goal">GOAL:</div>
                    <div class = "dice_page-multiplier">MULTIPLIER:</div>
                    <div class = "dice_page-deposit">DEPOSIT:</div>
                </div>
                <div class="dice_page-game-dice">
                    <div class = "dice_page-dice">1</div>
                    <div class = "dice_page-dice">2</div>
                    <div class = "dice_page-dice">3</div>
                    <div class = "dice_page-dice">4</div>
                    <div class = "dice_page-dice">5</div>
                    <div class = "dice_page-dice">6</div>
                </div>
                <div class="dice_page-game-buttons">
                    <div class = "dice_page_button-roll"><button>ROLL</button></div>
                    <div class = "dice_page_button-cash-out"><button>ROLL</button></div>
                </div>
                <div class="dice_page-game-help"><p>?</p></div>
            </section>
        </main>
    </div>
    
</body>
</html>