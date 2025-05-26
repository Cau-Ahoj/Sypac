<?php
require_once 'db/database.php';
$db = new DB();

// Spustíme session pro uchování hry mezi kliknutími
session_start();

// Funkce pro náhodné vytažení jedné nepoužité karty z databáze
function vytahniKartu($db) {
    $vysledek = $db->get("SELECT * FROM blackjack WHERE used = 0 AND value <= 10 ORDER BY RAND()");
    
    if (empty($vysledek)) return null;

    $karta = $vysledek[0];
    $db->run("UPDATE blackjack SET used = 1 WHERE id = ?", [$karta['id']]);
    return $karta;
}

// Pomocná funkce pro výměnu esa (value=11) za kartu s hodnotou 1 stejného suitu
function nahradEsoKartouJedna(&$karty, $db) {
    foreach ($karty as $index => $karta) {
        if ($karta['value'] == 11) {
            // Hledáme v DB kartu se stejným suitem, hodnotou 1 a nepoužitou
            $novaKarta = $db->get("SELECT * FROM blackjack WHERE suit = ? AND original = 1 AND used = 0 LIMIT 1", [$karta['suit']]);
            if (!empty($novaKarta)) {
                $novaKarta = $novaKarta[0];
                
                // Označíme novou kartu jako použitou v DB
                $db->run("UPDATE blackjack SET used = 1 WHERE id = ?", [$novaKarta['id']]);
                
                // Původní eso označíme jako nepoužité (pokud bylo v DB)
                $db->run("UPDATE blackjack SET used = 0 WHERE id = ?", [$karta['id']]);
                
                // Vyměníme eso v poli za novou kartu s hodnotou 1
                $karty[$index] = $novaKarta;
                
                // Výměna provedena, ukončíme
                break;
            }
        }
    }
}

// Upravená funkce výpočtu skóre s výměnou esa za 1
function spocitejSkore(&$karty, $db) {
    $skore = 0;
    foreach ($karty as $k) {
        $skore += $k['value'];
    }
    while ($skore > 21) {
        // Pokud máme eso (11), nahraď ho kartou s hodnotou 1
        $predSkore = $skore;
        nahradEsoKartouJedna($karty, $db);
        
        // Přepočítáme skóre po výměně
        $skore = 0;
        foreach ($karty as $k) {
            $skore += $k['value'];
        }
        
        // Pokud nedošlo ke změně skóre (tedy nemáme další esa k výměně), přerušíme smyčku
        if ($skore == $predSkore) break;
    }
    return $skore;
}

// Nová hra – resetuje balíček a rozdá karty
if (isset($_POST['nova'])) {
    $db->run("UPDATE blackjack SET used = 0");

    // Načti dvě karty pro hráče
    do {
        $hrac1 = vytahniKartu($db);
    } while (!$hrac1 || $hrac1['value'] >= 5);

    do {
        $hrac2 = vytahniKartu($db);
    } while (!$hrac2 || $hrac2['value'] >= 5);

    $_SESSION['hrac'] = [$hrac1, $hrac2];

    // Načti dvě karty pro krupiéra
    do {
        $krupier1 = vytahniKartu($db);
    } while (!$krupier1 || $krupier1['value'] >= 5);

    do {
        $krupier2 = vytahniKartu($db);
    } while (!$krupier2 || $krupier2['value'] >= 5);

    $_SESSION['krupier'] = [$krupier1, $krupier2];

    $_SESSION['zprava'] = '';
    $_SESSION['konec'] = false;
}

// Hráč chce další kartu
if (isset($_POST['hit']) && !($_SESSION['konec'] ?? false)) {
    $_SESSION['hrac'][] = vytahniKartu($db);
    $skore = spocitejSkore($_SESSION['hrac'], $db);
    if ($skore == 21) {
        // Hráč dosáhl 21, automaticky stojí
        $_SESSION['zprava'] = 'Máte 21! Přecházíte na stand.';
    }
    if ($skore > 21) {
        $_SESSION['konec'] = true;
        $_SESSION['zprava'] = 'Přetáhl/a jste. Prohrál/a jste.';
    }
}

// Hráč chce další kartu
if (isset($_POST['hit']) && !($_SESSION['konec'] ?? false)) {
    $_SESSION['hrac'][] = vytahniKartu($db);
    $skoreHrac = spocitejSkore($_SESSION['hrac'], $db);

    if ($skoreHrac == 21) {
        // Automatický stand - hráč dosáhl 21
        $_SESSION['zprava'] = 'Máte 21! Přecházíte na stand.';
        
        // Tah krupiéra
        while (true) {
            $skoreKrupier = spocitejSkore($_SESSION['krupier'], $db);
            
            // Krupiér táhne, pokud má méně než 17 nebo přesně 21 (pokud chceš, aby hrál i na 21, jak píšeš)
            if ($skoreKrupier < 17 || $skoreKrupier == 21) {
                $_SESSION['krupier'][] = vytahniKartu($db);
            } else {
                break;
            }
        }

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
        $_SESSION['konec'] = true;
    } elseif ($skoreHrac > 21) {
        // Hráč přetáhl
        $_SESSION['konec'] = true;
        $_SESSION['zprava'] = 'Přetáhl/a jste. Prohrál/a jste.';
    }
}

// Hráč stojí – hraje krupiér
if (isset($_POST['stand']) && !($_SESSION['konec'] ?? false)) {
    $skoreHrac = spocitejSkore($_SESSION['hrac'], $db);

    // Tah krupiéra - stejná logika jako výše
    while (true) {
        $skoreKrupier = spocitejSkore($_SESSION['krupier'], $db);

        if ($skoreKrupier < 17 || $skoreKrupier == 21) {
            $_SESSION['krupier'][] = vytahniKartu($db);
        } else {
            break;
        }
    }

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

// Výpis aktuálních karet
$hrac = $_SESSION['hrac'] ?? [];
$krupier = $_SESSION['krupier'] ?? [];
$zprava = $_SESSION['zprava'] ?? '';
$konec = $_SESSION['konec'] ?? false;
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Jednoduchý Blackjack</title>
</head>
<body>
    <h1>Blackjack (PHP verze bez JavaScriptu)</h1>

    <h2>Hráč</h2>
    <?php foreach ($hrac as $k): ?>
        <?= htmlspecialchars($k['value']) ?> <?= htmlspecialchars($k['suit']) ?><br>
    <?php endforeach; ?>
    <b>Skóre: <?= spocitejSkore($hrac, $db) ?></b>

    <h2>Krupiér</h2>
    <?php foreach ($krupier as $k): ?>
        <?= htmlspecialchars($k['value']) ?> <?= htmlspecialchars($k['suit']) ?><br>
    <?php endforeach; ?>
    <b>Skóre: <?= spocitejSkore($krupier, $db) ?></b>

    <h3><?= htmlspecialchars($zprava) ?></h3>

    <form method="post">
        <button name="nova">Nová hra</button>
        <button name="hit" <?= $konec ? 'disabled' : '' ?>>Hit (karta navíc)</button>
        <button name="stand" <?= $konec ? 'disabled' : '' ?>>Stand (čekám)</button>
    </form>
</body>
</html>