<?php
session_start();

$error = '';
$gamesFile = __DIR__ . '/games.json';

// Načti seznam her (nebo prázdné pole)
$games = file_exists($gamesFile) ? json_decode(file_get_contents($gamesFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if ($username === '') {
        $error = "Zadej jméno!";
    } else {
        // Zkontroluj, jestli už hráč není přihlášen do nějaké hry
        foreach ($games as $gameId => $game) {
            if (in_array($username, $game['players'])) {
                $_SESSION['username'] = $username;
                $_SESSION['game_id'] = $gameId;
                header('Location: wait.php');
                exit;
            }
        }

        // Přihlas se do hry čekající na hráče (max 2 hráči)
        $found = false;
        foreach ($games as $gameId => &$game) {
            if (count($game['players']) === 1 && !in_array($username, $game['players'])) {
                $game['players'][] = $username;
                $found = true;
                $_SESSION['username'] = $username;
                $_SESSION['game_id'] = $gameId;
                break;
            }
        }
        unset($game);

        // Pokud se žádná hra nenašla, vytvoř novou
        if (!$found) {
            $gameId = uniqid('game_', true);
            $games[$gameId] = [
                'players' => [$username],
                'turn' => 0,
                'moves' => [],
                'finished' => false
            ];
            $_SESSION['username'] = $username;
            $_SESSION['game_id'] = $gameId;
        }

        // Ulož hry
        file_put_contents($gamesFile, json_encode($games, JSON_PRETTY_PRINT));

        header('Location: wait.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <title>Přihlášení do Blackjacku</title>
</head>
<body>
    <h1>Přihlášení do Blackjack hry</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Jméno hráče: <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" /></label><br><br>
        <button type="submit">Přihlásit se</button>
    </form>
</body>
</html>