<?php
session_start();
const GAMEFILE = __DIR__ . '/games.json';

if(!isset($_SESSION['username'],$_SESSION['game_id'])){
    header('Location: login.php'); exit;
}

$username = $_SESSION['username'];
$gameId   = $_SESSION['game_id'];

/* načti + zpracuj případné přepnutí režimu */
$games = file_exists(GAMEFILE)?json_decode(file_get_contents(GAMEFILE),true):[];

if(isset($_POST['toggle']) && isset($games[$gameId])){
    /* tlačítko smí mačkat jen zakladatel (první hráč) */
    if($games[$gameId]['players'][0] === $username){
        $games[$gameId]['public'] = !$games[$gameId]['public'];
        file_put_contents(GAMEFILE,json_encode($games,JSON_PRETTY_PRINT),LOCK_EX);
    }
    header("Location: wait.php"); exit;
}

$info    = $games[$gameId] ?? ['players'=>[],'public'=>false];
$players = $info['players'];
$public  = $info['public'];
$count   = count($players);

/* pokud už jsou dva hráči -> hra startuje */
if($count >= 2){
    header('Location: game.php'); exit;
}
?>
<!DOCTYPE html><html lang="cs"><head>
<meta charset="utf-8"><title>Čekárna</title>
<script>setTimeout(()=>location.reload(),3000);</script>
</head><body>
<h1>Čekáš na dalšího hráče…</h1>
<p>Hra: <b><?=htmlspecialchars($gameId)?></b></p>

<p>Režim: <?= $public?'Public (může se připojit kdokoli)':'Private (pouze s ID)' ?></p>

<?php if($players[0]===$username): /* tlačítko vidí jen zakladatel */?>
<form method="post" style="margin-bottom:1em">
    <button name="toggle">Přepnout na <?= $public?'Private':'Public' ?></button>
</form>
<?php endif; ?>

<p>Připojení hráči (<?= $count ?>/2):</p>
<ul><?php foreach($players as $p) echo '<li>'.htmlspecialchars($p).'</li>'; ?></ul>

<p>Stránka se každé 3&nbsp;s obnovuje.</p>
</body></html>