<?php
session_start();
require_once "../database.php";
$db = new DB();

// Zkontroluj, zda je aktivní hra
if (!isset($_SESSION['game_id'])) {
    die("Nejdříve spusť novou hru přes index.php.");
}

$game_id = $_SESSION['game_id'];
$user_id = $_SESSION['user_id'];

$data = $db->getRow("SELECT goal, multiplier, deposit FROM Dice_game WHERE user_id = ?", [$user_id]);

$goal = $data['goal'] ?? 0;
$multiplier = $data['multiplier'] ?? 1;
$deposit = $data['deposit'] ?? 0;

// Načti kostky pro danou hru
if ($game_id) {
    $dices = $db->getAll("SELECT * FROM Dices WHERE user_id = ? AND game_id = ?", [$user_id, $game_id]);
}

// Inicializace session
if (!isset($_SESSION['dice'])) {
    $_SESSION['dice'] = array_column($dices, 'value');
    $_SESSION['locked'] = array_map(fn($v) => (bool)$v, array_column($dices, 'locked'));
    $_SESSION['has_rolled'] = false;
    $_SESSION['game_end'] = false;
    $_SESSION['hazard'] = false;
}
if (!isset($_SESSION['total_score'])) {
    $_SESSION['total_score'] = 0;
}
if (!isset($_SESSION['locked_permanent'])) {
    $_SESSION['locked_permanent'] = array_fill(0, 6, false);
}
function load_permanent_locks_from_db() {
    global $db, $dices; // předpokládám, že máš $db a $dices globálně

    $_SESSION['locked_permanent'] = [];

    foreach ($dices as $i => $dice) {
        // načti permanent_lock z DB
        $result = $db->run("SELECT permanent_lock FROM Dices WHERE id = ?", [$dice['id']]);
        $permanent = $result->fetchColumn();
        $_SESSION['locked_permanent'][$i] = ($permanent == 1);
    }
}
function is_scoring_roll(array $rolled_vals): bool {
    // 1 a 5 jsou vždy skórovací
    foreach ($rolled_vals as $val) {
        if ($val === 1 || $val === 5) {
            return true;
        }
    }

    // Kontrola trojice nebo lepší
    $counts = array_count_values($rolled_vals);
    foreach ($counts as $num => $count) {
        if ($count >= 3) {
            return true;
        }
    }

    // Kontrola postupky (1-6)
    // Pokud je ve hozených hodnotách 6 různých čísel po sobě od 1 do 6, je to postupka
    $unique = array_unique($rolled_vals);
    sort($unique);
    if ($unique === [1, 2, 3, 4, 5, 6]) {
        return true;
    }

    // Pokud nic z toho neplatí, není to skórovací hod
    return false;
}


