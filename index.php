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
        <div class = "dice_page-popup-window"><p onclick="togglePopup()">X</p></div>
    </div>
    
      
        <?php require "../header/index.php"  ?>
       
    
        <?php require "../aside/index.php"  ?>
        

        
        <main class="dice_page-main">
            <section class = "dice_page-title">
                DICES
            </section>
            <section class="dice_page-game">
                <div id=var1 class = "dice_page-variable">SCORE:</div>
                <div id=var2 class = "dice_page-variable">GOAL:</div>
                <div id=var3 class = "dice_page-variable">MULTIPLIER:</div>
                <div id=var4 class = "dice_page-variable">DEPOSIT:</div>

                <div id=kostka1 class = "dice_page-dice"><img src="/dices/assets/kostka1b.png" alt="kostka1"></div>
                <div id=kostka2 class = "dice_page-dice"><img src="/dices/assets/kostka2b.png" alt="kostka1"></div>
                <div id=kostka3 class = "dice_page-dice"><img src="/dices/assets/kostka3b.png" alt="kostka1"></div>
                <div id=kostka4 class = "dice_page-dice"><img src="/dices/assets/kostka4b.png" alt="kostka1"></div>
                <div id=kostka5 class = "dice_page-dice"><img src="/dices/assets/kostka5b.png" alt="kostka1"></div>
                <div id=kostka6 class = "dice_page-dice"><img src="/dices/assets/kostka6b.png" alt="kostka1"></div>
                <div id=roll class = "dice_page-button"><button>ROLL</button></div>
                <div id=end class = "dice_page-button"><button>END</button></div>
                <div class="dice_page-game-help"><button onclick="togglePopup()"><img src="/dices/assets/kostkyOtaznik.png" alt="help"></button></div>
            </section>

        </main>
    
    <?php require "../footer/index.php"  ?>

    <script src="script.js"></script>

</body>
</html>