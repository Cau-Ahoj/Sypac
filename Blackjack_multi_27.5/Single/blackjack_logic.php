<?php
require_once 'db/database.php';
$db = new DB();

session_start();  
$hrac = $_SESSION['hrac'] ?? 'Hráč'; // přihlášený hráč - pole
$hrac_st = is_array($hrac) ? ($hrac['hrac_st'] ?? 'Hráč') : $hrac; // přihlášený hráč - string

/* ---------- Pomocné funkce ---------- */
function vytahniKartu(DB $db): ?array {
    $r = $db->get("SELECT * FROM blackjack WHERE used = 0 ORDER BY RAND() LIMIT 1");
    if (!$r) return null;
    $k = $r[0];
    $db->run("UPDATE blackjack SET used = 1 WHERE id = ?", [$k['id']]);
    return $k;
}
function spocitejSkore(array $karty): int {
    $s = 0; $esa = 0;
    foreach ($karty as $c) {
        $s += $c['value'];
        if ($c['value'] == 11) $esa++;
    }
    while ($s > 21 && $esa--) $s -= 10;
    return $s;
}

/* ---------- Hlavní zpracování ---------- */
function processGame() {
    global $db, $hrac, $hrac_st;

    /* ---- Nová hra ---- */
    if (isset($_POST['nova'])) {
        $db->run("UPDATE blackjack SET used = 0");
        $_SESSION['game_id'] = bin2hex(random_bytes(8));
        $_SESSION['hrac']    = [vytahniKartu($db)];
        $_SESSION['krupier'] = [vytahniKartu($db)];
        $_SESSION['player_stood'] = false;
        $_SESSION['konec']   = false;
        $_SESSION['zprava']  = '';
    }

    /* ---- Hit ---- */
    if (isset($_POST['hit']) && !($_SESSION['konec'] ?? true) && !($_SESSION['player_stood'] ?? false)) {
        $_SESSION['hrac'][] = vytahniKartu($db);

        if (spocitejSkore($_SESSION['hrac']) > 21) {
            $_SESSION['zprava'] = "Prohrál hráč $hrac_st, prohrává 100 kreditů";
            $db->run("UPDATE users SET money = money - 100 WHERE username = ?", [$hrac_st]);
            $_SESSION['konec']  = true;
        }
    }

    /* ---- Stand ---- */
    if (isset($_POST['stand']) && !($_SESSION['konec'] ?? true) && !($_SESSION['player_stood'] ?? false)) {
        $_SESSION['player_stood'] = true;

        /* Krupiér táhne do 17 */
        while (spocitejSkore($_SESSION['krupier']) < 17) {
            $_SESSION['krupier'][] = vytahniKartu($db);
        }

        $sH = spocitejSkore($_SESSION['hrac']);
        $sK = spocitejSkore($_SESSION['krupier']);

        if ($sK > 21 || $sH > $sK) {
            $_SESSION['zprava'] = "Vyhrál hráč $hrac_st, vyhrává 100 kreditů";
            $db->run("UPDATE users SET money = money + 100 WHERE username = ?", [$hrac_st]);
        } elseif ($sH < $sK) {
            $_SESSION['zprava'] = "Prohrál hráč $hrac_st, prohrává 100 kreditů";
            $db->run("UPDATE users SET money = money - 100 WHERE username = ?", [$hrac_st]);
        } else {
            $_SESSION['zprava'] = 'Remíza.';
            /* kredity se nemění */
        }
        $_SESSION['konec'] = true;
    }
}
