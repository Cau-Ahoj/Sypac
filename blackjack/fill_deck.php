<?php
require_once 'db/database.php';
$db = new DB();

// Přidej sloupec `original`, pokud neexistuje (bez chyby)
$db->run("ALTER TABLE blackjack ADD COLUMN original INT DEFAULT 0");

$db->run("DELETE FROM blackjack");

$suits = ['hearts', 'diamonds', 'clubs', 'spades'];
$values = [
    1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6',
    7 => '7', 8 => '8', 9 => '9', 10 => '10',
    11 => 'J', 12 => 'Q', 13 => 'K', 14 => 'A'
];

foreach ($suits as $suit) {
    foreach ($values as $v => $name) {
        $val = ($v > 10 && $v < 14) ? 10 : ($v == 14 ? 11 : $v);
        $picture = strtolower($name . '_of_' . $suit) . '.png';
        $db->run(
            "INSERT INTO blackjack (value, suit, picture, used, original) VALUES (?, ?, ?, 0, ?)",
            [$val, $suit, $picture, $v]
        );
    }
}

echo "Deck filled!";
?>