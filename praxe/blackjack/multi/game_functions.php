<?php
session_start();
require_once 'db/database.php';
$db = new DB();

header('Content-Type: application/json');

// Tahni kartu, ale jen ty s hodnotou do 10 (logika z druhého skriptu)
function vytahniKartu($db) {
    $vysledek = $db->get("SELECT * FROM blackjack WHERE used = 0 AND value <= 10 ORDER BY RAND() LIMIT 1");
    if (empty($vysledek)) return null;
    $karta = $vysledek[0];
    $db->run("UPDATE blackjack SET used = 1 WHERE id = ?", [$karta['id']]);
    return $karta;
}

// Nahrazení esa kartou s hodnotou 1, pokud je skóre > 21
function nahradEsoKartouJedna(&$karty, $db) {
    foreach ($karty as $index => $karta) {
        if ($karta['value'] == 11) { // Eso
            $novaKarta = $db->get("SELECT * FROM blackjack WHERE suit = ? AND original = 1 AND used = 0 LIMIT 1", [$karta['suit']]);
            if (!empty($novaKarta)) {
                $novaKarta = $novaKarta[0];
                $db->run("UPDATE blackjack SET used = 1 WHERE id = ?", [$novaKarta['id']]);
                $db->run("UPDATE blackjack SET used = 0 WHERE id = ?", [$karta['id']]);
                $karty[$index] = $novaKarta;
                break;
            }
        }
    }
}

// Spočítej skóre hráče či krupiéra
function spocitejSkore(&$karty, $db) {
    $skore = 0;
    foreach ($karty as $k) {
        $skore += $k['value'];
    }
    while ($skore > 21) {
        $predSkore = $skore;
        nahradEsoKartouJedna($karty, $db);
        $skore = 0;
        foreach ($karty as $k) {
            $skore += $k['value'];
        }
        if ($skore == $predSkore) break; // Už žádná změna
    }
    return $skore;
}

// Krupiér hraje svůj tah automaticky
function zahrajKrupier(&$db, &$krupier) {
    while (true) {
        $skoreKrupier = spocitejSkore($krupier, $db);
        if ($skoreKrupier < 17 && $skoreKrupier != 21) {
            $novaKarta = vytahniKartu($db);
            if ($novaKarta) {
                $krupier[] = $novaKarta;
            } else {
                break; // už není z čeho táhnout
            }
        } else {
            break;
        }
    }
    return $krupier;
}

// Reset balíčku (všechny karty na unused)
function resetBalicek($db) {
    $db->run("UPDATE blackjack SET used = 0");
}

// --- AJAX logika ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'nova') {
        resetBalicek($db);

        // Vytáhni dvě karty hráči - vybíráme tak, aby value < 5 (jak v game.php)
        do { $hrac1 = vytahniKartu($db); } while (!$hrac1 || $hrac1['value'] >= 5);
        do { $hrac2 = vytahniKartu($db); } while (!$hrac2 || $hrac2['value'] >= 5);
        $_SESSION['hrac'] = [$hrac1, $hrac2];

        // Vytáhni dvě karty krupiérovi
        do { $krupier1 = vytahniKartu($db); } while (!$krupier1 || $krupier1['value'] >= 5);
        do { $krupier2 = vytahniKartu($db); } while (!$krupier2 || $krupier2['value'] >= 5);
        $_SESSION['krupier'] = [$krupier1, $krupier2];

        $_SESSION['zprava'] = '';
        $_SESSION['konec'] = false;

    } elseif ($action === 'hit' && !($_SESSION['konec'] ?? false)) {
        $_SESSION['hrac'][] = vytahniKartu($db);
        $skoreHrac = spocitejSkore($_SESSION['hrac'], $db);

        if ($skoreHrac == 21) {
            $_SESSION['zprava'] = 'Máte 21! Přecházíte na stand.';
            $_SESSION['konec'] = true;

            $_SESSION['krupier'] = zahrajKrupier($db, $_SESSION['krupier']);

            $skoreKrupier = spocitejSkore($_SESSION['krupier'], $db);
            if ($skoreKrupier > 21) {
                $_SESSION['zprava'] = 'Krupiér přetáhl. Vyhrál/a jste!';
            } else {
                if ($skoreHrac > $skoreKrupier) {
                    $_SESSION['zprava'] = 'Vyhrál/a jste!';
                } elseif ($skoreHrac < $skoreKrupier) {
                    $_SESSION['zprava'] = 'Prohrál/a jste.';
                } else {
                    $_SESSION['zprava'] = 'Remíza.';
                }
            }
        } elseif ($skoreHrac > 21) {
            $_SESSION['konec'] = true;
            $_SESSION['zprava'] = 'Přetáhl/a jste. Prohrál/a jste.';
        }

    } elseif ($action === 'stand' && !($_SESSION['konec'] ?? false)) {
        $skoreHrac = spocitejSkore($_SESSION['hrac'], $db);

        $_SESSION['krupier'] = zahrajKrupier($db, $_SESSION['krupier']);

        $skoreKrupier = spocitejSkore($_SESSION['krupier'], $db);

        if ($skoreKrupier > 21) {
            $_SESSION['zprava'] = 'Krupiér přetáhl. Vyhrál/a jste!';
        } elseif ($skoreHrac > $skoreKrupier) {
            $_SESSION['zprava'] = 'Vyhrál/a jste!';
        } elseif ($skoreHrac < $skoreKrupier) {
            $_SESSION['zprava'] = 'Prohrál/a jste.';
        } else {
            $_SESSION['zprava'] = 'Remíza.';
        }
        $_SESSION['konec'] = true;
    }

    // Po akci pošleme stav hry jako JSON
    $hrac = $_SESSION['hrac'] ?? [];
    $krupier = $_SESSION['krupier'] ?? [];
    $zprava = $_SESSION['zprava'] ?? '';
    $konec = $_SESSION['konec'] ?? false;

    $skoreHrac = spocitejSkore($hrac, $db);
    $skoreKrupier = spocitejSkore($krupier, $db);

    echo json_encode([
        'hrac' => $hrac,
        'krupier' => $krupier,
        'skoreHrac' => $skoreHrac,
        'skoreKrupier' => $skoreKrupier,
        'zprava' => $zprava,
        'konec' => $konec,
    ]);
    exit;
}

// Podpora pro ajaxTah('') - vrátit stav hry bez změny
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['action'])) {
    $hrac = $_SESSION['hrac'] ?? [];
    $krupier = $_SESSION['krupier'] ?? [];
    $zprava = $_SESSION['zprava'] ?? '';
    $konec = $_SESSION['konec'] ?? false;

    $skoreHrac = spocitejSkore($hrac, $db);
    $skoreKrupier = spocitejSkore($krupier, $db);

    echo json_encode([
        'hrac' => $hrac,
        'krupier' => $krupier,
        'skoreHrac' => $skoreHrac,
        'skoreKrupier' => $skoreKrupier,
        'zprava' => $zprava,
        'konec' => $konec,
    ]);
    exit;
}

?>