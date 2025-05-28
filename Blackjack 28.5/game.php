<?php
session_start();
require_once 'db/database.php';
$db = new DB();

if (!isset($_SESSION['user_id'], $_SESSION['game_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$gameId = $_SESSION['game_id'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <title>Blackjack</title>
  <link rel="stylesheet" href="game.css">
  <style>
    .cards { list-style: none; padding: 0; }
    .cards li { display: inline-block; margin: 2px 4px; padding: 3px 6px; border: 1px solid #555; border-radius: 4px; }
  </style>
</head>
<body>

<h1>Blackjack</h1>
<p><b>ID hry:</b> <span id="gid"><?= htmlspecialchars($gameId) ?></span></p>
<div id="board"></div>

<hr>
<h3>Chat</h3>
<div id="chat-box" style="border:1px solid #aaa;height:180px;overflow-y:auto;padding:4px"></div>
<form id="chat-form">
  <input id="chat-input" style="width:70%" placeholder="Napiš zprávu…" required>
  <button>Odeslat</button>
</form>

<script>
const board = document.getElementById('board'),
      gid = document.getElementById('gid').textContent,
      me = <?= json_encode($username) ?>;

function escapeHtml(t) {
    return t.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));
}

function score(cards) {
    let s = 0, aces = 0;
    cards.forEach(c => { s += c.value; if (c.value === 11) aces++; });
    while (s > 21 && aces--) s -= 10;
    return s;
}

function ulCards(cards) {
    return `<ul class="cards">${cards.map(c => `<li>${c.value} ${c.suit}</li>`).join('')}</ul>`;
}

function render(state) {
    const {players, hands, stood, turn, done, results} = state;
    board.innerHTML = '';

    players.forEach(p => {
        board.innerHTML += `<h2>${p}${p === me ? ' (ty)' : ''}</h2>`;
        board.innerHTML += ulCards(hands[p]);
        board.innerHTML += `<p>Skóre: ${score(hands[p])} ${stood[p] ? '– Stand' : ''}</p>`;
    });

    if (!done && turn === me) {
        if (score(hands[me]) >= 21) {
            send('stand');
        } else {
            board.innerHTML += `<button onclick="send('hit')">Hit</button> <button onclick="send('stand')">Stand</button>`;
        }
    } else if (done) {
        board.innerHTML += `<p><b>Partie skončila</b></p>`;
        players.forEach(p => {
            board.innerHTML += `<p><b>${p}</b> ${results[p]}</p>`;
        });
        board.innerHTML += `<button onclick="location.href='new_game.php'">Nová hra</button>`;
        board.innerHTML += `<button onclick="location.href='login.php'">Zpátky do loginu</button>`;
    } else {
        board.innerHTML += `<p>Čekám na soupeře nebo na jejich tah…</p>`;
    }
}

async function getState() {
    const r = await fetch('game_logic.php?ajax=1');
    const data = await r.json();
    render(data);
}

async function send(action) {
    const fd = new FormData();
    fd.append('action', action);
    const r = await fetch('game_logic.php', { method: 'POST', body: fd });
    const js = await r.json();
    if (js.reload) { location.reload(); return; }
    getState();
}

// CHAT
const chatBox = document.getElementById('chat-box'),
      chatFrm = document.getElementById('chat-form'),
      chatInp = document.getElementById('chat-input');

async function loadChat() {
    const r = await fetch('chat.php?_=' + Date.now());
    if (!r.ok) return;
    const data = await r.json();
    chatBox.innerHTML = data.map(
        m => `<p><b>${escapeHtml(m.user)}:</b> ${escapeHtml(m.text)}</p>`
    ).join('');
    chatBox.scrollTop = chatBox.scrollHeight;
}

chatFrm.addEventListener('submit', async e => {
    e.preventDefault();
    const txt = chatInp.value.trim();
    if (!txt) return;
    await fetch('chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ text: txt })
    });
    chatInp.value = '';
    loadChat();
});

setInterval(() => getState(), 1000);
getState();
loadChat();
</script>
</body>
</html>