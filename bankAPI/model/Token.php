<?php
class Token {
    static function new(string $ip, int $userId, mysqli $db) : string {


        $hash = hash('sha256', $ip . $userId . time());

        $sql = "INSERT INTO token (token, ip, user_id) VALUES (?, ?, ?)";
        $query = $db->prepare($sql);

        $query->bind_param('ssi', $hash, $ip, $userId);

        if(!$query->execute())
            throw new Exception('Could not create token');
        else {
    
            return $hash;
        }
    }
    static function check(string $token, string $ip, mysqli $db) : bool {

        $sql = "SELECT * FROM token WHERE token = ? AND ip = ?";

        $query = $db->prepare($sql);

        $query->bind_param('ss', $token, $ip);

        $query->execute();  

        $result = $query->get_result();

        if($result->num_rows == 0) {
    
            return false;
        } else {
    
            return true;
        }
    }
    static function getUserId(string $token, mysqli $db) : int {



        $sql = "SELECT user_id FROM token WHERE token = ? 
                    ORDER BY id DESC LIMIT 1";
        $query = $db->prepare($sql);
        $query->bind_param('s', $token);
        $query->execute();
        $result = $query->get_result();

        if($result->num_rows == 0) {
            throw new Exception('Invalid token');
        } else {
    
            $row = $result->fetch_assoc();
    
            return $row['user_id'];
        }
    }
}
?>