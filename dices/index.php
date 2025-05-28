<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../globalstyle.css">
    <link rel="stylesheet" href="style.css">
    <title>Dices</title>
</head>
<body>
    <script src="script.js"></script>
    <div id="popup" class = "dice_page-popup-background" style="display: none;"> 
        <div class = "dice_page-popup-window"></div>
    </div>
    
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
                <img src="/dices/assets/kostkyStul.png" alt="stul">
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
                    <div class = "dice_page_button-cash-out"><button>END</button></div>
                </div>
                <script src="script.js"></script>
                <div class="dice_page-game-help"><button onclick="togglePopup()"><img src="/dices/assets/kostkyOtaznik.png" alt="help"></button></div>
            </section>
        </main>
    
    
</body>
</html>