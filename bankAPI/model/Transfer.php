<?php
class Transfer {
    public static function new(int $source, int $target, int $amount, mysqli $db) : void {
        $db->begin_transaction();
        try {

            $sql = "UPDATE account SET amount = amount - ? WHERE accountNo = ?";

            $query = $db->prepare($sql);

            $query->bind_param('ii', $amount, $source);

            $query->execute();

            $sql = "UPDATE account SET amount = amount + ? WHERE accountNo = ?";

            $query = $db->prepare($sql);

            $query->bind_param('ii', $amount, $target);

            $query->execute();

            $sql = "INSERT INTO transfer (source, target, amount) VALUES (?, ?, ?)";

            $query = $db->prepare($sql);

            $query->bind_param('iii', $source, $target, $amount);

            $query->execute();

            $db->commit();
        } catch (mysqli_sql_exception $e) {

            $db->rollback();

            throw new Exception('Transfer failed');
        }
        
    }
}
?>