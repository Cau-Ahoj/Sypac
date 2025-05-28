<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['game_id'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$gameId = $_SESSION['game_id'];
$gamesFile = __DIR__ . '/games.json';

// Načteme hry z JSON
$games = file_exists($gamesFile) ? json_decode(file_get_contents($gamesFile), true) : [];

if (!isset($games[$gameId])) {
    echo "Hra nenalezena.";
    exit;
}

$game = $games[$gameId];
$game['moves'] = $game['moves'] ?? [];
$game['finished'] = $game['finished'] ?? false;

if (!in_array($username, $game['players'])) {
    echo "Nejste účastníkem této hry.";
    exit;
}

$players = $game['players'];
$turnIndex = $game['turn'] ?? 0;
$turnPlayer = $players[$turnIndex];
$onTurn = ($username === $turnPlayer);

// --- Funkce ---

function vytahniKartu() {
    // Karty: hodnota 1-10, barva - srdce, piky, káry, kříže (pro jednoduchost)
    $suits = ['Srdce', 'Piky', 'Káry', 'Kříže'];
    $value = rand(1,10);
    $suit = $suits[array_rand($suits)];
    return ['value' => $value, 'suit' => $suit];
}

function spocitejSkore($karty) {
    $skore = 0;
    $pocetEs = 0;
    foreach ($karty as $k) {
        $val = $k['value'];
        if ($val == 1) {
            $pocetEs++;
            $skore += 11;
        } else {
            $skore += $val;
        }
    }
    while ($skore > 21 && $pocetEs > 0) {
        $skore -= 10; // Eso z 11 na 1
        $pocetEs--;
    }
    return $skore;
}

// --- Obnovení stavu hry podle tahů ---

// Data hráčů: index 0 i 1
$hands = [[], []];      // pole karet pro hráče
$standed = [false, false];  // jestli hráč zvolil stand
$finished = false;
$message = '';

// Procházet tahy a stav aktualizovat
foreach ($game['moves'] as $move) {
    $player = $move['player'];
    $action = $move['move'];
    $pIndex = array_search($player, $players);

    if ($action === 'nova') {
        // Nová hra - reset rukou a stavů
        $hands = [[], []];
        $standed = [false, false];
        $finished = false;
        $message = '';

        // Rozdání 2 karet oběma hráčům
        for ($i=0; $i<2; $i++) {
            foreach ([0,1] as $pi) {
                $hands[$pi][] = vytahniKartu();
            }
        }
    } elseif ($finished) {
        // Po skončení hry se tahy ignorují až do nova
        continue;
    } elseif ($action === 'hit') {
        if (!$standed[$pIndex]) {
            $hands[$pIndex][] = vytahniKartu();
            $skore = spocitejSkore($hands[$pIndex]);
            if ($skore > 21) {
                $finished = true;
                $message = "$player přetáhl! Prohrává.";
            } elseif ($skore == 21) {
                $message = "$player má 21!";
            }
        }
    } elseif ($action === 'stand') {
        $standed[$pIndex] = true;

        // Pokud oba stand, vyhodnotit výsledky
        if ($standed[0] && $standed[1]) {
            $skore0 = spocitejSkore($hands[0]);
            $skore1 = spocitejSkore($hands[1]);

            if ($skore0 > 21 && $skore1 > 21) {
                $message = "Oba hráči přetáhli. Remíza.";
            } elseif ($skore0 > 21) {
                $message = "{$players[1]} vyhrává!";
            } elseif ($skore1 > 21) {
                $message = "{$players[0]} vyhrává!";
            } else {
                if ($skore0 > $skore1) $message = "{$players[0]} vyhrává!";
                elseif ($skore1 > $skore0) $message = "{$players[1]} vyhrává!";
                else $message = "Remíza.";
            }
            $finished = true;
        }
    }
}

// --- Zpracování nového tahu ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $onTurn && !$finished) {
    $inputMove = trim($_POST['move'] ?? '');
    if ($inputMove !== '') {
        if (str_starts_with($inputMove, 'chat:')) {
            $game['moves'][] = [
                'player' => $username,
                'move' => $inputMove,
                'time' => time()
            ];
        } else {
            $action = strtolower($inputMove);
            if (in_array($action, ['nova', 'hit', 'stand'])) {
                // Pokud hra ještě neskončila nebo nova povolena
                if ($action === 'nova') {
                    // Nova resetuje hru a vždy dá tah prvnímu hráči
                    $game['turn'] = 0;
                    $game['finished'] = false;
                    $finished = false;
                } elseif ($action === 'hit' || $action === 'stand') {
                    // Tahy mění tah na druhého hráče jen pokud hra neskončila
                    if (!$finished) {
                        $game['turn'] = ($turnIndex + 1) % 2;
                    }
                }
                $game['moves'][] = [
                    'player' => $username,
                    'move' => $action,
                    'time' => time()
                ];
            }
        }
        $game['finished'] = $finished;
        $games[$gameId] = $game;
        file_put_contents($gamesFile, json_encode($games, JSON_PRETTY_PRINT));

        header("Location: game.php");
        exit;
    }
}

// --- Výpis stránky ---

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <title>Blackjack - Tahová Multiplayer Verze</title>
</head>
<body>
    <h1>Blackjack (Tahová Multiplayer Verze)</h1>

    <p><strong>Hráči:</strong> <?=htmlspecialchars($players[0])?> vs <?=htmlspecialchars($players[1])?></p>
    <p><strong>Na tahu:</strong> <?=htmlspecialchars($turnPlayer)?></p>
    <p><strong>Status:</strong> <?=htmlspecialchars($message)?></p>

    <?php foreach ([0,1] as $i): ?>
        <h2>Karty hráče <?=htmlspecialchars($players[$i])?>:</h2>
        <ul>
            <?php foreach ($hands[$i] as $k): ?>
                <li><?=htmlspecialchars($k['value'].' '.$k['suit'])?></li>
            <?php endforeach; ?>
        </ul>
        <b>Skóre: <?=spocitejSkore($hands[$i])?></b>
    <?php endforeach; ?>

    <h2>Historie tahů a chat:</h2>
    <ul>
    <?php foreach ($game['moves'] as $m): ?>
        <li><strong><?=htmlspecialchars($m['player'])?>:</strong>
            <?php 
                if (str_starts_with($m['move'], 'chat:')) {
                    echo htmlspecialchars(substr($m['move'], 5));
                } else {
                    echo '<em>'.htmlspecialchars($m['move']).'</em>';
                }
            ?>
        </li>
    <?php endforeach; ?>
    </ul>

    <?php if (!$finished): ?>
        <?php if ($onTurn): ?>
            <form method="post">
                <input type="text" name="move" placeholder="Zadejte tah: nova, hit, stand nebo chat: vaše zpráva" required>
                <button type="submit">Odeslat tah / chat</button>
            </form>
        <?php else: ?>
            <p>Čekám na tah soupeře...</p>
            <script>setTimeout(() => location.reload(), 3000);</script>
        <?php endif; ?>
    <?php else: ?>
        <p>Hra skončila. Pro novou hru zadejte "nova".</p>
        <?php if ($onTurn): ?>
            <form method="post">
                <input type="text" name="move" placeholder="nova nebo chat: vaše zpráva" required>
                <button type="submit">Odeslat</button>
            </form>
        <?php else: ?>
            <p>Čekám na nový tah soupeře...</p>
            <script>setTimeout(() => location.reload(), 3000);</script>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>