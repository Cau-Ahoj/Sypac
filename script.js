// Game Variables
let selectedEgg = null;
let eggPurchased = false;
let eggStages = {
  bronze: [
    "/sypac/skorapky/vajicka/bronz/vajicko1.png",
    "/sypac/skorapky/vajicka/bronz/vajicko2.png",
    "/sypac/skorapky/vajicka/bronz/vajicko3.png",
    "/sypac/skorapky/vajicka/bronz/vajicko4.png",
    "/sypac/skorapky/vajicka/bronz/vajicko5.png" // Cracked open image
  ],
  silver: [
    "/sypac/skorapky/vajicka/dia/vajicko1.png",
    "/sypac/skorapky/vajicka/dia/vajicko2.png",
    "/sypac/skorapky/vajicka/dia/vajicko3.png",
    "/sypac/skorapky/vajicka/dia/vajicko4.png",
    "/sypac/skorapky/vajicka/dia/vajicko5.png"
  ],
  gold: [
    "/sypac/skorapky/vajicka/master/vajicko1.png",
    "/sypac/skorapky/vajicka/master/vajicko2.png",
    "/sypac/skorapky/vajicka/master/vajicko3.png",
    "/sypac/skorapky/vajicka/master/vajicko4.png",
    "/sypac/skorapky/vajicka/master/vajicko5.png"
  ]
};
let currentStage = 0;
let crackingInterval;

// Egg Prices
const eggPrices = {
  bronze: 50,
  silver: 100,
  gold: 200
};

// Win Chances
const winChances = {
  bronze: 0.35,
  silver: 0.6,
  gold: 0.85
};

// Prize Multipliers
const prizeMultipliers = {
  bronze: [2, 3, 5],
  silver: [3, 5, 8],
  gold: [5, 8, 10]
};

// DOM Elements
const buyBtn = document.getElementById('buyBtn');
const crackBtn = document.getElementById('crackBtn');
const selectedEggDisplay = document.getElementById('selectedEggDisplay');
const rewardBox = document.getElementById('rewardBox');
const rulesModal = document.getElementById('rulesModal');

// Select Egg
function selectEgg(type) {
  selectedEgg = type;
  eggPurchased = false;
  currentStage = 0;
  
  // Update display
  selectedEggDisplay.innerHTML = `<img src="${eggStages[type][0]}" alt="${type} Egg" class="pulse">`;
  
  // Enable buy button
  buyBtn.disabled = false;
  buyBtn.textContent = `BUY EGG (${eggPrices[type]} Kč)`;
  
  // Disable crack button until purchase
  crackBtn.disabled = true;
  
  // Clear reward box
  rewardBox.textContent = '';
  rewardBox.className = 'reward-box';
}

// Buy Egg
function buyEgg() {
  if (!selectedEgg) return;
  
  // In a real game, you would check player balance here
  eggPurchased = true;
  
  // Update UI
  buyBtn.disabled = true;
  crackBtn.disabled = false;
  
  // Show confirmation
  selectedEggDisplay.innerHTML = `<img src="${eggStages[selectedEgg][0]}" alt="${selectedEgg} Egg">`;
}

// Crack Egg
function crackEgg() {
  if (!selectedEgg || !eggPurchased) return;
  
  // Disable buttons during animation
  crackBtn.disabled = true;
  
  // Start cracking animation
  currentStage = 0;
  crackingInterval = setInterval(animateCracking, 800);
}

// Animate Cracking Process
function animateCracking() {
  currentStage++;
  
  // Update egg image
  selectedEggDisplay.querySelector('img').src = eggStages[selectedEgg][currentStage - 1];
  selectedEggDisplay.querySelector('img').classList.add('shake');
  
  // Remove shake class after animation
  setTimeout(() => {
    selectedEggDisplay.querySelector('img').classList.remove('shake');
  }, 500);
  
  // If we've reached the final stage
  if (currentStage >= eggStages[selectedEgg].length) {
    clearInterval(crackingInterval);
    
    // Show cracked egg
    selectedEggDisplay.querySelector('img').src = eggStages[selectedEgg][currentStage - 1];
    
    // Determine win/lose
    setTimeout(revealPrize, 1000);
  }
}

// Reveal Prize
function revealPrize() {
  const isWin = Math.random() < winChances[selectedEgg];
  
  if (isWin) {
    // Player wins
    const multipliers = prizeMultipliers[selectedEgg];
    const multiplier = multipliers[Math.floor(Math.random() * multipliers.length)];
    const prize = eggPrices[selectedEgg] * multiplier;
    
    rewardBox.textContent = `YOU WIN ${prize} Kč! (${multiplier}x)`;
    rewardBox.className = 'reward-box win';
    
    // In a real game, you would add to player balance here
  } else {
    // Player loses
    rewardBox.textContent = 'NO PRIZE THIS TIME!';
    rewardBox.className = 'reward-box lose';
  }
  
  // Reset for next play
  setTimeout(() => {
    eggPurchased = false;
    crackBtn.disabled = true;
    buyBtn.disabled = false;
  }, 3000);
}

// Modal Functions
function showRules() {
  rulesModal.style.display = 'block';
}

function closeModal() {
  rulesModal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
  if (event.target == rulesModal) {
    closeModal();
  }
}

// Initialize Buy Button
buyBtn.addEventListener('click', buyEgg);