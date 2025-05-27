<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ./");
    exit;
}

require_once 'database.php';
session_start();
$db = new DB();

$baseCost = 10;
$maxCost = 10000;


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
        $multiplier = $spinCost / $baseCost;

        // Symboly rozloženy dle výskytu (víc levných, míň drahých)
        $symbols = array_merge(
            array_fill(0, 10, '🍒'),
            array_fill(0, 8, '🍋'),
            array_fill(0, 6, '🍇'),
            array_fill(0, 4, '🍀'),
            array_fill(0, 3, '💎'),
            array_fill(0, 2, '7️⃣'),
            array_fill(0, 1, '🃏') // Joker = nejvzácnější
        );

        // Výběr náhodných slotů
        $slot1 = $symbols[array_rand($symbols)];
        $slot2 = $symbols[array_rand($symbols)];
        $slot3 = $symbols[array_rand($symbols)];
        $slots = [$slot1, $slot2, $slot3];

        // Výpočet výher
        $counts = array_count_values($slots);
        $jokerCount = $counts['🃏'] ?? 0;

        if ($jokerCount === 3) {
            $result = "🃏🃏 Tři jesteri! Mega výhra! 🃏";
            $winAmount = round($spinCost * 100);
        } elseif ($jokerCount > 0) {
            unset($counts['🃏']);
            arsort($counts);
            $mainSymbol = key($counts);
            $symbolCount = current($counts) + $jokerCount;

            if ($symbolCount >= 3) {
                switch ($mainSymbol) {
                    case '7️⃣':
                        $result = "JACKPOOT 💰 s jokerem!";
                        $winAmount = round($spinCost * 10);
                        break;
                    case '💎':
                        $result = "💎 Diamanty s jokerem 💎!";
                        $winAmount = round($spinCost * 6);
                        break;
                    default:
                        $result = "Výhra díky jokerovi!";
                        $winAmount = round($spinCost * 3);
                }
            } else {
                $result = "Dva různé symboly a joker, žádná výhra.";
            }
        } elseif ($slot1 === $slot2 && $slot2 === $slot3) {
            switch ($slot1) {
                case '7️⃣':
                    $result = "JACKPOOT 💰! Tři sedmičky!";
                    $winAmount = round($spinCost * 20);
                    break;
                case '💎':
                    $result = "💎💎 Diamanty 💎💎!";
                    $winAmount = round($spinCost * 10);
                    break;
                case '🍀':
                    $result = "🍀🍀 Tři čtyřlístky 🍀🍀!";
                    $winAmount = round($spinCost * 6);
                    break;
                case '🍇':
                    $result = "🍇🍇 Tři hrozny! 🍇🍇";
                    $winAmount = round($spinCost * 4);
                    break;
                case '🍋':
                    $result = "🍋🍋 Tři citrony! 🍋🍋";
                    $winAmount = round($spinCost * 3);
                    break;
                case '🍒':
                    $result = "🍒🍒 Tři třešně! 🍒🍒";
                    $winAmount = round($spinCost * 2);
                    break;
                default:
                    $result = "Výhra!";
                    $winAmount = round($spinCost * 2);
            }
        } else {
            $result = "❌ Nic si nevyhrál.";
            $winAmount = 0;
        }

        // Přičtení výhry
        $currentMoney += $winAmount;

        // Uložení do DB
        $db->run("UPDATE users SET money = ? WHERE id = ?", [$currentMoney, $user_id]);
        $db->run(
            "INSERT INTO slot_results (user_id, slot1, slot2, slot3, result_text, win_amount, spin_cost, credit_after)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$user_id, $slot1, $slot2, $slot3, $result, $winAmount, $spinCost, $currentMoney]
        );
    }

    // POST-Redirect-GET
    $_SESSION['result'] = $result;
    $_SESSION['slots'] = $slots;
    $_SESSION['currentMoney'] = $currentMoney;

    header("Location: ./");
    exit;
}