// Roll
if (isset($_GET['action']) && $_GET['action'] === 'roll') {
    if ($_SESSION['game_end']) {
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }

    unset($_SESSION['current_locked_score']);

    $dice = $_SESSION['dice'];
    $locked = $_SESSION['locked'];
    $locked_before = $_SESSION['locked_before_roll'] ?? array_fill(0, 6, false);
    $permanent_locked = $_SESSION['locked_permanent'] ?? array_fill(0, 6, false);

    // Najdi nově zamčené kostky oproti předchozímu hodu
    $newly_locked_indices = [];
    foreach ($locked as $i => $is_locked) {
        if ($is_locked && !$locked_before[$i]) {
            $newly_locked_indices[] = $i;
        }
    }

    // Získáme hodnoty těchto nově zamčených kostek
    $newly_locked_values = array_map(fn($i) => $dice[$i], $newly_locked_indices);
    

    // Vypočítáme skóre pouze z nově zamčených hodnot
    $score_result = calculate_score($newly_locked_values);
    $score = $score_result['score'] ?? 0;
    $_SESSION['total_score'] += $score;

    // Permanentně zamkneme právě zamčené kostky
    foreach ($newly_locked_indices as $i) {
        $_SESSION['locked_permanent'][$i] = true;

        // Aktualizace i v DB
        $diceId = $dices[$i]['id'];
        $db->run("UPDATE Dices SET permanent_lock = 1 WHERE id = ?", [$diceId]);
    }

    // Pokud jsou všechny kostky zamčené, spustíme nové kolo
    if (!in_array(false, $locked, true)) {
        $_SESSION['locked'] = array_fill(0, 6, false);
        $_SESSION['locked_permanent'] = array_fill(0, 6, false);
        $_SESSION['locked_before_roll'] = array_fill(0, 6, false);
        $_SESSION['has_rolled'] = false;
        $_SESSION['risk_next_roll'] = false;

        // DB update – odemkneme všechny
        foreach ($dices as $die) {
            $db->run("UPDATE Dices SET locked = 0, permanent_lock = 0, score_combo = NULL WHERE id = ?", [$die['id']]);
        }

    } else {
        // Kolo pokračuje – zapamatuj aktuální stav zamknutí
        $_SESSION['locked_before_roll'] = $locked;
        $locked_count = count(array_filter($locked));
        $_SESSION['risk_next_roll'] = ($locked_count >= 0);
    }

    $_SESSION['has_rolled'] = true;

    // Házíme pouze s kostkami, které nejsou permanentně zamčené
    $rolled_vals = [];
    foreach ($_SESSION['dice'] as $i => $val) {
        if (!($_SESSION['locked_permanent'][$i] ?? false)) {
            $newVal = rand(1, 6);
            $_SESSION['dice'][$i] = $newVal;
            $rolled_vals[] = $newVal;

            $diceId = $dices[$i]['id'];
            $db->run("UPDATE Dices SET value = ?, locked = 0 WHERE id = ?", [$newVal, $diceId]);
        }
    }

    // Pokud je aktivní risk – zkontroluj, jestli padl 1 nebo 5 nebo jiná skórovací kombinace
    if ($_SESSION['risk_next_roll']) {
        $has_scoring = false;

        // Zjisti hodnoty, které můžeš skórovat (1, 5, nebo trojice)
        $val_counts = array_count_values($rolled_vals);

        foreach ($val_counts as $val => $count) {
            if (in_array($val, [1, 5]) || $count >= 3) {
                $has_scoring = true;
                break;
            }
        }

        if (!$has_scoring) {
            $_SESSION['total_score'] = 0;
            $_SESSION['hazard'] = true;
            $_SESSION['game_end'] = true; // ⬅️ Přidáno: konec hry
            $_SESSION['warning'] = false;  // Volitelné: schová warning při konci
        }
        $_SESSION['risk_next_roll'] = false;
    }

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}




// Toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['index'])) {
    $i = (int)$_GET['index'];

    // Pokud je kostka permanentně zamčená, nelze ji odemknout
    if (empty($_SESSION['locked_permanent'][$i])) {
    
        $was_locked = $_SESSION['locked'][$i];
        $val = $_SESSION['dice'][$i];

        if (!$was_locked) {
        // Výpočet skórovacích indexů
        $score_result = calculate_score($_SESSION['dice']);
        $allowed_indexes = $score_result['used'] ?? [];

        if (!in_array($i, $allowed_indexes, true)) {
            $_SESSION['locked'][$i] = false;
        } else {
            // Počítáme zamčené kostky dané hodnoty (permanentní i dočasné)
            $currently_locked_vals = [];
            foreach ($_SESSION['locked'] as $idx => $locked_state) {
                if ($locked_state) {
                    $currently_locked_vals[] = $_SESSION['dice'][$idx];
                }
            }
            $count_locked_val = array_count_values($currently_locked_vals)[$val] ?? 0;

            // Celkový počet kostek s danou hodnotou
            $count_val = array_count_values($_SESSION['dice'])[$val] ?? 0;

            if ($count_locked_val >= $count_val) {
                $_SESSION['locked'][$i] = false;
            } else {
                $_SESSION['locked'][$i] = true;
            }
        }

    } else {
        $_SESSION['locked'][$i] = false;
    }

        // Další validace zamknutí – povolíme jen skórovací hodnoty
        if ($_SESSION['locked'][$i]) {
            $count = array_count_values($_SESSION['dice'])[$val] ?? 0;
            if (!in_array($val, [1, 5]) && $count < 3) {
                $_SESSION['locked'][$i] = false;
            }
        }

        // Aktualizuj DB
        $diceId = $dices[$i]['id'];
        $db->run("UPDATE Dices SET locked = ?, score_combo = ? WHERE id = ?", [
            $_SESSION['locked'][$i] ? 1 : 0,
            $_SESSION['locked'][$i] ? $val : null,
            $diceId
        ]);
    }

    // Aktualizuj score aktuálně zamčených kostek
    $locked_before = $_SESSION['locked_before_roll'] ?? array_fill(0, 6, false);
    $newly_locked_values = [];
    foreach ($_SESSION['dice'] as $j => $v) {
        if ($_SESSION['locked'][$j] && !$locked_before[$j]) {
            $newly_locked_values[] = $v;
        }
    }

    $score_result = calculate_score($newly_locked_values);
    $_SESSION['current_locked_score'] = $score_result['score'];

    // ⚠️ Aktivuj warning, pokud jsou zamčeny 4 nebo více kostek
    $locked_count = count(array_filter($_SESSION['locked']));
    $_SESSION['warning'] = ($locked_count >= 4);

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}



