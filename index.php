<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SYPAC CASINO - Golden Eggs</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="casino-container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-content">
        <h1>SYPAC CASINO</h1>
        <div class="menu-item">PLAY</div>
        <div class="menu-item">BALANCE</div>
        <div class="menu-item">HISTORY</div>
        <div class="menu-item">LEADERBOARD</div>
        <div class="menu-item">SETTINGS</div>
      </div>
      
      <div class="active-time">
        <div>AKTIVNÍ ČAS</div>
        <span>celkem: 25 hodin</span>
      </div>
    </div>
    
    <!-- Main Game Area -->
    <div class="main-panel">
      <!-- Info Button -->
      <div class="info-btn" onclick="showRules()">
        <i class="fas fa-info-circle"></i>
      </div>

      <div class="bonus-label">GOLDEN EGGS</div>
      
      <div class="egg-container">
        <div class="egg-option" onclick="selectEgg('bronze')">
          <img src="/sypac/skorapky/vajicka/bronz/vajicko1.png" alt="Bronze Egg">
          <div class="egg-info">
            <h3>BRONZE EGG</h3>
            <p>50 Kč</p>
            <div class="odds">Win Chance: 35%</div>
          </div>
        </div>
        
        <div class="egg-option" onclick="selectEgg('silver')">
          <img src="/sypac/skorapky/vajicka/dia/vajicko1.png" alt="Diamond Egg">
          <div class="egg-info">
            <h3>DIAMOND EGG</h3>
            <p>100 Kč</p>
            <div class="odds">Win Chance: 60%</div>
          </div>
        </div>
        
        <div class="egg-option" onclick="selectEgg('gold')">
          <img src="/sypac/skorapky/vajicka/master/vajicko1.png" alt="Master Egg">
          <div class="egg-info">
            <h3>MASTER EGG</h3>
            <p>200 Kč</p>
            <div class="odds">Win Chance: 85%</div>
          </div>
        </div>
      </div>
      
      <div class="game-area">
        <div class="selected-egg-display" id="selectedEggDisplay">
          <p>Select an egg to begin</p>
        </div>
        
        <div class="controls">
          <button class="buy-btn" id="buyBtn" disabled>BUY EGG (50 Kč)</button>
          <button class="crack-btn" id="crackBtn" disabled onclick="crackEgg()">CRACK EGG</button>
        </div>
      </div>
      
      <div class="reward-label">REWARD</div>
      <div class="reward-box" id="rewardBox"></div>
    </div>
  </div>

  <!-- Rules Modal -->
  <div id="rulesModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>GAME RULES</h2>
      <div class="rules-content">
        <p>1. Choose an egg type (Bronze, Silver, or Gold)</p>
        <p>2. Click "BUY EGG" to purchase your selected egg</p>
        <p>3. Click "CRACK EGG" to open it and reveal your prize</p>
        <br>
        <h3>WIN CHANCES:</h3>
        <p>- Bronze Egg: 35% chance to win (50 Kč)</p>
        <p>- Silver Egg: 60% chance to win (100 Kč)</p>
        <p>- Gold Egg: 85% chance to win (200 Kč)</p>
        <br>
        <p>Prizes range from 2x to 10x your bet amount!</p>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>