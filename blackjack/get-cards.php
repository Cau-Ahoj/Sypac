<?php
require __DIR__ . '/db/database.php';
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT id, value, suit, picture, used FROM blackjack";
$res = $database->query($sql);

$cards = [];
while ($row = $res->fetch_assoc()) {
    $cards[] = $row;
}

echo json_encode($cards);
?>