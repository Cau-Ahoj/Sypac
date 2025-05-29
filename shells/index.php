<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hra se skořápkami</title>

  <!-- CSS styly -->
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="../globalstyle.css" />
</head>
<body>

  <!-- Hlavička -->
  <header>
    <?php require "../header/index.php"; ?>
  </header>

  <!-- Postranní panel -->
  <?php require "../aside/index.php"; ?>

  <!-- Hlavní část -->
  <main class="clicker_page-main">
    <article>
      <h1>Shells</h1>

      <section class="clicker_page-play_area">
        <!-- Výpis zůstatku -->
        <div class="clicker_page-play_area-item-1">
          <p id="userBalance">Loading balance...</p>
        </div>

        <!-- Herní skořápky -->
        <div class="shell-buttons">
          <button class="shell" id="shell1" disabled>
            <img src="Kelimek.png" alt="Skořápka 1" />
            <img src="Kulicka.png" alt="Míček" class="ball ball1" />
          </button>
          <button class="shell" id="shell2" disabled>
            <img src="Kelimek.png" alt="Skořápka 2" />
            <img src="Kulicka.png" alt="Míček" class="ball ball2" />
          </button>
          <button class="shell" id="shell3" disabled>
            <img src="Kelimek.png" alt="Skořápka 3" />
            <img src="Kulicka.png" alt="Míček" class="ball ball3" />
          </button>
        </div>

        <!-- Ovládací prvky -->
        <div class="game-controls">
          <div class="bet-input-group">
            <label for="betAmountInput">Enter bet:</label>
            <input type="number" id="betAmountInput" value="10" min="1" step="10" required />
          </div>
          <button id="startBtn">Start</button>
          <p id="reward"></p>
        </div>

        <!-- Double or Nothing -->
        <div id="afterWinControls" style="display: none;">
          <button id="doubleBtn">Double or Nothing</button>
          <button id="collectBtn">Collect</button>
        </div>
      </section>
    </article>
  </main>

  <!-- Skript -->
  <script src="script.js"></script>

  <!-- Paticka -->
  <?php require "../footer/index.php"; ?>
</body>
</html>
