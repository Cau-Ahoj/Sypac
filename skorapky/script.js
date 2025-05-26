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

  // Náhodná výhra
  const reward = rewards[Math.floor(Math.random() * rewards.length)];

  if (!reward) {
    console.error('Chyba při výběru výhry.');
    document.getElementById('reward').innerText = 'Výhra nenalezena.';
    return;
  }

  // Zobrazit výhru
  document.getElementById('reward').innerText = `Vyhrál jsi: ${reward.name}`;
  console.log(`Vyhrál jsi: ${reward.name}`);

  // Zakázat tlačítka
  document.querySelectorAll('.shell').forEach(btn => btn.disabled = true);
  document.getElementById('startBtn').disabled = false;

  // Odeslat výhru na server
  // Odeslat výhru na server s user_id = 1
fetch('validate.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    user_id: 1,
    reward_id: reward.id
  })
})
.then(res => res.json())
.then(data => {
  if (!data.success) {
    console.error('Nepodařilo se uložit výhru:', data.error);
  } else {
    console.log('Výhra úspěšně zapsána do databáze.');
  }
})
.catch(error => {
  console.error('Chyba při ukládání výhry:', error);
});

}

// Přiřazení obsluhy kliknutí ke všem skořápkám
document.querySelectorAll('.shell').forEach(btn => {
  btn.addEventListener('click', chooseShell);
});
