<?php
session_start();
require_once '../database.php';
$db = new DB();

// Uživatel musí být přihlášen
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../login/");
    exit;
}

// Získání aktuálního kreditu z DB
$user = $db->getOne("SELECT money FROM users WHERE id = ?", [$user_id]);
$currentMoney = $user['money'] ?? 0;

// Výsledky posledního tahu
$result = $_SESSION['result'] ?? '';
$slots = $_SESSION['slots'] ?? [];
$spinCost = $_SESSION['spinCost'] ?? 50;

// Vyčištění slotů a výsledku (ale NE kredit!)
unset($_SESSION['result'], $_SESSION['slots']);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Slot Automat</title>
</head>
<body>
    <h2>🎰 Vítej ve hře automaty 🎰</h2>
    <p><strong>Aktuální kredit:</strong> <?= $currentMoney ?> Kč</p>

    <?php if (!empty($slots)): ?>
        <h1><?= $slots[0] ?> | <?= $slots[1] ?> | <?= $slots[2] ?></h1>
        <p><?= $result ?></p>
    <?php elseif ($result): ?>
        <p><?= $result ?></p>
    <?php endif; ?>

    <form method="post" action="slot.php">
        <label for="spinCost">Kolik chceš vsadit (10 – 200 000 Kč): </label>
        <input
            type="number"
            name="spinCost"
            id="spinCost"
            value="<?= htmlspecialchars($spinCost) ?>"
            min="10"
            max="200000"
            step="10"
            required
        >
        <button type="submit">Zatočit</button>
    </form>
</body>
</html>
