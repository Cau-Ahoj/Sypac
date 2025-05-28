<?php
session_start();
if (!isset($_SESSION['hand'])) {
    $_SESSION['hand'] = [];
    $_SESSION['opponent'] = [];
    $_SESSION['deck'] = createDeck();
    shuffle($_SESSION['deck']);
    $_SESSION['hand'][] = drawCard();
    $_SESSION['opponent'][] = drawCard();
    $_SESSION['turn'] = 'player';
    $_SESSION['done'] = false;
    $_SESSION['result'] = '';
}

function createDeck() {
    $suits = ['♠', '♥', '♦', '♣'];
    $values = [
        ['name' => 'A', 'value' => 11],
        ['name' => '2', 'value' => 2],
        ['name' => '3', 'value' => 3],
        ['name' => '4', 'value' => 4],
        ['name' => '5', 'value' => 5],
        ['name' => '6', 'value' => 6],
        ['name' => '7', 'value' => 7],
        ['name' => '8', 'value' => 8],
        ['name' => '9', 'value' => 9],
        ['name' => '10', 'value' => 10],
        ['name' => 'J', 'value' => 10],
        ['name' => 'Q', 'value' => 10],
        ['name' => 'K', 'value' => 10],
    ];

    $deck = [];
    foreach ($suits as $suit) {
        foreach ($values as $v) {
            $deck[] = ['value' => $v['value'], 'suit' => $suit, 'name' => $v['name']];
        }
    }
    return $deck;
}

function drawCard() {
    return array_pop($_SESSION['deck']);
}

function score($hand) {
    $sum = 0;
    $aces = 0;
    foreach ($hand as $card) {
        $sum += $card['value'];
        if ($card['name'] === 'A') $aces++;
    }
    while ($sum > 21 && $aces--) $sum -= 10;
    return $sum;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <title>Blackjack – Singleplayer</title>
  <style>
    .cards { list-style: none; padding: 0; }
    .cards li { display: inline-block; margin: 2px 4px; padding: 3px 6px; border: 1px solid #555; border-radius: 4px; }
    button { margin: 4px; }
  </style>
</head>
<body>
<h1>Blackjack – Singleplayer</h1>

<h2>Hráč</h2>
<ul class="cards">
<?php foreach ($_SESSION['hand'] as $c): ?>
  <li><?= $c['name'] ?> <?= $c['suit'] ?></li>
<?php endforeach; ?>
</ul>
<p>Skóre: <?= score($_SESSION['hand']) ?></p>

<h2>Soupeř</h2>
<ul class="cards">
<?php foreach ($_SESSION['opponent'] as $c): ?>
  <li><?= $c['name'] ?> <?= $c['suit'] ?></li>
<?php endforeach; ?>
</ul>
<p>Skóre: <?= score($_SESSION['opponent']) ?></p>

<?php if (!$_SESSION['done']): ?>
    <?php if ($_SESSION['turn'] === 'player'): ?>
        <form method="post" action="logic.php">
            <button name="action" value="hit">Hit</button>
            <button name="action" value="stand">Stand</button>
        </form>
    <?php else: ?>
        <form method="post" action="logic.php">
            <input type="hidden" name="action" value="opponent">
            <button>Pokračuj tah soupeře</button>
        </form>
    <?php endif; ?>
<?php else: ?>
    <p><strong>Výsledek: <?= $_SESSION['result'] ?></strong></p>
    <form method="post" action="reset.php">
        <button>Nová hra</button>
    </form>
<?php endif; ?>
</body>
</html>