// End - přičti skóre pouze z nově zamčených kostek do celkového a ukonči hru
if (isset($_GET['action']) && $_GET['action'] === 'end') {
    $locked_vals = [];
    $newly_locked_indexes = [];

    $locked_before = $_SESSION['locked_before_roll'] ?? array_fill(0, 6, false);
    $current_locked = $_SESSION['locked'];

    foreach ($_SESSION['dice'] as $i => $val) {
        // Najdi jen nově zamčené kostky (které nebyly zamčené před tímto kolem)
        if ($current_locked[$i] && !$locked_before[$i]) {
            $locked_vals[] = $val;
            $newly_locked_indexes[] = $i;
        }

        // Reset zamčení v databázi
        $diceId = $dices[$i]['id'];
        $db->run("UPDATE Dices SET locked = 0, score_combo = NULL WHERE id = ?", [$diceId]);
    }

    // Přičti skóre z nově zamčených kostek
    $score_result = calculate_score($locked_vals);
    $_SESSION['total_score'] += $score_result['score'];

    // Reset zamčení v session
    $_SESSION['locked'] = array_fill(0, 6, false);
    $_SESSION['locked_before_roll'] = array_fill(0, 6, false);

    // ❌ Vypni warning při konci hry
    $_SESSION['warning'] = false;

    // Nastav, že hra je ukončená a nelze dál házet
    $_SESSION['game_end'] = true;
    $_SESSION['risk_next_roll'] = false;

    // Přesměrování zpět na hru
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Score
function calculate_score($dice) {
    $counts = array_count_values($dice);
    $score = 0;
    $used_indexes = [];

    // Straight 1-6
    if (count($counts) === 6 && min($dice) === 1 && max($dice) === 6) {
        // najdi indexy všech 6 různých hodnot
        $needed = [1, 2, 3, 4, 5, 6];
        $found = [];

        foreach ($dice as $i => $v) {
            if (in_array($v, $needed) && !in_array($v, $found)) {
                $used_indexes[] = $i;
                $found[] = $v;
            }
        }

        return ['score' => 2000, 'used' => $used_indexes];
    }

    // Three or more of a kind
    foreach ($counts as $num => $count) {
        if ($count >= 3) {
            $mult = match($count) {
                3 => 100,
                4 => 200,
                5 => 500,
                6 => 1000,
                default => 0,
            };
            $base = ($num == 1) ? 10 : $num;
            $score += $mult * $base;

            // Mark 'count' matching dice
            $matched = 0;
            foreach ($dice as $i => $v) {
                if ($v == $num && $matched < $count) {
                    $used_indexes[] = $i;
                    $matched++;
                }
            }
        }
    }

    // Individual 1s and 5s (not used already)
    foreach ($dice as $i => $v) {
        if (in_array($i, $used_indexes)) continue;
        if ($v == 1) {
            $score += 100;
            $used_indexes[] = $i;
        } elseif ($v == 5) {
            $score += 50;
            $used_indexes[] = $i;
        }
    }

    return ['score' => $score, 'used' => $used_indexes];
}

// Should roll be disabled?
$can_roll = true;

if ($_SESSION['has_rolled']) {
    $can_roll = false;

    $dice = $_SESSION['dice'];
    $locked = $_SESSION['locked'];
    $locked_before = $_SESSION['locked_before_roll'] ?? array_fill(0, 6, false);
    

    $permanent_locked = $_SESSION['locked_permanent'] ?? array_fill(0, 6, false);

    // Vytvoř pole "dice" pouze z hodnot, které nejsou permanentně zamčené
    $unlocked_dice = [];
    $unlocked_indices = [];
    foreach ($dice as $i => $val) {
        if (empty($permanent_locked[$i])) {
            $unlocked_dice[$i] = $val;
            $unlocked_indices[] = $i;
        }
    }

    $result = calculate_score($unlocked_dice);
    $scoring_indexes = $result['used'] ?? [];



    if (count($scoring_indexes) === 0) {
        $can_roll = false;
    } else {
        // Najdi NOVĚ zamčené skórovací kostky
        $newly_locked_scoring = [];
        foreach ($scoring_indexes as $i) {
            if ($locked[$i] && !$locked_before[$i]) {
                $newly_locked_scoring[] = $i;
            }
        }

        // 👇 POSTUPKA – musí být kompletní a všech 6 kostek nově zamčené
        $is_straight = (count(array_unique($dice)) === 6 && min($dice) === 1 && max($dice) === 6);
        if ($is_straight) {
            $all_straight_newly_locked = true;
            foreach ($dice as $i => $v) {
                if (!($locked[$i] && !$locked_before[$i])) {
                    $all_straight_newly_locked = false;
                    break;
                }
            }
            $can_roll = $all_straight_newly_locked;
        } else {
            // Jinak 1 nebo 5 (musí být nově zamčené)
            $new_1_or_5 = false;
            foreach ($newly_locked_scoring as $i) {
                if ($dice[$i] === 1 || $dice[$i] === 5) {
                    $new_1_or_5 = true;
                    break;
                }
            }

            // Nebo validní trojice (musí být všechny 3 nově zamknuté)
            $valid_group_locked = false;
            $value_counts = array_count_values($dice);
            foreach ($value_counts as $val => $count) {
                if ($count >= 3) {
                    $indexes = array_keys(array_filter($dice, fn($v) => $v === $val));
                    $all_locked = true;
                    $all_new = true;
                    foreach ($indexes as $i) {
                        if (!$locked[$i]) $all_locked = false;
                        if ($locked_before[$i]) $all_new = false;
                    }
                    if ($all_locked && $all_new) {
                        $valid_group_locked = true;
                        break;
                    }
                }
            }

            $can_roll = $new_1_or_5 || $valid_group_locked;
        }
    }

    // ✅ BONUS: Když jsou zamknuté všechny kostky → můžeš hodit znovu a začíná nové kolo
    if (array_filter($locked) === $locked) {
        $can_roll = true;
    }
}

// Add score
if (isset($_SESSION['game_end']) && $_SESSION['total_score'] >= $goal) {
    $final_score = $_SESSION['total_score'] * $multiplier;
    $db->run(
        "UPDATE users SET money = money + ? WHERE id = ?",
        [$final_score, $user_id]
    );
}

// New game
if (isset($_GET['action']) && $_GET['action'] === 'new game') {
    unset($_SESSION['current_locked_score']);
    unset($_SESSION['total_score']);
    $_SESSION['locked_permanent'] = array_fill(0, 6, false);
    
    // Reset herních hodnot kostek a jejich stavů
    $_SESSION['dice'] = array_fill(0, 6, null); // nebo [] podle potřeby
    $_SESSION['locked'] = array_fill(0, 6, false);
    $_SESSION['locked_before_roll'] = array_fill(0, 6, false);
    
    $_SESSION['has_rolled'] = false;
    $_SESSION['game_end'] = false;
    $_SESSION['risk_next_roll'] = false;
    
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Reset
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['current_locked_score']);
    unset($_SESSION['total_score']);
    $_SESSION['locked_permanent'] = array_fill(0, 6, false);
    $db->run("DELETE FROM Dices WHERE game_id = ?", [$game_id]);
    $db->run("DELETE FROM Dice_game WHERE id = ?", [$game_id]);
    session_destroy();
    header("Location: ../index.php");
    exit;
}

