<?php
class DB {
    private $pdo;

    // DATABASE CONNECTION
    public function __construct() {
        $host = 'localhost';
        $dbname = 'varga_casino_db';
        $user = 'varga';
        $pass = '5QJDkbddFKtahDdy';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database ERROR: " . $e->getMessage());
        }
    }

    // INSERT, UPDATE, DELETE
    public function run($sql, $values = []) {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    // SELECT
    public function get($sql, $values = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // LOG
    public function log($user_id, $action, $detail) {
        $sql = "INSERT INTO logs VALUES (NULL, ?, ?, ?, NOW())";
        $this->run($sql, [$user_id, $action, $detail]);
    }
}
?>
