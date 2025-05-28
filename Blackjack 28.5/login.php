<?php
session_start();
require_once 'db/database.php';
$db = new DB();

$error = '';
$username = trim($_POST['username'] ?? '');
$gameId   = trim($_POST['game_id'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($username === '') {
        $error = 'Zadej jméno!';
    } else {
        // Zjisti nebo vytvoř uživatele v tabulce users
        $user = $db->getOne("SELECT id FROM users WHERE username = ?", [$username]);
        if (!$user) {
            $db->run("INSERT INTO users (username, password) VALUES (?, ?)", [$username, password_hash('default', PASSWORD_DEFAULT)]);
            $user = $db->getOne("SELECT id FROM users WHERE username = ?", [$username]);
        }
        $userId = $user['id'];

        if ($gameId === 'random') {
            $existing = $db->getOne("SELECT id FROM games WHERE public = 1 AND id NOT IN (SELECT game_id FROM players GROUP BY game_id HAVING COUNT(*) >= 2) LIMIT 1");
            if ($existing) {
                $gameId = $existing['id'];
            } else {
                $db->run("INSERT INTO games (public) VALUES (1)");
                $row = $db->getOne("SELECT LAST_INSERT_ID() AS id");
                $gameId = $row['id'];
            }
        } else {
            $game = $db->getOne("SELECT id FROM games WHERE id = ?", [$gameId]);
            if (!$game) {
                $error = 'Hra s tímto ID neexistuje.';
            } else {
                $count = $db->getOne("SELECT COUNT(*) AS cnt FROM players WHERE game_id = ? AND `left` = 0", [$gameId]);
                if ($count['cnt'] == 0) {
                    $db->run("DELETE FROM players WHERE game_id = ?", [$gameId]);
                    $db->run("DELETE FROM games WHERE id = ?", [$gameId]);
                    $error = 'Tato hra již byla prázdná a byla smazána.';
                } elseif ($count['cnt'] >= 2) {
                    $error = 'Tato místnost je plná.';
                }
            }
        }

        if ($error === '') {
            $exists = $db->getOne("SELECT id FROM players WHERE game_id = ? AND user_id = ?", [$gameId, $userId]);
            if (!$exists) {
                // Záznam neexistuje → vlož nového hráče do hry
                $db->run("INSERT INTO players (game_id, user_id, user_name, hand, score, finished, `left`) VALUES (?, ?, ?, '[]', 0, 0, 0)", [$gameId, $userId, $username]);
            } else {
        // Záznam existuje → aktualizuj pouze game_id, pokud je jiný
                $db->run("UPDATE players SET game_id = ?, `left` = 0 WHERE user_id = ?", [$gameId, $userId]);
            }
            $_SESSION['username'] = $username;
            $_SESSION['user_id']  = $userId;
            $_SESSION['game_id']  = $gameId;
            header('Location: wait.php');
            exit;
        }
    }
}

$openGames = $db->getAll("
    SELECT g.id, COUNT(p.id) AS players 
    FROM games g 
    LEFT JOIN players p ON g.id = p.game_id AND p.`left` = 0
    WHERE g.public = 1 
    GROUP BY g.id 
    HAVING players < 2
    ORDER BY g.id ASC
");
?>
<!DOCTYPE html>
<html lang="cs"><head>
<meta charset="utf-8"><title>Přihlášení</title></head><body>
<h1>Přihlášení do Blackjacku</h1>
<?php if($error): ?><p style="color:red"><?=htmlspecialchars($error)?></p><?php endif; ?>

<h2>Náhodná veřejná hra</h2>
<form method="post">
    <label>Jméno: <input name="username" required></label>
    <input type="hidden" name="game_id" value="random">
    <button>Připojit se</button>
</form>

<hr>

<h2>Otevřené veřejné hry</h2>
<form method="post">
    <label>Jméno: <input name="username" required></label>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th>ID hry</th><th>Počet hráčů</th><th>Akce</th></tr>
        <?php foreach ($openGames as $game): ?>
        <tr>
            <td><?=htmlspecialchars($game['id'])?></td>
            <td><?=htmlspecialchars($game['players'])?> / 2</td>
            <td>
                <button type="submit" name="game_id" value="<?=htmlspecialchars($game['id'])?>">Připojit se</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</form>

</body></html>