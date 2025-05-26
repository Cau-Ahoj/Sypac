<?php
class DB {
    private $pdo;

    public function __construct() {
        $host = 'localhost';
        $dbname = 'kubela_casino';
        $user = 'kubela';
        $pass = 'SeObU1by3iyJ9v8b';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database ERROR: " . $e->getMessage());
        }
    }

    public function getRewards() {
        $stmt = $this->pdo->query("SELECT name FROM rewards");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
