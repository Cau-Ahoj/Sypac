<?php require_once 'game_logic.php'; ?>
<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="utf-8">
<title>Blackjack</title>
<link rel="stylesheet" href="game.css">
<style>.cards{list-style:none;padding:0}.cards li{display:inline-block;margin:2px 4px;padding:3px 6px;border:1px solid #555;border-radius:4px}</style>
</head>
<body>

<h1>Blackjack</h1>
<p><b>ID hry:</b> <span id="gid"><?= htmlspecialchars($_SESSION['game_id']) ?></span></p>

<div id="board"></div>

<hr>
<h3>Chat</h3>
<div id="chat-box" style="border:1px solid #aaa;height:180px;overflow-y:auto;padding:4px"></div>
<form id="chat-form">
  <input id="chat-input" style="width:70%" placeholder="Napiš zprávu…" required>
  <button>Odeslat</button>
</form>

<script>
const board   = document.getElementById('board'),
      gid     = document.getElementById('gid').textContent,
      me      = <?= json_encode($_SESSION['username']) ?>;

/* ---------- RENDER HRACÍ PLOCHY ---------- */
function render(state){
    const {players,hands,stood,turn,done} = state;
    board.innerHTML = '';

    const h=(p)=>hands[p]??[];
    const ulCards = c=>`<ul class="cards">${c.map(k=>`<li>${k.value} ${k.suit}</li>`).join('')}</ul>`;
    players.forEach(p=>{
        board.innerHTML += `<h2>${p}${p===me?' (ty)':''}</h2>`
              + ulCards(h(p))
              + `<p>Skóre: ${score(h(p))} ${(stood[p]?'– Stand':'')}</p>`;
    });

    board.innerHTML += `<h2>Dealer</h2>${ulCards(hands.Dealer||[])}<p>Skóre: ${score(hands.Dealer||[])}</p>`;

    /* Ovládání */
    if(!done && turn===me){
        board.innerHTML += `
        <button onclick="send('hit')">Hit</button>
        <button onclick="send('stand')">Stand</button>`;
    }else if(done){
        board.innerHTML += '<p><b>Partie skončila</b></p><button onclick="send(`newgame`)">Nová hra</button>';
    }else{
        board.innerHTML += '<p>Čekám na soupeře…</p>';
    }
}

/* ---------- AJAX volání ---------- */
async function getState(){ const r=await fetch('game_logic.php?ajax=1'); return r.json(); }
async function send(action){
    const fd = new FormData(); fd.append('action',action);
    const r  = await fetch('game_logic.php',{method:'POST',body:fd});
    const js = await r.json();
    if(js.reload){location.reload();return;}
    render(js);
}

/* ---------- CHAT ---------- */
const chatBox = document.getElementById('chat-box'),
      chatFrm = document.getElementById('chat-form'),
      chatInp = document.getElementById('chat-input');

async function loadChat() {
    const r = await fetch('chat.php?_=' + Date.now());   // ← změna
    if (!r.ok) return;
    const data = await r.json();
    chatBox.innerHTML = data.map(
        m => `<p><b>${escapeHtml(m.user)}:</b> ${escapeHtml(m.text)}</p>`
    ).join('');
    chatBox.scrollTop = chatBox.scrollHeight;
}

setInterval(loadChat, 3000);
loadChat();

chatFrm.addEventListener('submit', async e => {
    e.preventDefault();
    const txt = chatInp.value.trim();
    if (!txt) return;

    await fetch('chat.php', {               // POST pořád na chat.php
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user: me, text: txt })
    });

    /* zobrazím okamžitě lokálně */
    chatBox.innerHTML += `<p><b>${escapeHtml(me)}:</b> ${escapeHtml(txt)}</p>`;
    chatBox.scrollTop = chatBox.scrollHeight;
    chatInp.value = '';
});

function escapeHtml(t) {
    return t.replace(/[&<>"']/g, c => ({
        '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    }[c]));
}


/* ---------- VÝPOČET SKÓRE (stejný jako v PHP) ---------- */
function score(cards){
    let s=0,aces=0;
    cards.forEach(c=>{s+=c.value;if(c.value===11)aces++;});
    while(s>21&&aces--)s-=10;
    return s;
}

/* ---------- Auto-refresh hry i chatu ---------- */
getState().then(render);
setInterval(async ()=>render(await getState()),3000);
setInterval(loadChat,3000);
loadChat();
</script>
</body>
</html>