<?php
require_once 'database.php';
session_start();

$db = new DB();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2;
}
$user_id = $_SESSION['user_id'];

// Načti uživatele
$user = $db->get("SELECT * FROM users WHERE id = ?", [$user_id]);
if (!$user) {
    die("❌ Uživatel s ID $user_id neexistuje.");
}
$user = $user[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['click'])) {
        $current_money = $user['money'];
        $current_xp = $user['xp'];
        $crit_level = $user['crit_level'];
        $crit_xp_bonus = $user['crit_xp_bonus'];
        $level = $user['lvl'];
        $bar_count = $user['xp_bar_completions'];

        // Výpočet šance na critical hit
        $crit_chance = 3 + ($crit_level * 2);
        $is_crit = rand(1, 100) <= $crit_chance;

        // XP zisk
        $xp_gain = $is_crit ? (2 + $crit_xp_bonus) : 1;
        $new_xp = $current_xp + $xp_gain;

        // Výpočet XP limitu a odměny podle levelu
        $xp_limit = 100 * pow(2, $level);
        $reward = 100 * pow(1.5, $level);

        if ($new_xp >= $xp_limit) {
            $new_money = $current_money + (int)$reward;
            $new_xp = 0;
            $bar_count++;

            if ($bar_count >= 3) {
                $level++;
                $bar_count = 0;
            }

            $db->run("UPDATE users SET money = ?, xp = ?, lvl = ?, xp_bar_completions = ? WHERE id = ?", [
                $new_money, $new_xp, $level, $bar_count, $user_id
            ]);
        } else {
            $db->run("UPDATE users SET xp = ? WHERE id = ?", [
                $new_xp, $user_id
            ]);
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['upgrade_crit'])) {
        if ($user['money'] >= 100) {
            $new_money = $user['money'] - 100;
            $new_crit_level = $user['crit_level'] + 1;
            $db->run("UPDATE users SET money = ?, crit_level = ? WHERE id = ?", [
                $new_money, $new_crit_level, $user_id
            ]);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['upgrade_crit_xp'])) {
        if ($user['money'] >= 150) {
            $new_money = $user['money'] - 150;
            $new_bonus = $user['crit_xp_bonus'] + 1;
            $db->run("UPDATE users SET money = ?, crit_xp_bonus = ? WHERE id = ?", [
                $new_money, $new_bonus, $user_id
            ]);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Clicker hra – XP bar</title>
</head>
<body>
    <h1>Clicker hra (XP Bar systém)</h1>

    <form method="POST">
        <button type="submit" name="click">⚒️ Klikni pro XP</button>
    </form>

    <form method="POST">
        <button type="submit" name="upgrade_crit">⚡ Upgrade Crit Chance (100💰)</button>
    </form>

    <form method="POST">
        <button type="submit" name="upgrade_crit_xp">🔥 Upgrade Crit XP Bonus (150💰)</button>
    </form>

    <hr>
    <p>
        <?php
        $updated = $db->get("SELECT money, xp, lvl, crit_level, crit_xp_bonus, xp_bar_completions FROM users WHERE id = ?", [$user_id])[0];
        $crit_chance = 3 + ($updated['crit_level'] * 2);
        $xp_limit = 100 * pow(2, $updated['lvl']);
        $reward = 100 * pow(1.5, $updated['lvl']);

        echo "Uživatel ID $user_id<br>";
        echo "💰 Peníze: {$updated['money']}<br>";
        echo "⭐ XP: {$updated['xp']} / $xp_limit<br>";
        echo "📶 Level: {$updated['lvl']}<br>";
        echo "🔁 XP bar dokončen: {$updated['xp_bar_completions']} / 3<br>";
        echo "💵 Výplata po vyplnění baru: " . (int)$reward . "<br>";
        echo "🔥 Crit Level: {$updated['crit_level']}<br>";
        echo "📈 Crit XP Bonus: {$updated['crit_xp_bonus']}<br>";
        echo "🎯 Šance na kritický zásah: $crit_chance%";
        ?>
    </p>
</body>
</html>
