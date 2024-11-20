<?php
namespace BankAPI;
use mysqli;

class Account {

    private $accountNo;
    private $amount;
    private $name;


    public function __construct(int $accountNo, int $amount, string $name) {
        $this->accountNo = $accountNo;
        $this->amount = $amount;
        $this->name = $name;
    }

    public static function getAccountNo(int $userId, mysqli $db) : int {
    
        $sql = "SELECT accountNo FROM account WHERE user_id = ? LIMIT 1";
    
        $query = $db->prepare($sql);
    
        $query->bind_param('i', $userId);
    
        $query->execute();
    
        $result = $query->get_result();
    
    
        $account = $result->fetch_assoc();
    
        return $account['accountNo'];
    }




    public static function getAccount(int $accountNo, mysqli $db) : Account {
    
        $result = $db->query("SELECT * FROM account WHERE accountNo = $accountNo");
    
        $account = $result->fetch_assoc();
        $account = new Account($account['accountNo'], $account['amount'], $account['name']);
        return $account;
    }

    public function getArray() : array {
        $array = [
            'accountNo' => $this->accountNo,
            'amount' => $this->amount,
            'name' => $this->name
        ];
        return $array;
    }
}
?>