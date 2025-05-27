<?php
class DB {
    private $pdo;

    // DATABASE CONNECTION
    public function __construct() {
        $host = 'localhost';
        $dbname = 'karlovsky_casino';
        $user = 'karlovsky';
        $pass = 'CU5cEQfhFb6yLcNo';

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

    // LOG (pokud chceš)
    public function log($user_id, $action, $detail) {
        $sql = "INSERT INTO logs VALUES (NULL, ?, ?, ?, NOW())";
        $this->run($sql, [$user_id, $action, $detail]);
    }

    // ---- NOVÉ METODY PRO BLACKJACK ----

    // Načte všechny karty, případně jen nepoužité (used=0)
    public function getCards($onlyUnused = true) {
        $sql = "SELECT id, value, suit, picture, used FROM blackjack";
        if ($onlyUnused) {
            $sql .= " WHERE used = 0";
        }
        return $this->get($sql);
    }

    // Označí kartu jako použitou nebo nepoužitou
    public function setCardUsed(int $id, bool $used = true) {
        $sql = "UPDATE blackjack SET used = ? WHERE id = ?";
        return $this->run($sql, [ $used ? 1 : 0, $id ]);
    }

    // Resetuje všechny karty na nepoužité
    public function resetDeck() {
        $sql = "UPDATE blackjack SET used = 0";
        return $this->run($sql);
    }
}
?>