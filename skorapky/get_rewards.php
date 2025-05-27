<?php
// Soubor: get_rewards.php

// Vždy nastavte Content-Type hlavičku na application/json pro JSON odpovědi
header('Content-Type: application/json');

// Potlačení zobrazení chyb na výstupu pro produkční prostředí. Chyby by se měly logovat.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL); // Nastavte pro logování všech chyb

// Zahrňte soubor s třídou DB
require_once 'database.php'; // Cesta k database.php

$response = []; // Inicializujeme pole pro JSON odpověď

try {
    $db = new DB(); // Vytvoření instance databáze

    // Získání všech odměn z tabulky 'rewards'
    $rewards = $db->getAll("SELECT id, name, type, amount FROM rewards");

    // Zkontrolujte, zda je výsledek pole, a nastavte ho jako odpověď
    if (is_array($rewards)) {
        $response = $rewards;
    } else {
        // Pokud getAll nevrátí pole, logujte chybu a vraťte prázdné pole
        error_log("Získání odměn nevrátilo pole v get_rewards.php. Obdrženo: " . print_r($rewards, true));
        $response = [];
    }

} catch (Exception $e) {
    // Zachycení jakékoli chyby při získávání dat z databáze
    error_log("Chyba v get_rewards.php: " . $e->getMessage());
    http_response_code(500); // Nastavte HTTP status kód na 500 Internal Server Error
    $response = ['error' => 'Chyba serveru při načítání odměn.'];
}

// Vypište JSON data. JSON_UNESCAPED_UNICODE zajistí správné kódování českých znaků.
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Ukončete skript, aby se nic dalšího nevypisovalo
exit;
?>