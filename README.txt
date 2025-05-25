🗂️ Základní pravidla pro každou stránku v projektu

Tento dokument popisuje obecná pravidla pro strukturu a psaní kódu v rámci projektu. Cílem je, aby byl projekt jednotný, přehledný a snadno udržovatelný pro celý tým.

📁 Struktura složek a souborů

    Každá stránka má vlastní složku
    Název složky musí být ve formátu kebab-case (malá písmena, pomlčky).
    Např. user-profile, credit-history, my-page.

    Uvnitř každé složky je hlavní soubor:
    index.php (povinný)

    Další soubory (PHP, JS, CSS) se přidávají dle potřeby
    Pokud stránka potřebuje vlastní logiku nebo styly, vytvoří se další soubory ve stejné složce (např. script.js, style.css, helper.php, apod.).



🎨 Globální soubory (načítat na každé stránce)

global.css

    Obsahuje základní styly pro celý web (např. tlačítka, formuláře, barvy, fonty, velikosti).

    Neměň globální styly – pokud potřebuješ něco navíc, přidej vlastní CSS soubor v rámci své stránky a načti ho až po global.css.


global.js

    Obsahuje základní funkce a skripty, které se mohou hodit na různých stránkách (např. skrývání hlášek, přepínače, preloader).

    Vždy se načítá jako první JS skript.

    Vlastní JavaScript soubory přidávej až po načtení global.js.



🧩 Vkládání vlastních JS a CSS souborů

    Pokud stránka potřebuje vlastní JS nebo CSS, vytvoř je ve stejné složce jako index.php.

    Soubor pojmenuj srozumitelně, např. user-profile.js, user-profile.css.

    Vkládej je do index.php až po globálních souborech, např.:

💻 Kódový styl

    Složky a URL: kebab-case

    Proměnné a funkce: snake_case

    Nepoužívej: camelCase, názvy složek s velkými písmeny


🗃️ Práce s databází

    Databáze se připojuje výhradně přes database.php

    Používej předpřipravenou třídu DB s metodami:

        get(), run(), log(), atd.

    Nikdy nevytvářej připojení k databázi ručně v jednotlivých souborech.

    Pokud potřebuješ upravit databázi nebo přidat nové tabulky, vždy se domluv se mnou – upravím databázi nebo příslušný PHP soubor podle potřeby.

💬 Komentáře v kódu

    Komentáře piš česky

    Komentuj pouze složitější části – u běžného kódu nejsou komentáře nutné.

    Komentář by měl odpovídat na otázku: „K čemu to je?“



✅ Shrnutí

Každá stránka | složka kebab-case + index.php
Globální soubory | global.css + global.js vždy jako první
Vlastní JS a CSS | ve složce stránky, vkládat až po globálních souborech
Styl kódu | složky/URL = kebab-case, proměnné/funkce = snake_case
Databáze | přes database.php + třídu DB (úpravy jen po domluvě)
Komentáře | piš česky, nemusis vsude, jen tam kde je to potřeba