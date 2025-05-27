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
$currentXp = $user['xp'];
$spinCost = $_SESSION['spinCost'] ?? 50;
$result = '';
$slots = [];
$winAmount = 0;

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

    // Rovné šance – každý symbol jednou
    $symbols = ['🍒', '🍋', '🍇', '🍀', '💎', '7️⃣', '🃏'];

    // Výběr náhodných symbolů
    $slot1 = $symbols[array_rand($symbols)];
    $slot2 = $symbols[array_rand($symbols)];
    $slot3 = $symbols[array_rand($symbols)];
    $slots = [$slot1, $slot2, $slot3];

    // Výpočet výher
    $counts = array_count_values($slots);
    $jokerCount = $counts['🃏'] ?? 0;

    if ($jokerCount === 3) {
        $result = "🃏🃏 Tři jesteri! MEGA výhra! 🃏";
        $winAmount = round($spinCost * 500);
    } elseif ($jokerCount > 0) {
        unset($counts['🃏']);
        arsort($counts);
        $mainSymbol = key($counts);
        $symbolCount = current($counts) + $jokerCount;

        if ($symbolCount >= 3) {
            $result = "Výhra díky jokerovi!";
            $winAmount = round($spinCost * 25);
        } else {
            $result = "Dva různé symboly a joker, žádná výhra.";
        }
    } elseif ($slot1 === $slot2 && $slot2 === $slot3) {
        $result = "Tři stejné symboly!";
        $winAmount = round($spinCost * 20);
    } else {
        $result = "❌ Nic si nevyhrál.";
    }

    // 🎁 Bonus za výhru
    if ($winAmount > 0) {
        $winAmount += 500;
    }

    // XP výpočet – pomalý, exponenciální růst
    $xpGain = round(pow($spinCost / $baseCost, 1.2));  // 1.2 exponent je vhodný
    $currentXp += $xpGain;

    // Uložení
    $currentMoney += $winAmount;
    $db->run("UPDATE users SET money = ?, xp = ? WHERE id = ?", [
        $currentMoney, $currentXp, $user_id
    ]);

    $db->run(
        "INSERT INTO slot_results (user_id, slot1, slot2, slot3, result_text, win_amount, spin_cost, credit_after)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        [$user_id, $slot1, $slot2, $slot3, $result, $winAmount, $spinCost, $currentMoney]
    );

    $_SESSION['result'] = $result;
    $_SESSION['slots'] = $slots;
    $_SESSION['currentMoney'] = $currentMoney;
    $_SESSION['winAmount'] = $winAmount;
    $_SESSION['xpGain'] = $xpGain;

    header("Location: ./");
    exit;
}
?>
