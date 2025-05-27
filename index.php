<?php
session_start();
$currentMoney = $_SESSION['currentMoney'] ?? 0;
$result = $_SESSION['result'] ?? '';
$slots = $_SESSION['slots'] ?? [];
$spinCost = $_SESSION['spinCost'] ?? 50;

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
        <label for="spinCost">Kolik chceš vsadit (10–10 000 Kč): </label>
        <input
            type="number"
            name="spinCost"
            id="spinCost"
            value="<?= htmlspecialchars($spinCost) ?>"
            min="10"
            max="10000"
            step="10"
            required
        >
        <button type="submit">Zatočit</button>
    </form>
</body>
</html>
