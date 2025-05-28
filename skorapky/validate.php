<?php
session_start();
header('Content-Type: application/json');

require_once 'database.php';

$response = ['success' => false];

try {
    $db = new DB();

    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Neplatná JSON data: " . json_last_error_msg());
    }

    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
        http_response_code(401);
        throw new Exception('Uživatel není přihlášen.');
    }

    $bet_amount = $data['bet_amount'] ?? 0;
    $is_win = isset($data['is_win']) && $data['is_win'] ? 1 : 0;
    $won_amount = $data['won_amount'] ?? 0;
    $game_type = $data['game_type'] ?? 'normal'; // 'normal' nebo 'double'

    if (!in_array($game_type, ['normal', 'double'])) {
        throw new Exception("Neplatný typ hry.");
    }

    if ($game_type === 'normal') {
        // Normální hra: odečti sázku
        if ($bet_amount <= 0) {
            throw new Exception("Neplatná sázka.");
        }

        $result = $db->run("UPDATE users SET money = money - ? WHERE id = ? AND money >= ?", [
            $bet_amount, $user_id, $bet_amount
        ]);
        if (!$result) {
            throw new Exception("Nedostatek peněz nebo uživatel neexistuje.");
        }
        $db->log($user_id, 'game_bet', "Normální sázka: -{$bet_amount}");

        if ($is_win) {
            $result = $db->run("UPDATE users SET money = money + ? WHERE id = ?", [
                $won_amount, $user_id
            ]);
            if (!$result) {
                throw new Exception("Nepodařilo se přičíst výhru.");
            }
            $db->log($user_id, 'game_win', "Normální výhra: +{$won_amount}");
        } else {
            $db->log($user_id, 'game_loss', "Normální prohra: -{$bet_amount}");
        }
    }

    elseif ($game_type === 'double') {
        if (!is_numeric($won_amount)) {
            throw new Exception("Neplatná částka výhry.");
        }
    
        if ($is_win) {
            $result = $db->run("UPDATE users SET money = money + ? WHERE id = ?", [
                $won_amount, $user_id
            ]);
            if (!$result) {
                throw new Exception("Nepodařilo se přičíst double výhru.");
            }
            $db->log($user_id, 'double_win', "Double výhra: +{$won_amount}");
        } else {
            $amountToRemove = abs($won_amount);
            $result = $db->run("UPDATE users SET money = money - ? WHERE id = ? AND money >= ?", [
                $amountToRemove, $user_id, $amountToRemove
            ]);
            if (!$result) {
                throw new Exception("Nepodařilo se odečíst prohranou výhru v Double.");
            }
            $db->log($user_id, 'double_loss', "Double prohra: -{$amountToRemove}");
        }
    }
    

    // Zaznamenej do historie
    $sql = "INSERT INTO game_history (id_user, bet_amount, is_win, won_amount, played_at, game_type)
            VALUES (?, ?, ?, ?, NOW(), ?)";
    $db->run($sql, [
        $user_id,
        $bet_amount,
        $is_win,
        $won_amount,
        $game_type
    ]);

    // Nový zůstatek
    $new_balance = $db->getOne("SELECT money FROM users WHERE id = ?", [$user_id]);

    $response['success'] = true;
    $response['new_balance'] = (int)$new_balance['money'];

} catch (Exception $e) {
    http_response_code(500);
    error_log("Chyba ve validate.php: " . $e->getMessage());
    $response['error'] = $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
