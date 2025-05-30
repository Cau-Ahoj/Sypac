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
        document.getElementById('userBalance').innerText = `Credits: ${userBalance}`;
      } else {
        document.getElementById('userBalance').innerText = `Error: ${data.error}`;
      }
    })
    .catch(err => {
      document.getElementById('userBalance').innerText = `Error: ${err.message}`;
    });
}

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
  document.getElementById('shellButtons').classList.add('disabled-shells');
  hideAllBalls();
  showBall();

  setTimeout(() => {
    hideAllBalls();
    liftAllShells(() => {
      setTimeout(() => {
        dropAllShells(() => {
          shuffleShells(10000, 29, () => {
            document.getElementById('shellButtons').classList.remove('disabled-shells');
            gameReady = true;

            // Zobraz hlášku "Choose one"
            const msg = document.getElementById('chooseMessage');
            msg.style.display = 'block';
            msg.innerText = 'Choose one';
          });
        });
      }, 400);
    });
  }, 1000);
}

window.onload = () => {
  if (typeof isLoggedIn !== "undefined" && !isLoggedIn) {
    window.location.href = "https://sypac.workspace-sosceskybrod.cz/login/";
    return;
  }

  (function detectDevTools() {
    const threshold = 160;
    const check = () => {
      const widthThreshold = window.outerWidth - window.innerWidth > threshold;
      const heightThreshold = window.outerHeight - window.innerHeight > threshold;

      if (widthThreshold || heightThreshold) {
        document.body.innerHTML = '<div class="devtools-detected">Developer tools detected. Access to this game has been blocked.</div>';
      }
    };
    setInterval(check, 1000);
  })();

  updateBalance();
  document.getElementById('startBtn').disabled = false;
  document.querySelectorAll('.shell').forEach((shell, index) => {
    shell.style.left = `${shellPositions[index]}px`;
  });

  const savedGame = localStorage.getItem('activeGame');
  if (savedGame) {
    const data = JSON.parse(savedGame);
    currentBet = data.currentBet;
    ballPosition = data.ballPosition;
    inDoubleRound = data.inDoubleRound;
    lastWinAmount = data.lastWinAmount;
    shellSelected = false;
    gameReady = false;

    document.getElementById('startBtn').style.display = 'none';
    document.querySelector('.bet-input-group').style.display = 'none';
    document.getElementById('reward').innerText = 'Game resumed! Watch the shells...';

    startGameSequence(inDoubleRound);
  }
};

document.getElementById('startBtn').onclick = () => {
  const betInput = document.getElementById('betAmountInput');
  const betAmount = parseInt(betInput.value, 10);

  if (isNaN(betAmount) || betAmount <= 0) {
    document.getElementById('reward').innerText = 'Enter valid balance';
    betInput.focus();
    return;
  }

  if (betAmount > userBalance) {
    document.getElementById('reward').innerText = `Not enough money`;
    return;
  }

  currentBet = betAmount;
  shellSelected = false;
  gameReady = false;
  lastWinAmount = 0;
  inDoubleRound = false;

  document.getElementById('reward').innerText = `Bet: ${currentBet}`;
  document.getElementById('startBtn').disabled = true;
  document.getElementById('startBtn').style.display = 'none';
  betInput.disabled = true;
  document.querySelector('.bet-input-group').style.display = 'none';

  ballPosition = Math.floor(Math.random() * 3) + 1;
  hideAllBalls();

  localStorage.setItem('activeGame', JSON.stringify({
    currentBet,
    ballPosition,
    inDoubleRound,
    lastWinAmount
  }));

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

  // Skryj hlášku "Choose one"
  document.getElementById('chooseMessage').style.display = 'none';

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
      message = `You won ${wonAmount} credits!`;
      document.getElementById('afterWinControls').style.display = 'block';
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

    localStorage.removeItem('activeGame');
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

  ballPosition = Math.floor(Math.random() * 3) + 1;

  localStorage.setItem('activeGame', JSON.stringify({
    currentBet,
    ballPosition,
    inDoubleRound,
    lastWinAmount
  }));

  startGameSequence(true);
};

document.getElementById('collectBtn').onclick = () => {
  localStorage.removeItem('activeGame');
  document.getElementById('afterWinControls').style.display = 'none';
  document.getElementById('startBtn').style.display = 'inline-block';
  document.querySelector('.bet-input-group').style.display = 'flex';
  hideAllBalls();
};
