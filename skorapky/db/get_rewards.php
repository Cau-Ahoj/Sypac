<?php
header('Content-Type: application/json');
require_once 'database.php';

$db = new DB();

$rewards = $db->getRewards();

echo json_encode($rewards);
