<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hra se skořápkami</title>
</head>
<body>

  <h1>BONUS</h1>

  <div class="shell-buttons">
    <button class="shell" id="shell1" disabled>Skořápka 1</button>
    <button class="shell" id="shell2" disabled>Skořápka 2</button>
    <button class="shell" id="shell3" disabled>Skořápka 3</button>
  </div>

  <p id="userBalance">Načítám zůstatek...</p>
  <p id="reward"></p>

  <div class="game-controls">
    <div class="bet-input-group">
      <label for="betAmountInput">Zadejte sázku:</label>
      <input type="number" id="betAmountInput" value="10" min="1" step="1">
    </div>
    <button id="startBtn">Začít hru</button>
  </div>

  <!-- Nové ovládací prvky pro double or nothing -->
  <div id="afterWinControls" style="display: none;">
    <button id="doubleBtn">Double or Nothing</button>
    <button id="takeWinBtn" onclick="document.getElementById('afterWinControls').style.display='none'; document.getElementById('startBtn').style.display='inline-block';">Vyzvednout výhru</button>
  </div>


  <script src="script.js"></script>
</body>
</html>
