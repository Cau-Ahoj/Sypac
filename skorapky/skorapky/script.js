let gameReady = false;
let shellSelected = false;
let currentBet = 0;
let userBalance = 0;
let lastWinAmount = 0;
let inDoubleRound = false;

function updateBalance() {
    fetch('get_balance.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                userBalance = data.balance;
                document.getElementById('userBalance').innerText = `Zůstatek: ${userBalance} peněz`;
            } else {
                document.getElementById('userBalance').innerText = `Chyba: ${data.error}`;
            }
        })
        .catch(err => {
            document.getElementById('userBalance').innerText = `Chyba: ${err.message}`;
        });
}

window.onload = () => {
    updateBalance();
    document.getElementById('startBtn').disabled = false;
};

document.getElementById('startBtn').onclick = () => {
    const betInput = document.getElementById('betAmountInput');
    const betAmount = parseInt(betInput.value, 10);

    if (isNaN(betAmount) || betAmount <= 0) {
        document.getElementById('reward').innerText = 'Zadejte platnou částku.';
        betInput.focus();
        return;
    }

    if (betAmount > userBalance) {
        document.getElementById('reward').innerText = `Nemáte dostatek peněz (zůstatek: ${userBalance})`;
        return;
    }

    currentBet = betAmount;
    shellSelected = false;
    gameReady = true;
    lastWinAmount = 0;
    inDoubleRound = false;

    document.getElementById('reward').innerText = `Sázka: ${currentBet} peněz. Vyberte skořápku!`;
    document.querySelectorAll('.shell').forEach(btn => btn.disabled = false);
    document.getElementById('startBtn').disabled = true;
    betInput.disabled = true;

    fetch('validate.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            bet_amount: currentBet,
            is_win: false, // ještě nevíme
            won_amount: 0,
            game_type: 'normal'
        })
    }).then(res => res.json())
      .then(data => {
          if (data.success) updateBalance();
          else console.error(data.error);
      });    
};

function chooseShell(e) {
    if (!gameReady || shellSelected) return;

    shellSelected = true;
    gameReady = false;

    const isWin = Math.random() < (inDoubleRound ? 0.7 : 1 / 2);
    const wonAmount = inDoubleRound ? lastWinAmount : currentBet * 5;

    let message = '';
    let payload = {
        bet_amount: 0,
        is_win: isWin,
        won_amount: inDoubleRound ? (isWin ? lastWinAmount : -lastWinAmount) : wonAmount,
        game_type: inDoubleRound ? 'double' : 'normal'
    };

    if (inDoubleRound) {
        if (isWin) {
            lastWinAmount *= 2;
            message = `Double or Nothing vyšlo! Výhra je nyní ${lastWinAmount} peněz. Chceš to zkusit znovu?`;
            document.getElementById('afterWinControls').style.display = 'block';
            document.getElementById('startBtn').style.display = 'none';
        } else {
            message = `Bohužel, prohráváš svou výhru.`;
            lastWinAmount = 0;
            document.getElementById('afterWinControls').style.display = 'none';
            document.getElementById('startBtn').style.display = 'inline-block';
        }

        inDoubleRound = false;

    } else {
        if (isWin) {
            lastWinAmount = wonAmount;
            message = `Vyhrál jsi ${wonAmount} peněz! Chceš zkusit Double or Nothing?`;
            document.getElementById('afterWinControls').style.display = 'block';
            document.getElementById('startBtn').style.display = 'none';
        } else {
            message = `Nevyhrál jsi. Zkus to znovu.`;
            document.getElementById('afterWinControls').style.display = 'none';
            document.getElementById('startBtn').style.display = 'inline-block';
        }
    }

    document.getElementById('reward').innerText = message;

    fetch('validate.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).then(res => res.json())
      .then(data => {
          if (data.success) updateBalance();
      });

    document.querySelectorAll('.shell').forEach(btn => btn.disabled = true);
    document.getElementById('startBtn').disabled = false;
    document.getElementById('betAmountInput').disabled = false;
}

document.querySelectorAll('.shell').forEach(btn => {
    btn.addEventListener('click', chooseShell);
});

document.getElementById('doubleBtn').onclick = () => {
    if (!lastWinAmount || lastWinAmount <= 0) {
        document.getElementById('reward').innerText = 'Není co zdvojnásobit!';
        return;
    }

    inDoubleRound = true;
    gameReady = true;
    shellSelected = false;

    document.getElementById('afterWinControls').style.display = 'none';
    document.getElementById('reward').innerText = 'Double or Nothing! Vyber skořápku pro šanci na zdvojnásobení!';
    document.querySelectorAll('.shell').forEach(btn => btn.disabled = false);
};

document.getElementById('collectBtn').onclick = () => {
    document.getElementById('afterWinControls').style.display = 'none';
    document.getElementById('startBtn').style.display = 'inline-block';
    document.getElementById('reward').innerText += ' Výhra připsána.';
};