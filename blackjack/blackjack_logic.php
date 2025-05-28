<?php
require_once 'db/database.php';
$db = new DB();

session_start();

function vytahniKartu($db) {
    $vysledek = $db->get("SELECT * FROM blackjack WHERE used = 0 ORDER BY RAND() LIMIT 1");
    if (empty($vysledek)) return null;
    $karta = $vysledek[0];
    $db->run("UPDATE blackjack SET used = 1 WHERE id = ?", [$karta['id']]);
    return $karta;
}

function spocitejSkore($karty) {
    $skore = 0;
    $esa = 0;
    foreach ($karty as $k) {
        $skore += $k['value'];
        if ($k['value'] == 11) $esa++;
    }
    while ($skore > 21 && $esa--) $skore -= 10;
    return $skore;
}

function processGame() {
    global $db;

    if (isset($_POST['nova'])) {
        $db->run("UPDATE blackjack SET used = 0");
        $_SESSION['game_id'] = bin2hex(random_bytes(8));
        $_SESSION['hrac'] = [vytahniKartu($db)];
        $_SESSION['krupier'] = [vytahniKartu($db)];
        $_SESSION['zprava'] = '';
        $_SESSION['konec'] = false;
    }

    if (isset($_POST['hit']) && !$_SESSION['konec']) {
        $_SESSION['hrac'][] = vytahniKartu($db);
        if (spocitejSkore($_SESSION['hrac']) > 21) {
            $_SESSION['zprava'] = 'Přetáhl jsi. Prohrál jsi.';
            $_SESSION['konec'] = true;
        }
    }

    if (isset($_POST['stand']) && !$_SESSION['konec']) {
        while (spocitejSkore($_SESSION['krupier']) < 17) {
            $_SESSION['krupier'][] = vytahniKartu($db);
        }

        $skoreHrac = spocitejSkore($_SESSION['hrac']);
        $skoreKrupier = spocitejSkore($_SESSION['krupier']);

        if ($skoreKrupier > 21 || $skoreHrac > $skoreKrupier) {
            $_SESSION['zprava'] = 'Vyhrál jsi!';
        } elseif ($skoreHrac < $skoreKrupier) {
            $_SESSION['zprava'] = 'Prohrál jsi.';
        } else {
            $_SESSION['zprava'] = 'Remíza.';
        }

        $_SESSION['konec'] = true;
    }
}
?>