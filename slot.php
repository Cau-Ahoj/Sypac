<?php
session_start();
require_once 'database.php';
$db = new DB();

$spinCost = 50;
$_SESSION['user_id'] = 1;

if (!isset($_SESSION['user_id'])) {
    die("Nepřihlášený uživatel.");
}

$user_id = $_SESSION['user_id'];
$user = $db->getOne("SELECT * FROM users WHERE id = ?", [$user_id]);

if (!$user) {
    die("Uživatel neexistuje.");
}

$currentMoney = $user['money'];

// Ošetření POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($currentMoney < $spinCost) {
        $_SESSION['slot_message'] = [
            'error' => true,
            'text' => "Nemáš dostatek kreditu!"
        ];
    } else {
        $currentMoney -= $spinCost;

        $symbols = [
            '🍒', '🍒', '🍒', '🍒',
            '🍋', '🍋', '🍋', '🍋',
            '🍇', '🍇', '🍇',
            '🍀', '🍀', '🍀',
            '💎', '💎',
            '7️⃣', '7️⃣',
            '🃏',
        ];

        $slot1 = $symbols[array_rand($symbols)];
        $slot2 = $symbols[array_rand($symbols)];
        $slot3 = $symbols[array_rand($symbols)];
        $slots = [$slot1, $slot2, $slot3];

        function count_symbols($slots) {
            $counts = [];
            foreach ($slots as $s) {
                if (!isset($counts[$s])) $counts[$s] = 0;
                $counts[$s]++;
            }
            return $counts;
        }

        $counts = count_symbols($slots);
        $jokerCount = $counts['🃏'] ?? 0;
        $winAmount = 0;
        $result = "";

        if ($jokerCount === 3) {
            $result = "🃏🃏 Tři jesteri 🃏🃏! Mega výhra!";
            $winAmount = 1500;
        } elseif ($jokerCount > 0) {
            unset($counts['🃏']);
            arsort($counts);
            $mainSymbol = key($counts);
            $symbolCount = current($counts) + $jokerCount;

            if ($symbolCount >= 3) {
                switch ($mainSymbol) {
                    case '7️⃣':
                        $result = "JACKPOOT 💰 s jokerem!";
                        $winAmount = 800;
                        break;
                    case '💎':
                        $result = "💎💎 Diamantyyy s jokerem 💎💎!";
                        $winAmount = 600;
                        break;
                    default:
                        $result = "Výhra díky jokerovi🃏!";
                        $winAmount = 500;
                }
            }
        } elseif ($slot1 === $slot2 && $slot2 === $slot3) {
            switch ($slot1) {
                case '7️⃣':
                    $result = "JACKPOOT 💰! Tři sedmičky!";
                    $winAmount = 1200;
                    break;
                case '💎':
                    $result = "💎💎 Diamantyyy 💎💎!";
                    $winAmount = 700;
                    break;
                default:
                    $result = "Našel jsi tři stejné symboly!";
                    $winAmount = 400;
            }
        } elseif ($slot1 === $slot2 || $slot2 === $slot3 || $slot1 === $slot3) {
            $result = "Bohužel prohrál si!";
        } else {
            $result = "Nic si nevyhrál.";
        }

        $currentMoney += $winAmount;

        // Uložení výsledku
        $db->run(
            "INSERT INTO slot_results (user_id, slot1, slot2, slot3, result_text, win_amount, spin_cost, credit_after)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $user_id,
                $slot1,
                $slot2,
                $slot3,
                $result,
                $winAmount,
                $spinCost,
                $currentMoney
            ]
        );

        // Aktualizace kreditu
        $db->run("UPDATE users SET money = ? WHERE id = ?", [$currentMoney, $user_id]);

        // Uložení pro GET
        $_SESSION['slot_result'] = [
            'slots' => $slots,
            'result' => $result,
            'money' => $currentMoney
        ];
    }

    // Přesměrování (POST-Redirect-GET)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

echo "<h2>🎰 Vítej ve hře automaty 🎰</h2>";

if (isset($_SESSION['slot_result'])) {
    $r = $_SESSION['slot_result'];
    echo "<h1>{$r['slots'][0]} | {$r['slots'][1]} | {$r['slots'][2]}</h1>";
    echo "<p>{$r['result']}</p>";
    echo "<p><strong>Aktuální kredit:</strong> {$r['money']}</p>";
    unset($_SESSION['slot_result']);
} else {
    echo "<p><strong>Aktuální kredit:</strong> {$currentMoney}</p>";
}

if (isset($_SESSION['slot_message'])) {
    $msg = $_SESSION['slot_message'];
    echo "<p style='color:" . ($msg['error'] ? "red" : "green") . ";'>{$msg['text']}</p>";
    unset($_SESSION['slot_message']);
}


echo '<form method="post"><button type="submit">Zatočit</button></form>';
?>
