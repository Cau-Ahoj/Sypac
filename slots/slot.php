<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ./");
    exit;
}

require_once '../database.php';
session_start();
$db = new DB();

$baseCost = 10;
$maxCost = 200000;

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../login/");
    exit;
}

$user = $db->getOne("SELECT * FROM users WHERE id = ?", [$user_id]);
if (!$user) {
    die("Uživatel neexistuje.");
}

$currentMoney = $user['money'];
$spinCost = $_SESSION['spinCost'] ?? 50;
$result = '';
$slots = [];
$winAmount = 0;
$xpGain = 0;

// Symboly pouze pro logiku – NEJSOU posílány do frontend
$symbols = ['cherry', 'lemon', 'grape', 'clover', 'diamond', 'seven', 'joker'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput = (int)($_POST['spinCost'] ?? 0);

    if ($userInput < $baseCost) {
        $result = "Minimální částka na zatočení je $baseCost Kč.";
    } elseif ($userInput > $maxCost) {
        $result = "Maximální částka na zatočení je $maxCost Kč.";
    } elseif ($currentMoney < $userInput) {
        $result = "❌ Nemáš dostatek kreditu! ❌";
    } else {
        $spinCost = $userInput;
        $_SESSION['spinCost'] = $spinCost;
        $currentMoney -= $spinCost;

        // Náhodné indexy 0–6
        $slot1 = rand(0, 6);
        $slot2 = rand(0, 6);
        $slot3 = rand(0, 6);
        $slots = [$slot1, $slot2, $slot3];

        // Převod na "názvy" symbolů pro logiku
        $symbol1 = $symbols[$slot1];
        $symbol2 = $symbols[$slot2];
        $symbol3 = $symbols[$slot3];
        $symbolArray = [$symbol1, $symbol2, $symbol3];

        $counts = array_count_values($symbolArray);
        $jokerCount = $counts['joker'] ?? 0;

        if ($jokerCount === 3) {
            $result = "🃏🃏 Tři jesteri! MEGA výhra! 🃏";
            $winAmount = round($spinCost * 10);
        } elseif ($jokerCount > 0) {
            unset($counts['joker']);
            arsort($counts);
            $mainSymbol = key($counts);
            $symbolCount = current($counts) + $jokerCount;

            if ($symbolCount >= 3) {
                switch ($mainSymbol) {
                    case 'seven':
                        $result = "JACKPOOT 💰 s jokerem!";
                        $winAmount = round($spinCost * 6);
                        break;
                    case 'diamond':
                        $result = "💎 Diamanty s jokerem 💎!";
                        $winAmount = round($spinCost * 5);
                        break;
                    default:
                        $result = "Výhra díky jokerovi!";
                        $winAmount = round($spinCost * 4);
                }
            } else {
                $result = "Dva různé symboly a joker, žádná výhra.";
            }
        } elseif ($symbol1 === $symbol2 && $symbol2 === $symbol3) {
            switch ($symbol1) {
                case 'seven':
                    $result = "JACKPOOT 💰! Tři sedmičky!";
                    $winAmount = round($spinCost * 10);
                    break;
                case 'diamond':
                    $result = "💎💎 Diamanty 💎💎!";
                    $winAmount = round($spinCost * 9);
                    break;
                case 'clover':
                    $result = "🍀🍀 Tři čtyřlístky 🍀🍀!";
                    $winAmount = round($spinCost * 8);
                    break;
                case 'grape':
                    $result = "🍇🍇 Tři hrozny! 🍇🍇";
                    $winAmount = round($spinCost * 6);
                    break;
                case 'lemon':
                    $result = "🍋🍋 Tři citrony! 🍋🍋";
                    $winAmount = round($spinCost * 4);
                    break;
                case 'cherry':
                    $result = "🍒🍒 Tři třešně! 🍒🍒";
                    $winAmount = round($spinCost * 3);
                    break;
                default:
                    $result = "Výhra!";
                    $winAmount = round($spinCost * 2);
            }
        } else {
            $result = "❌ Nic si nevyhrál.";
            $winAmount = 0;
        }

        // ❌ Žádný +500 bonus
        $xpGain = floor(pow($spinCost / 10, 0.9));
        $currentMoney += $winAmount;

        $db->run("UPDATE users SET money = ?, xp = xp + ? WHERE id = ?", [$currentMoney, $xpGain, $user_id]);

        $db->run(
            "INSERT INTO slot_results (user_id, slot1, slot2, slot3, result_text, win_amount, spin_cost, credit_after)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$user_id, $slot1, $slot2, $slot3, $result, $winAmount, $spinCost, $currentMoney]
        );

        $userUpdated = $db->getOne("SELECT xp FROM users WHERE id = ?", [$user_id]);
        $currentXp = $userUpdated['xp'] ?? 0;
        $currentLevel = floor($currentXp / 1000);

        $_SESSION['currentXp'] = $currentXp;
        $_SESSION['currentLevel'] = $currentLevel;
    }

    unset($_SESSION['winAmount']);

    $_SESSION['result'] = $result;
    $_SESSION['slots'] = $slots; // např. [3, 6, 1]
    $_SESSION['currentMoney'] = $currentMoney;
    $_SESSION['winAmount'] = $winAmount;

    header("Location: ./");
    exit;
}
