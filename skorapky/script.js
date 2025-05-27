// Soubor: script.js

let rewards = [];
let gameReady = false;
let shellSelected = false;

// Funkce fetchUserStats() byla odebrána, protože uživatel ji nechce.
// Nebudou se tedy zobrazovat ani aktualizovat uživatelské statistiky.


window.onload = () => {
    // Načítání odměn ze serveru
    // Cesta k get_rewards.php je správná
    fetch('get_rewards.php')
        .then(res => {
            if (!res.ok) {
                // Pokud HTTP odpověď není OK, pokusíme se přečíst text odpovědi pro detailnější chybu
                return res.text().then(text => {
                    throw new Error(`Síťová chyba: ${res.status} - ${text}`);
                });
            }
            // Zkusíme parsovat odpověď jako JSON
            return res.json();
        })
        .then(data => {
            // Zkontrolujeme, zda JSON obsahuje chybovou zprávu z PHP
            if (data.error) {
                throw new Error(data.error);
            }
            // Zkontrolujeme, zda data jsou pole (očekávaný formát pro odměny)
            if (!Array.isArray(data)) {
                throw new Error('Neočekávaný formát dat odměn. Očekáváno pole.');
            }
            rewards = data; // Uložíme načtené odměny do globální proměnné
            console.log('Načtené odměny:', rewards);
            document.getElementById('startBtn').disabled = false; // Aktivujeme tlačítko "Začít hru"

            // fetchUserStats(); // Toto volání je Nyní odebráno
        })
        .catch(error => {
            // Zachytíme jakoukoli chybu během procesu fetch/parse
            console.error('Chyba při načítání odměn:', error);
            document.getElementById('reward').innerText = `Chyba: ${error.message || 'Neznámá chyba při načítání odměn.'}`;
            document.getElementById('startBtn').disabled = true; // Deaktivujeme tlačítko
        });
};

document.getElementById('startBtn').onclick = () => {
    // Zkontrolujeme, zda jsou odměny načteny a dostupné
    if (rewards.length === 0) {
        alert('Odměny nejsou dostupné. Zkuste to prosím později.');
        return;
    }
    gameReady = true; // Hra je připravena k výběru
    shellSelected = false; // Žádná skořápka zatím nebyla vybrána
    document.getElementById('reward').innerText = ''; // Vyčistíme zprávu o odměně
    // Aktivujeme všechna tlačítka skořápek
    document.querySelectorAll('.shell').forEach(btn => btn.disabled = false);
    document.getElementById('startBtn').disabled = true; // Deaktivujeme tlačítko "Začít hru"
};

function chooseShell(e) {
    // Zastavíme funkci, pokud hra není připravena nebo už byla vybrána skořápka
    if (!gameReady || shellSelected) return;

    shellSelected = true; // Označíme, že skořápka byla vybrána
    gameReady = false; // Hra už není připravena k dalšímu výběru v tomto kole

    // Náhodně vybereme jednu odměnu z načtených odměn
    const reward = rewards[Math.floor(Math.random() * rewards.length)];

    if (!reward) {
        console.error('Chyba při výběru výhry: Objekt odměny je null/undefined.');
        document.getElementById('reward').innerText = 'Výhra nenalezena.';
        document.getElementById('startBtn').disabled = false; // Umožnit restart hry
        return;
    }

    // Zobrazíme výhru uživateli na stránce
    document.getElementById('reward').innerText = `Vyhrál jsi: ${reward.name}`;
    console.log(`Vyhrál jsi: ${reward.name}`);

    // Zakážeme tlačítka skořápek (aby nešlo vybrat znovu) a aktivujeme tlačítko "Začít hru" pro další kolo
    document.querySelectorAll('.shell').forEach(btn => btn.disabled = true);
    document.getElementById('startBtn').disabled = false;

    // Odeslání ID vybrané odměny na server pro validaci a uložení
    fetch('validate.php', { // Cesta k validate.php
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            reward_id: reward.id // Posíláme ID vybrané odměny
        })
    })
    .then(res => {
        if (!res.ok) {
            // Pokud HTTP odpověď není OK, přečteme text odpovědi pro detailnější chybu
            return res.text().then(text => {
                throw new Error(`Chyba serveru při ukládání výhry: ${res.status} - ${text}`);
            });
        }
        return res.json(); // Zkusíme parsovat odpověď jako JSON
    })
    .then(data => {
        // Zkontrolujeme, zda server vrátil úspěch nebo chybu
        if (!data.success) {
            console.error('Nepodařilo se uložit výhru:', data.error || 'Neznámá chyba');
            document.getElementById('reward').innerText += ` (Chyba uložení: ${data.error || 'Neznámá chyba'})`;
            // Pokud uživatel není přihlášen, můžete ho informovat explicitně
            if (data.error && data.error.includes('Uživatel není přihlášen')) {
                alert('Pro uplatnění odměny se musíte přihlásit!');
            }
        } else {
            console.log('Výhra úspěšně zapsána do databáze.');
            // fetchUserStats(); // Toto volání je Nyní odebráno
        }
    })
    .catch(error => {
        // Zachytíme jakoukoli chybu během procesu fetch/parse
        console.error('Chyba při ukládání výhry:', error);
        document.getElementById('reward').innerText += ` (Chyba komunikace: ${error.message || 'Neznámá chyba'})`;
    });
}

// Přiřazení obsluhy kliknutí ke všem tlačítkům s třídou 'shell'
document.querySelectorAll('.shell').forEach(btn => {
    btn.addEventListener('click', chooseShell);
});