<?php
session_start();

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

$action = $_POST['action'] ?? '';

if ($_SESSION['done']) {
    header('Location: index.php');
    exit;
}

if ($action === 'hit') {
    $_SESSION['hand'][] = drawCard();
    if (score($_SESSION['hand']) >= 21) {
        $_SESSION['turn'] = 'opponent';
    }
} elseif ($action === 'stand') {
    $_SESSION['turn'] = 'opponent';
} elseif ($action === 'opponent') {
    $s = score($_SESSION['opponent']);
    if ($s < 17) {
        $_SESSION['opponent'][] = drawCard();
        if (score($_SESSION['opponent']) >= 21) $_SESSION['done'] = true;
    } else {
        $_SESSION['done'] = true;
    }

    if ($_SESSION['done']) {
        $ps = score($_SESSION['hand']);
        $os = score($_SESSION['opponent']);

        if ($ps > 21) {
            $_SESSION['result'] = 'Prohra – přetáhl jsi!';
        } elseif ($os > 21) {
            $_SESSION['result'] = 'Výhra – soupeř přetáhl!';
        } elseif ($ps > $os) {
            $_SESSION['result'] = 'Výhra!';
        } elseif ($ps < $os) {
            $_SESSION['result'] = 'Prohra!';
        } else {
            $_SESSION['result'] = 'Remíza!';
        }
    }
}

header('Location: index.php');
exit;