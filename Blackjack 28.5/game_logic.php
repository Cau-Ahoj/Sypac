<?php
session_start();
require_once __DIR__ . '/db/database.php';
$db = new DB();

$user = $_SESSION['username'] ?? null;
$gid  = $_SESSION['game_id'] ?? null;

if (!$user || !$gid) {
    http_response_code(403);
    echo json_encode(['error' => 'Neoprávněný přístup']);
    exit;
}

// Inicializace – první karta pro každého hráče
$hands = $db->getAll("SELECT user_name, hand FROM players WHERE game_id = ?", [$gid]);
$anyCards = false;
foreach ($hands as $h) {
    if (!empty(json_decode($h['hand'], true))) {
        $anyCards = true;
        break;
    }
}

if (!$anyCards) {
    foreach ($hands as $h) {
        // vyčistíme ruce, finished=0
        $db->run("UPDATE players SET hand = ?, finished = 0 WHERE game_id = ? AND user_name = ?", [json_encode([]), $gid, $h['user_name']]);
    }
    // nastavíme první tah
    $db->run("UPDATE games SET turn = ? WHERE id = ?", [$hands[0]['user_name'], $gid]);
}

// POST – HIT / STAND / NEWGAME
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $turn = $db->getOne("SELECT turn FROM games WHERE id = ?", [$gid])['turn'] ?? null;

    if ($turn !== $user) {
        echo json_encode(['error' => 'Není tvůj tah']);
        exit;
    }

    $row = $db->getOne("SELECT hand FROM players WHERE game_id = ? AND user_name = ?", [$gid, $user]);
    $hand = json_decode($row['hand'], true);
    $deck = createDeck();
    shuffle($deck);

    if ($action === 'hit') {
        $hand[] = array_pop($deck);
        $db->run("UPDATE players SET hand = ? WHERE game_id = ? AND user_name = ?", [json_encode($hand), $gid, $user]);
        if (score($hand) > 21) {
            $db->run("UPDATE players SET finished = 1 WHERE game_id = ? AND user_name = ?", [$gid, $user]);
        }
    } elseif ($action === 'stand') {
        $db->run("UPDATE players SET finished = 1 WHERE game_id = ? AND user_name = ?", [$gid, $user]);
    }

    // další hráč
    $players = $db->getAll("SELECT user_name, finished FROM players WHERE game_id = ?", [$gid]);
    $remaining = array_filter($players, fn($p) => !$p['finished']);

    if (empty($remaining)) {
        // Všichni hráči dokončili hru – hra je u konce
        $db->run("UPDATE games SET turn = NULL, finish = 1 WHERE id = ?", [$gid]);
    } else {
        $names = array_column($players, 'user_name');
        $i = array_search($user, $names);
        do {
            $i = ($i + 1) % count($names);
        } while ($players[$i]['finished']);
        $db->run("UPDATE games SET turn = ? WHERE id = ?", [$names[$i], $gid]);
    }

    echo json_encode(['reload' => true]);
    exit;
}

// GET – AJAX: stav hry
if (isset($_GET['ajax'])) {
    $players = $db->getAll("SELECT user_name, hand, finished FROM players WHERE game_id = ?", [$gid]);
    $turn = $db->getOne("SELECT turn FROM games WHERE id = ?", [$gid])['turn'] ?? null;

    $state = [
        'players' => [],
        'hands'   => [],
        'stood'   => [],
        'turn'    => $turn,
        'done'    => true,
        'results' => []
    ];

    $scores = [];
    foreach ($players as $p) {
        $cards = json_decode($p['hand'], true);
        $score = score($cards);
        $state['hands'][$p['user_name']] = $cards;
        $state['players'][] = $p['user_name'];
        $state['stood'][$p['user_name']] = (int)$p['finished'];
        if (!$p['finished']) $state['done'] = false;
        $scores[$p['user_name']] = $score;
    }

    // vyhodnocení výsledků
    if ($state['done']) {
        $validScores = array_filter($scores, fn($s) => $s <= 21);
        $max = $validScores ? max($validScores) : 0;

        foreach ($scores as $name => $s) {
            if ($s > 21) {
                $state['results'][$name] = 'prohrál (byl/a přes 21)';
            } elseif ($s === $max && array_count_values($validScores)[$s] > 1) {
                $state['results'][$name] = "remíza ($s)";
            } elseif ($s === $max) {
                $state['results'][$name] = "vyhrál ($s)";
            } else {
                $state['results'][$name] = "prohrál ($s)";
            }
        }
    }

    echo json_encode($state);
    exit;
}

function createDeck() {
    $suits = ['♠', '♥', '♦', '♣'];
    $deck = [];
    foreach ($suits as $suit) {
        foreach (range(2, 10) as $v) {
            $deck[] = ['value' => $v, 'suit' => $suit];
        }
        foreach (['J', 'Q', 'K'] as $f) {
            $deck[] = ['value' => 10, 'suit' => $suit];
        }
        $deck[] = ['value' => 11, 'suit' => $suit];
    }
    return $deck;
}

function score(array $cards): int {
    $sum = 0; $aces = 0;
    foreach ($cards as $c) {
        $sum += $c['value'];
        if ($c['value'] == 11) $aces++;
    }
    while ($sum > 21 && $aces--) $sum -= 10;
    return $sum;
}