?>

<!--HTML-->
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>DICE GAME</title>
    <style>
        button.dice {
            width: 60px;
            height: 60px;
            font-size: 24px;
            margin: 5px;
            border-radius: 8px;
            cursor: pointer;
        }
        .locked { background-color: gray; color: white; }
        .unlocked { background-color: lightgreen; color: black; }
        #controls { margin-top: 20px; }
    </style>
</head>
<body>

<h2>Klikni na kostku pro zamčení / odemčení</h2>

<div>
<?php foreach ($_SESSION['dice'] as $i => $val): 
    $locked = $_SESSION['locked'][$i];
    $class = $locked ? 'locked' : 'unlocked';
    $label = $val ?? '-';
?>
    <form method="get" style="display:inline;">
        <input type="hidden" name="action" value="toggle">
        <input type="hidden" name="index" value="<?= $i ?>">
        <button type="submit" class="dice <?= $class ?>"><?= $label ?></button>
    </form>
<?php endforeach; ?>
</div>

<div id="controls">
    <form method="get" style="display:inline;">
        <button type="submit" name="action" value="roll" <?= (!$can_roll || ($_SESSION['game_over'] ?? false)) ? 'disabled' : '' ?>>ROLL</button>
    </form>

    <form method="get" style="display:inline;">
        <button type="submit" name="action" value="end"<?= ((isset($_SESSION['game_end']) && $_SESSION['game_end']) || (isset($_SESSION['game_over']) && $_SESSION['game_over']) ) ? 'disabled' : '' ?>>END</button>
    </form>

    <form method="get" style="display:inline;">
        <button type="submit" name="action" value="new game"<?= ( (isset($_SESSION['game_over']) && $_SESSION['game_over']) || (isset($_SESSION['game_end']) && $_SESSION['game_end']) ) ? '' : 'disabled' ?>>NEW GAME</button>
    </form>
    
    <form method="get" style="display:inline;">
        <button type="submit" name="action" value="reset">RESET</button>
    </form>
</div>

<?php if (isset($_SESSION['current_locked_score'])): ?>
    <h3>🔒 Skóre zamčených kostek: <?= $_SESSION['current_locked_score'] ?></h3>
<?php endif; ?>

<pre>
Skórovací indexy: <?= json_encode($scoring_indexes ?? []) ?>

Zámky: <?= json_encode($_SESSION['locked'] ?? []) ?>

Roll povolen: <?= $can_roll ? 'ANO' : 'NE' ?>

Score: <?= $_SESSION['total_score'] ?? 0 ?>

Goal: <?= $goal ?>

Multiplier: <?= $multiplier ?>

Deposit: <?= $deposit ?>
</pre>

<?php if (!empty($_SESSION['warning'])): ?>
    <div style="color: red; font-weight: bold; margin-bottom: 10px;">
        Are you sure? You have a higher chance of loosing your progress!
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['hazard'])): ?>
    <div style="color: red; font-weight: bold; margin-bottom: 10px;">
        Fuck off you lost!
    </div>
<?php endif; ?>

</body>
</html>

