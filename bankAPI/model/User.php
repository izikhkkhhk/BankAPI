<?php
class User {
    static function login(string $login, string $password, mysqli $db) : int {
        $sql = "SELECT id, passwordHash FROM user WHERE email = ?";

        $query = $db->prepare($sql);

        $query->bind_param('s', $login);

        $query->execute();

        $result = $query->get_result();

        if($result->num_rows == 0) {
    
            throw new Exception('Invalid login or password');
        } else {
            $user = $result->fetch_assoc();
            $id = $user['id'];
            $hash = $user['passwordHash'];
    
            if(password_verify($password, $hash)) {
        
        
                return $id;
            } else {
        
                throw new Exception('Invalid login or password');
            }
        }
    }
}
?>