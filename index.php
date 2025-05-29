<?php
session_start();
require_once "database.php";
$db = new DB();

$user_id = $_SESSION['user_id'] ?? 1;
$_SESSION['user_id'] = $user_id;
$game_id = $_SESSION['game_id'] ?? null;

if (!$user_id) {
    header("Location: ../login/");
    exit;
}

$user = $db->getOne("SELECT money, xp FROM users WHERE id = ?", [$user_id]);
if (!$user) {
    die("Uživatel nenalezen.");
}

$minDeposit = 100; // minimum 1000 (protože po 1000 jsi chtěl)
$maxDeposit = $user['money'];
$minGoal = 100;
$maxGoal = 10000;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allIn = isset($_POST['all_in']);
    $goal = (int)($_POST['goal'] ?? 0);

    // Pokud je all in, deposit je vsechny penize
    if ($allIn) {
        $deposit = $user['money'];
    } else {
        $deposit = (int)($_POST['deposit'] ?? 0);
    }

    // Validace depositu
    if ($deposit < $minDeposit) {
        die("Minimální vklad je $minDeposit.");
    }
    if ($deposit > $maxDeposit) {
        die("Nemáš dostatek peněz na tento vklad.");
    }

    // Validace goal
    if ($goal < $minGoal) {
        die("Minimální cíl je $minGoal.");
    }
    if ($goal > $maxGoal) {
        die("Maximální cíl je $maxGoal.");
    }

    if($goal < 500){
        $goal_multi = 1.5;
    } else {
        if ($goal < 1500 && $goal > 500){
            $goal_multi = 2.0;
        } else{
            if ($goal < 3000 && $goal > 1500){
                $goal_multi = 2.5;
            } else {
                if ($goal < 6000 && $goal > 3000){
                    $goal_multi = 3.0;
                } else {
                    $goal_multi = 4.0;
                }
            }
        }
    }

    if($deposit < 500){
        $deposit_multi = 1.5;
    } else {
        if ($deposit < 1500 && $deposit > 500){
            $deposit_multi = 2.0;
        } else{
            if ($deposit < 3000 && $deposit > 1500){
                $deposit_multi = 2.5;
            } else {
                if ($deposit < 6000 && $deposit > 3000){
                    $deposit_multi = 3.0;
                } else {
                    if ($deposit = $maxDeposit){
                        $deposit_multi = 10.0;
                    } else {
                        $deposit_multi = 4.0;
                    }
                }
            }
        }
    }

    // Získání multiplikátoru
    $item = $db->getOne("
        SELECT i.bonus_value 
        FROM purchases p
        JOIN items i ON p.item_id = i.id
        WHERE p.user_id = ? AND i.bonus_type = 'dice'
        ORDER BY i.bonus_value DESC
        LIMIT 1
    ", [$user_id]);

    $multiplier = $item['bonus_value'] ?? 1.0;

    $game_multi = $deposit_multi + $goal_multi + $multiplier;

    // Odečti peníze a založ hru
    $db->run("UPDATE users SET money = money - ? WHERE id = ?", [$deposit, $user_id]);
    $db->run("INSERT INTO Dice_game (user_id, goal, deposit, multiplier) VALUES (?, ?, ?, ?)", [
        $user_id,
        $goal,
        $deposit,
        $game_multi
    ]);

    $game_id = $db->lastInsertId();
    $_SESSION['game_id'] = $game_id;

    // Inicializuj kostky
    $dices = $db->getAll("SELECT * FROM Dices WHERE user_id = ? AND game_id = ?", [$user_id, $game_id]);

    if (count($dices) === 0) {
        for ($i = 0; $i < 6; $i++) {
            $db->run("INSERT INTO Dices (user_id, game_id, value, locked, permanent_lock) VALUES (?, ?, NULL, 0, 0)", [$user_id, $game_id]);
        }
    }

    $_SESSION['dice'] = array_fill(0, 6, null);
    $_SESSION['locked'] = array_fill(0, 6, false);
    $_SESSION['has_rolled'] = false;
    unset($_SESSION['game_over']);

    header("Location: dice/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <title>Vyber vklad a cíl</title>
</head>
<body>
    <h1>Vyber si vklad a cíl hry</h1>
    <form method="post">
        <label for="deposit">Vklad (min <?= $minDeposit ?>, max <?= $maxDeposit ?>):</label><br>
        <input type="number" id="deposit" name="deposit" min="<?= $minDeposit ?>" max="<?= $maxDeposit ?>" step="100" <?= isset($_POST['all_in']) ? 'disabled' : '' ?> required><br>

        <input type="checkbox" id="all_in" name="all_in" onchange="toggleDeposit(this)" <?= isset($_POST['all_in']) ? 'checked' : '' ?>>
        <label for="all_in">All in (vsadit vše)</label><br><br>

        <label for="goal">Cíl (min <?= $minGoal ?>, max <?= $maxGoal ?>):</label><br>
        <input type="number" id="goal" name="goal" min="<?= $minGoal ?>" max="<?= $maxGoal ?>" step="100" required><br><br>

        <button type="submit">Start hry</button>
    </form>

    <script>
    function toggleDeposit(checkbox) {
        const depositInput = document.getElementById('deposit');
        if (checkbox.checked) {
            depositInput.disabled = true;
            depositInput.value = '';
        } else {
            depositInput.disabled = false;
        }
    }
    // Po načtení stránky, pokud je all_in zaškrtnutý, deaktivuj deposit input
    window.onload = function() {
        const allInCheckbox = document.getElementById('all_in');
        toggleDeposit(allInCheckbox);
    };
    </script>
</body>
</html>
