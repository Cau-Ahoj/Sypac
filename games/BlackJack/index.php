<?php
require_once '../database.php';
$db = new DB();

session_start();

// Vybere náhodnou nepoužitou kartu z balíčku
function vytahni_kartu($db) {
    $vysledek = $db->get("SELECT * FROM Blackjack WHERE used = 0 ORDER BY RAND() LIMIT 1");
    if (empty($vysledek)) return null;
    $karta = $vysledek[0];
    $db->run("UPDATE Blackjack SET used = 1 WHERE id = ?", [$karta['id']]);
    return $karta;
}

// Spočítá skóre z karet
function spocitej_skore($karty) {
    $skore = 0;
    $esa = 0;
    foreach ($karty as $karta) {
        $skore += $karta['value'];
        if ($karta['value'] == 11) $esa++;
    }
    while ($skore > 21 && $esa--) $skore -= 10;
    return $skore;
}

// Nová hra
if (isset($_POST['nova'])) {
    $db->run("UPDATE Blackjack SET used = 0");
    $_SESSION['hrac'] = [vytahni_kartu($db), vytahni_kartu($db)];
    $_SESSION['krupier'] = [vytahni_kartu($db), vytahni_kartu($db)];
    $_SESSION['zprava'] = '';
    $_SESSION['konec'] = false;
}

// Hit – hráč si vezme další kartu
if (isset($_POST['hit']) && !$_SESSION['konec']) {
    $_SESSION['hrac'][] = vytahni_kartu($db);
    if (spocitej_skore($_SESSION['hrac']) > 21) {
        $_SESSION['zprava'] = 'Přetáhl jsi. Prohrál jsi.';
        $_SESSION['konec'] = true;
    }
}

// Stand – hraje krupiér
if (isset($_POST['stand']) && !$_SESSION['konec']) {
    while (spocitej_skore($_SESSION['krupier']) < 17) {
        $_SESSION['krupier'][] = vytahni_kartu($db);
    }

    $skore_hrac = spocitej_skore($_SESSION['hrac']);
    $skore_krupier = spocitej_skore($_SESSION['krupier']);

    if ($skore_krupier > 21 || $skore_hrac > $skore_krupier) {
        $_SESSION['zprava'] = 'Vyhrál jsi!';
    } elseif ($skore_hrac < $skore_krupier) {
        $_SESSION['zprava'] = 'Prohrál jsi.';
    } else {
        $_SESSION['zprava'] = 'Remíza.';
    }

    $_SESSION['konec'] = true;
}

// Výpis karet
$hrac = $_SESSION['hrac'] ?? [];
$krupier = $_SESSION['krupier'] ?? [];
$zprava = $_SESSION['zprava'] ?? '';
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Blackjack – jednoduchá verze</title>
</head>
<body>
    <h1>Blackjack (čisté PHP)</h1>

    <h2>Hráč</h2>
    <?php foreach ($hrac as $k): ?>
        <?= $k['value'] ?> <?= $k['suit'] ?><br>
    <?php endforeach; ?>
    <b>Skóre: <?= spocitej_skore($hrac) ?></b>

    <h2>Krupiér</h2>
    <?php foreach ($krupier as $k): ?>
        <?= $k['value'] ?> <?= $k['suit'] ?><br>
    <?php endforeach; ?>
    <b>Skóre: <?= spocitej_skore($krupier) ?></b>

    <h3><?= $zprava ?></h3>

    <form method="post">
        <button name="nova">Nová hra</button>
        <button name="hit">Hit</button>
        <button name="stand">Stand</button>
    </form>
</body>
</html>
