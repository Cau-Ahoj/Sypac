<?php
session_start();
const GAMEFILE = __DIR__ . '/games.json';

/* ---------- pomocné funkce ---------- */
function loadGames(): array {
    return file_exists(GAMEFILE) ? json_decode(file_get_contents(GAMEFILE), true) : [];
}
function saveGames(array $g): void {
    file_put_contents(GAMEFILE, json_encode($g, JSON_PRETTY_PRINT), LOCK_EX);
}

/* ---------- zpracování formuláře ---------- */
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $gameId   = trim($_POST['game_id'] ?? '');

    if ($username === '') {
        $error = 'Zadej jméno!';
    } else {
        $games = loadGames();

        /* === připojení do náhodné PUBLIC hry === */
        if ($gameId === '') {
            $gameId = null;
            foreach ($games as $gid => $info) {
                if ($info['public'] && count($info['players']) === 1) {
                    $gameId = $gid;
                    break;
                }
            }
            if ($gameId === null) {                        // žádná volná → vytvoř novou
                $gameId = 'game_' . bin2hex(random_bytes(4));
                $games[$gameId] = ['players' => [], 'public' => true];
            }
        } else {  /* === připojení do PRIVATE hry === */
            if (!isset($games[$gameId])) {
                $error = 'Hra s tímto ID neexistuje.';
            } elseif (count($games[$gameId]['players']) >= 2) {
                $error = 'Tato místnost je plná.';
            }
        }

        /* přidání hráče, uložení & redirect */
        if ($error === '') {
            if (!in_array($username, $games[$gameId]['players'], true)) {
                $games[$gameId]['players'][] = $username;
                saveGames($games);
            }
            $_SESSION['username'] = $username;
            $_SESSION['game_id']  = $gameId;
            header('Location: wait.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html><html lang="cs"><head>
<meta charset="utf-8"><title>Přihlášení</title></head><body>
<h1>Přihlášení do Blackjacku</h1>
<?php if($error): ?><p style="color:red"><?=htmlspecialchars($error)?></p><?php endif;?>

<h2>Náhodná (public) hra</h2>
<form method="post">
    <label>Jméno: <input name="username" required></label>
    <button>Připojit se</button>
</form>

<hr>

<h2>Privátní hra (game ID)</h2>
<form method="post">
    <label>Jméno: <input name="username" required></label><br>
    <label>ID hry: <input name="game_id" required></label><br>
    <button>Připojit se</button>
</form>
</body></html>