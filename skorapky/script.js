let rewards = [];
let gameReady = false;
let shellSelected = false;

window.onload = () => {
  fetch('db/get_rewards.php')
    .then(res => {
      if (!res.ok) throw new Error('Network error');
      return res.json();
    })
    .then(data => {
      rewards = data;
      console.log('Načtené odměny:', rewards);
      document.getElementById('startBtn').disabled = false;
    })
    .catch(error => {
      console.error('Chyba při načítání odměn:', error);
      document.getElementById('reward').innerText = 'Chyba při načítání odměn.';
    });
};

document.getElementById('startBtn').onclick = () => {
  if (rewards.length === 0) {
    alert('Odměny nejsou dostupné.');
    return;
  }
  gameReady = true;
  shellSelected = false;
  document.getElementById('reward').innerText = '';
  document.querySelectorAll('.shell').forEach(btn => btn.disabled = false);
  document.getElementById('startBtn').disabled = true;
};

function chooseShell(e) {
  if (!gameReady || shellSelected) return;

  shellSelected = true;
  gameReady = false;

  const reward = rewards[Math.floor(Math.random() * rewards.length)];
  document.getElementById('reward').innerText = `Vyhrál jsi: ${reward}`;
  console.log(`Vyhrál jsi: ${reward}`);

  document.querySelectorAll('.shell').forEach(btn => btn.disabled = true);
  document.getElementById('startBtn').disabled = false;
}

document.querySelectorAll('.shell').forEach(btn => {
  btn.addEventListener('click', chooseShell);
});
