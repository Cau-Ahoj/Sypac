<?php
/**
 * chat.php – univerzální chat pro každou hru
 *  •  GET  → vrací JSON všech zpráv dané game_id
 *  •  POST → přidá zprávu {user,text} do chat.json
 */
session_start();
const CHAT_FILE = __DIR__ . '/chat.json';

if (!isset($_SESSION['username'], $_SESSION['game_id'])) {
    http_response_code(403);  exit('Unauthorized');
}
$user   = $_SESSION['username'];
$gameId = $_SESSION['game_id'];

/* ---------- Načti (nebo vytvoř) strukturu ---------------- */
$all = [];
if (file_exists(CHAT_FILE)) {
    $raw = file_get_contents(CHAT_FILE);
    $all = $raw ? json_decode($raw,true) : [];
}
if (!isset($all[$gameId])) $all[$gameId] = [];

/* ---------- POST: ulož zprávu ---------------------------- */
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $body = file_get_contents('php://input');
    $data = json_decode($body,true);
    $text = trim($data['text'] ?? '');

    if ($text !== '') {
        $all[$gameId][] = ['user'=>$user,'text'=>$text,'time'=>time()];
        /* bezpečný zápis s file lockem */
        $fp = fopen(CHAT_FILE,'c+');
        if ($fp) {
            flock($fp,LOCK_EX);
            ftruncate($fp,0);
            fwrite($fp,json_encode($all,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            fflush($fp);
            flock($fp,LOCK_UN);
            fclose($fp);
        }
    }
    exit;               // po POST nevracíme nic – front-end si pak zavolá GET
}

/* ---------- GET: vrať JSON zpráv ------------------------- */
header('Content-Type: application/json; charset=utf-8');
echo json_encode($all[$gameId]);
?>