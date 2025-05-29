// Herní proměnné
let gameReady = false;
let shellSelected = false;
let currentBet = 0;
let userBalance = 0;
let lastWinAmount = 0;
let inDoubleRound = false;
let ballPosition = 0;

const shellPositions = [0, 130, 260];

function updateBalance() {
  fetch('get_balance.php')
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        userBalance = data.balance;
        document.getElementById('userBalance').innerText = `Balance: ${userBalance}`;
      } else {
        document.getElementById('userBalance').innerText = `Error: ${data.error}`;
      }
    })
    .catch(err => {
      document.getElementById('userBalance').innerText = `Error: ${err.message}`;
    });
}

window.onload = () => {
  updateBalance();
  document.getElementById('startBtn').disabled = false;
  document.querySelectorAll('.shell').forEach((shell, index) => {
    shell.style.left = `${shellPositions[index]}px`;
  });
};

function hideAllBalls() {
  document.querySelectorAll('.ball').forEach(ball => {
    ball.classList.remove('shown');
    ball.style.zIndex = '-1';
  });
}

function showBall() {
  const ballEl = document.querySelector(`.ball${ballPosition}`);
  if (ballEl) {
    ballEl.classList.add('shown');
    ballEl.style.zIndex = '10';
  }
}

function shuffleShells(duration, swaps, onComplete) {
  const shells = [...document.querySelectorAll('.shell')];
  let positionsState = [0, 1, 2];

  const swapOnce = () => {
    let i = Math.floor(Math.random() * 3);
    let j;
    do { j = Math.floor(Math.random() * 3); } while (j === i);

    [positionsState[i], positionsState[j]] = [positionsState[j], positionsState[i]];

    shells.forEach((shell, index) => {
      const newPos = shellPositions[positionsState[index]];
      shell.style.left = `${newPos}px`;
    });
  };

  let count = 0;
  const interval = setInterval(() => {
    swapOnce();
    count++;
    if (count >= swaps) {
      clearInterval(interval);
      setTimeout(() => {
        if (onComplete) onComplete();
      }, 300);
    }
  }, duration / swaps);
}

function liftAllShells(callback) {
  document.querySelectorAll('.shell').forEach(el => {
    el.classList.remove('drop');
    el.classList.add('lift');
  });
  setTimeout(() => {
    if (callback) callback();
  }, 400);
}

function dropAllShells(callback) {
  document.querySelectorAll('.shell').forEach(el => {
    el.classList.remove('lift');
    el.classList.add('drop');
  });
  setTimeout(() => {
    if (callback) callback();
  }, 300);
}

function startGameSequence(isDouble = false) {
  hideAllBalls();
  showBall();

  setTimeout(() => {
    liftAllShells(() => {
      setTimeout(() => {
        showBall();
        hideAllBalls();
        dropAllShells(() => {
          shuffleShells(5000, 20, () => {
            gameReady = true;
            document.querySelectorAll('.shell').forEach(btn => btn.disabled = false);
          });
        });
      }, 400);
    });
  }, 1000); // míček je vidět 1 sekundu
}

document.getElementById('startBtn').onclick = () => {
  const betInput = document.getElementById('betAmountInput');
  const betAmount = parseInt(betInput.value, 10);

  if (isNaN(betAmount) || betAmount <= 0) {
    document.getElementById('reward').innerText = 'Enter valid balance';
    betInput.focus();
    return;
  }

  if (betAmount > userBalance) {
    document.getElementById('reward').innerText = `Not enough money brokie`;
    return;
  }

  currentBet = betAmount;
  shellSelected = false;
  gameReady = false;
  lastWinAmount = 0;
  inDoubleRound = false;

  document.getElementById('reward').innerText = `Bet: ${currentBet}`;
  document.querySelectorAll('.shell').forEach(btn => btn.disabled = true);
  document.getElementById('startBtn').disabled = true;
  document.getElementById('startBtn').style.display = 'none';
  betInput.disabled = true;
  document.querySelector('.bet-input-group').style.display = 'none';

  ballPosition = Math.floor(Math.random() * 3) + 1; // 1–3
  hideAllBalls();

  fetch('validate.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      bet_amount: currentBet,
      is_win: false,
      won_amount: 0,
      game_type: 'normal'
    })
  }).then(res => res.json())
    .then(data => {
      if (data.success && data.new_balance !== undefined) {
        updateBalance();
      }
    });

  startGameSequence();
};

function chooseShell(e) {
  if (!gameReady || shellSelected) return;

  shellSelected = true;
  gameReady = false;

  const clickedId = e.currentTarget.id;
  const selectedIndex = parseInt(clickedId.replace('shell', ''));

  const isWin = selectedIndex === ballPosition;
  const wonAmount = inDoubleRound ? lastWinAmount : currentBet * 2;

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
      message = `You won ${lastWinAmount} dollars. Wanna try again?`;
      document.getElementById('afterWinControls').style.display = 'block';
      document.getElementById('startBtn').style.display = 'none';
    } else {
      message = `You lost!`;
      lastWinAmount = 0;
      document.getElementById('afterWinControls').style.display = 'none';
      document.getElementById('startBtn').style.display = 'inline-block';
      document.querySelector('.bet-input-group').style.display = 'flex';
    }
    inDoubleRound = false;
  } else {
    if (isWin) {
      lastWinAmount = wonAmount;
      message = `You won ${wonAmount} dollars!`;
      document.getElementById('afterWinControls').style.display = 'block';
      document.getElementById('startBtn').style.display = 'none';
    } else {
      message = `You lost. Try again!`;
      document.getElementById('afterWinControls').style.display = 'none';
      document.getElementById('startBtn').style.display = 'inline-block';
      document.querySelector('.bet-input-group').style.display = 'flex';
    }
  }

  document.getElementById('reward').innerText = message;

  showBall();

  liftAllShells(() => {
    fetch('validate.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }).then(res => res.json()).then(data => {
      if (data.success) updateBalance();
    });

    document.querySelectorAll('.shell').forEach(btn => btn.disabled = true);
    document.getElementById('startBtn').disabled = false;
    document.getElementById('betAmountInput').disabled = false;
  });
}

document.querySelectorAll('.shell').forEach(btn => {
  btn.addEventListener('click', chooseShell);
});

document.getElementById('doubleBtn').onclick = () => {
  if (!lastWinAmount || lastWinAmount <= 0) {
    document.getElementById('reward').innerText = 'Something went wrong';
    return;
  }

  inDoubleRound = true;
  gameReady = false;
  shellSelected = false;
  hideAllBalls();

  document.getElementById('afterWinControls').style.display = 'none';
  document.getElementById('reward').innerText = 'Double or Nothing!';
  document.querySelectorAll('.shell').forEach(btn => btn.disabled = true);

  ballPosition = Math.floor(Math.random() * 3) + 1;
  startGameSequence(true);
};

document.getElementById('collectBtn').onclick = () => {
  document.getElementById('afterWinControls').style.display = 'none';
  document.getElementById('startBtn').style.display = 'inline-block';
  document.querySelector('.bet-input-group').style.display = 'flex';
  hideAllBalls();
};
