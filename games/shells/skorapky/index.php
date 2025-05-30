<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Shell Game</title>

  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="../globalstyle.css" />

  <style>
    .shell-buttons, .shell, .shell * {
      user-select: none;
      -webkit-user-drag: none;
    }

    .disabled-shells {
      pointer-events: none;
    }

    .devtools-detected {
      background-color: #1e0041;
      color: red;
      font-family: 'Bungee', sans-serif;
      text-align: center;
      padding: 100px;
      font-size: 2rem;
    }

    #chooseMessage {
      display: none;
      color: #ffda6e;
      font-family: 'Bungee', sans-serif;
      font-size: 1.5em;
      margin-top: -10px;
      text-shadow: 0 0 6px #E8AC41, 0 0 6px #da3300;
    }
  </style>

  <script>
    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
  </script>
</head>
<body>

  <!-- Header -->
  <header>
    <?php require "../header/index.php"; ?>
  </header>

  <!-- Sidebar -->
  <?php require "../aside/index.php"; ?>

  <!-- Game Area -->
  <main class="clicker_page-main">
    <article>
      <h1>SHELLS</h1>

      <section class="clicker_page-play_area">
        <!-- Balance -->
        <div class="clicker_page-play_area-item-1">
          <p id="userBalance">Loading...</p>
        </div>

        <!-- Shells -->
        <div class="shell-buttons" id="shellButtons">
          <div class="shell" id="shell1">
            <img src="Kelimek.png" alt="Shell 1" />
            <img src="Kulicka.png" class="ball ball1" alt="Ball 1" />
          </div>
          <div class="shell" id="shell2">
            <img src="Kelimek.png" alt="Shell 2" />
            <img src="Kulicka.png" class="ball ball2" alt="Ball 2" />
          </div>
          <div class="shell" id="shell3">
            <img src="Kelimek.png" alt="Shell 3" />
            <img src="Kulicka.png" class="ball ball3" alt="Ball 3" />
          </div>
        </div>

        <!-- Choose One Message -->
        <p id="chooseMessage">Choose one</p>

        <!-- Controls -->
        <div class="game-controls">
          <div class="bet-input-group">
            <label for="betAmountInput">Bet:</label>
            <input type="number" id="betAmountInput" value="10" min="1" step="10" required />
          </div>
          <button id="startBtn">START</button>
          <p id="reward"></p>
        </div>

        <!-- Double / Collect -->
        <div id="afterWinControls" style="display: none;">
          <button id="doubleBtn">Double or Nothing</button>
          <button id="collectBtn">Collect</button>
        </div>
      </section>
    </article>
  </main>

  <!-- Game logic -->
  <script src="script.js"></script>

  <!-- Block right click and DevTools shortcuts -->
  <script>
    document.addEventListener('contextmenu', event => event.preventDefault());

    document.addEventListener('keydown', function (event) {
      if (
        event.key === "F12" ||
        (event.ctrlKey && event.shiftKey && (event.key === "I" || event.key === "J")) ||
        (event.ctrlKey && event.key === "U")
      ) {
        event.preventDefault();
      }
    });
  </script>

  <!-- Redirect to login and detect DevTools -->
  <script>
    if (typeof isLoggedIn !== "undefined" && !isLoggedIn) {
      window.location.href = "https://sypac.workspace-sosceskybrod.cz/login/";
    } else {
      (function detectDevTools() {
        const threshold = 160;
        const check = () => {
          const widthThreshold = window.outerWidth - window.innerWidth > threshold;
          const heightThreshold = window.outerHeight - window.innerHeight > threshold;

          if (widthThreshold || heightThreshold) {
            document.body.innerHTML = '<div class="devtools-detected">Developer tools detected. Access to this game has been blocked.</div>';
          }
        };
        setInterval(check, 1000);
      })();
    }
  </script>

  <!-- Footer -->
  <?php require "../footer/index.php"; ?>
</body>
</html>
