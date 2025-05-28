<?php
session_start();
require_once "../database.php";
$db = new DB();

// Zkontroluj, zda je aktivní hra
if (!isset($_SESSION['game_id'])) {
    die("Nejdříve spusť novou hru přes index.php.");
}

$game_id = $_SESSION['game_id'];

// Načti kostky pro danou hru
$dices = $db->get("SELECT * FROM Dices WHERE game_id = ? ORDER BY id ASC", [$game_id]);

// Inicializace session
if (!isset($_SESSION['dice'])) {
    $_SESSION['dice'] = array_column($dices, 'value');
    $_SESSION['locked'] = array_map(fn($v) => (bool)$v, array_column($dices, 'locked'));
    $_SESSION['has_rolled'] = false;
    $_SESSION['game_over'] = false;
}
if (!isset($_SESSION['total_score'])) {
    $_SESSION['total_score'] = 0;
}

// Roll
if (isset($_GET['action']) && $_GET['action'] === 'roll') {
    if ($_SESSION['game_over']) {
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }

    $dice = $_SESSION['dice'];
    $locked = $_SESSION['locked'];
    $locked_before = $_SESSION['locked_before_roll'] ?? array_fill(0, 6, false);

    if (!in_array(false, $locked, true)) {
        $locked_vals = array_values(array_filter($dice, fn($v, $i) => $locked[$i], ARRAY_FILTER_USE_BOTH));
        $score_result = calculate_score($locked_vals);
        $score = $score_result['score'] ?? 0;
        $_SESSION['total_score'] += $score;

        $_SESSION['locked'] = array_fill(0, 6, false);
        foreach ($dices as $die) {
            $db->run("UPDATE Dices SET locked = 0, score_combo = NULL WHERE id = ?", [$die['id']]);
        }
        $_SESSION['locked_before_roll'] = $_SESSION['locked'];
        $_SESSION['risk_next_roll'] = false;

        // OPRAVA: Resetuj has_rolled, začíná nové kolo
        $_SESSION['has_rolled'] = false;
    } else {
        $newly_locked = [];
        foreach ($locked as $i => $is_locked) {
            if ($is_locked && !$locked_before[$i]) {
                $newly_locked[] = $i;
            }
        }

        $new_vals = array_map(fn($i) => $dice[$i], $newly_locked);
        $score_result = calculate_score($new_vals);
        $score = $score_result['score'] ?? 0;
        $_SESSION['total_score'] += $score;

        $_SESSION['locked_before_roll'] = $locked;

        $locked_count = count(array_filter($locked));
        $_SESSION['risk_next_roll'] = ($locked_count >= 4);
    }

    $_SESSION['has_rolled'] = true;

    $rolled_vals = [];
    foreach ($_SESSION['dice'] as $i => $val) {
        if (!$_SESSION['locked'][$i]) {
            $newVal = rand(1, 6);
            $_SESSION['dice'][$i] = $newVal;
            $rolled_vals[] = $newVal;

            $diceId = $dices[$i]['id'];
            $db->run("UPDATE Dices SET value = ?, locked = 0 WHERE id = ?", [$newVal, $diceId]);
        }
    }

    if ($_SESSION['risk_next_roll']) {
        if (!in_array(1, $rolled_vals) && !in_array(5, $rolled_vals)) {
            $_SESSION['total_score'] = 0;
        }
        $_SESSION['risk_next_roll'] = false;
    }

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}


// Toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['index'])) {
    $i = (int)$_GET['index'];
    $val = $_SESSION['dice'][$i];
    $was_locked = $_SESSION['locked'][$i];
    $_SESSION['locked'][$i] = !$was_locked;
    $locked = $_SESSION['locked'][$i];
    $diceId = $dices[$i]['id'];

    // Validace – zamykání nesmyslné kostky
    if ($locked) {
        $count = array_count_values($_SESSION['dice'])[$val] ?? 0;
        if (!in_array($val, [1, 5]) && $count < 3) {
            $_SESSION['locked'][$i] = false;
            $_SESSION['toggle_warning'] = "Nelze zamknout hodnotu $val – není skórovací (ani trojice, ani 1/5).";
        }
    }

    $db->run("UPDATE Dices SET locked = ?, score_combo = ? WHERE id = ?", [
        $_SESSION['locked'][$i] ? 1 : 0,
        $_SESSION['locked'][$i] ? $val : null,
        $diceId
    ]);

    $locked_before = $_SESSION['locked_before_roll'] ?? array_fill(0, 6, false);
    $newly_locked_values = [];

    foreach ($_SESSION['dice'] as $j => $v) {
        if ($_SESSION['locked'][$j] && !$locked_before[$j]) {
            $newly_locked_values[] = $v;
        }
    }

    $score_result = calculate_score($newly_locked_values);
    $_SESSION['current_locked_score'] = $score_result['score'];

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}


// Stop - přičti skóre zamčených kostek do celkového a ukonči hru
if (isset($_GET['action']) && $_GET['action'] === 'stop') {
    $locked_vals = [];
    foreach ($_SESSION['dice'] as $i => $val) {
        if ($_SESSION['locked'][$i]) {
            $locked_vals[] = $val;
        }
        $diceId = $dices[$i]['id'];
        // Reset zamčení v DB (když už končí hra)
        $db->run("UPDATE Dices SET locked = 0, score_combo = NULL WHERE id = ?", [$diceId]);
    }

    // Přičti skóre právě zamčených kostek
    $score_result = calculate_score($locked_vals);
    $_SESSION['total_score'] += $score_result['score'];

    // Reset zamčení v session
    $_SESSION['locked'] = array_fill(0, 6, false);

    // Nastav, že hra je ukončená a nelze dál házet
    $_SESSION['game_over'] = true;

    // Reset ostatních příznaků
    $_SESSION['warning_issued'] = false;
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

    $result = calculate_score($dice);
    $scoring_indexes = $result['used'];

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



// Reset
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    unset($_SESSION['current_locked_score']);
    unset($_SESSION['total_score']);
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
    <title>Kostková hra</title>
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
        <button type="submit" name="action" value="roll" <?= (!$can_roll || ($_SESSION['game_over'] ?? false)) ? 'disabled' : '' ?>>🎲 Hod kostkami</button>
    </form>

    <form method="get" style="display:inline;">
        <button type="submit" name="action" value="stop" <?= (isset($_SESSION['game_over']) && $_SESSION['game_over']) ? 'disabled' : '' ?>>🛑 STOP</button>
    </form>

    <form method="get" style="display:inline;">
        <button type="submit" name="action" value="reset">🔁 Reset</button>
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
</pre>

<?php if (!empty($_SESSION['warning_issued'])): ?>
    <div style="color: red; font-weight: bold; margin-bottom: 10px;">
        Varování: Máš zamčeno více než 4 kostky. Pokud hodíš a nepadne 1 nebo 5, ztratíš všechno skóre!
    </div>
<?php endif; ?>

</body>
</html>

