<?php
session_start();
const GAMEFILE = __DIR__ . '/games.json';

$user = $_SESSION['username'] ?? null;
$gid  = $_SESSION['game_id'] ?? null;

if (!$user || !$gid) {
    http_response_code(403);
    echo json_encode(['error' => 'Neoprávněný přístup']);
    exit;
}

$games = file_exists(GAMEFILE) ? json_decode(file_get_contents(GAMEFILE), true) : [];

if (!isset($games[$gid])) {
    echo json_encode(['error' => 'Hra nenalezena']);
    exit;
}

$game = &$games[$gid];

// Inicializace hry
if (!isset($game['players'], $game['hands'])) {
    $game['players'] = [$user]; // při první inicializaci
    $game['hands']   = [$user => [], 'Dealer' => []];
    $game['stood']   = [$user => false];
    $game['turn']    = $user;
    $game['done']    = false;
    $game['deck']    = createDeck();
    shuffle($game['deck']);

    // Druhá karta se přidá po připojení druhého hráče
    file_put_contents(GAMEFILE, json_encode($games, JSON_PRETTY_PRINT));
}

// Pokud máme jen jednoho hráče, čekáme na druhého
if (count($game['players']) < 2 && !in_array($user, $game['players'])) {
    $game['players'][] = $user;
    $game['hands'][$user] = [];
    $game['stood'][$user] = false;
    if (count($game['players']) === 2) {
        // rozdání karet všem hráčům i dealerovi
        foreach ($game['players'] as $p) {
            $game['hands'][$p][] = array_pop($game['deck']);
            $game['hands'][$p][] = array_pop($game['deck']);
        }
        $game['hands']['Dealer'][] = array_pop($game['deck']);
        $game['hands']['Dealer'][] = array_pop($game['deck']);
        $game['turn'] = $game['players'][0];
    }
    file_put_contents(GAMEFILE, json_encode($games, JSON_PRETTY_PRINT));
}

// POST – provedení akce hráče
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($game['done']) {
        echo json_encode(['reload' => true]);
        exit;
    }

    if ($game['turn'] !== $user) {
        echo json_encode(['error' => 'Není tvůj tah']);
        exit;
    }

    if ($action === 'hit') {
        $game['hands'][$user][] = array_pop($game['deck']);
        if (score($game['hands'][$user]) > 21) {
            $game['stood'][$user] = true;
        }
    } elseif ($action === 'stand') {
        $game['stood'][$user] = true;
    } elseif ($action === 'newgame') {
        // Reset celé hry
        unset($games[$gid]);
        file_put_contents(GAMEFILE, json_encode($games, JSON_PRETTY_PRINT));
        echo json_encode(['reload' => true]);
        exit;
    }

    // Přepnutí na dalšího hráče
    $remaining = array_filter($game['players'], fn($p) => !$game['stood'][$p]);
    if (empty($remaining)) {
        // všichni hráči stojí – hraje dealer
        while (score($game['hands']['Dealer']) < 17) {
            $game['hands']['Dealer'][] = array_pop($game['deck']);
        }
        $game['done'] = true;
    } else {
        $currentIndex = array_search($user, $game['players']);
        $next = ($currentIndex + 1) % count($game['players']);
        $game['turn'] = $game['players'][$next];
        while ($game['stood'][$game['turn']] ?? false) {
            $next = ($next + 1) % count($game['players']);
            $game['turn'] = $game['players'][$next];
        }
    }

    file_put_contents(GAMEFILE, json_encode($games, JSON_PRETTY_PRINT));
}

// GET – vrácení aktuálního stavu hry (ajax=1)
if (isset($_GET['ajax'])) {
    echo json_encode([
        'players' => $game['players'],
        'hands'   => $game['hands'],
        'stood'   => $game['stood'],
        'turn'    => $game['turn'],
        'done'    => $game['done'],
    ]);
    exit;
}

// Pomocná funkce – vytvoření balíčku karet
function createDeck() {
    $suits = ['♠', '♥', '♦', '♣'];
    $deck = [];
    foreach ($suits as $suit) {
        foreach (range(2, 10) as $v) {
            $deck[] = ['value' => $v, 'suit' => $suit];
        }
        foreach (['J', 'Q', 'K'] as $face) {
            $deck[] = ['value' => 10, 'suit' => $suit];
        }
        $deck[] = ['value' => 11, 'suit' => $suit]; // eso
    }
    return $deck;
}

function score(array $cards): int {
    $s = 0; $aces = 0;
    foreach ($cards as $card) {
        $s += $card['value'];
        if ($card['value'] == 11) $aces++;
    }
    while ($s > 21 && $aces > 0) {
        $s -= 10;
        $aces--;
    }
    return $s;
}
?>