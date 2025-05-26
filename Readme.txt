-Všechny soubory jsou v jedné složce.
-Funguje je to přes funkci v javě (proto to trvá okolo 3-10 min než se odešle email).

Stavový Diagram

[forgot.php] 
    ↓ uživatel zadá e-mail
[send-code.php]
    → pokud e-mail neexistuje, vytvoří se nový uživatel
    → vygeneruje se ověřovací kód
    → uloží se do databáze (reset_token + reset_expires)
    → odešle se e-mail přes PHPMailer
    ↓
    ZOBRAZÍ SE FORMULÁŘ
    ↓
    uživatel zadá kód a odešle
[verify-code.php]
    → ověří: existuje uživatel s tímto e-mailem, kódem a platným časem?
        ├── ano: → uloží do session "verified" + email
        │         → přesměrování na [new-password.php]
        └── ne:  → vypíše "Neplatný nebo expirovaný kód"
[new-password.php]
    → formulář pro zadání nového hesla (2x)
    ↓ odeslání
[save-password.php]
    → ověří session
    → zahashuje nové heslo
    → uloží do DB, smaže token
    → potvrdí změnu hesla 

