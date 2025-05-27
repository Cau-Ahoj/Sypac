<?php
class DB {
    private $pdo;

    // DATABASE CONNECTION
    public function __construct() {
        $host = 'localhost';
        $dbname = 'sypac_casinoDB';
        $user = 'sypac';
        $pass = '8QGSkbDMqhqUpSPJ';

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

    // SELECT LIST
    public function getAll($sql, $values = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // SELECT ONE   
    public function getOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // LOG
    public function log($user_id, $action, $detail) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
        $sql = "INSERT INTO logs (user_id, action, detail, timestamp, ip_address, user_agent)
                VALUES (?, ?, ?, NOW(), ?, ?)";
        $this->run($sql, [$user_id, $action, $detail, $ip, $agent]);
    }
    
    

}
?>
