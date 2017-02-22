<?php
namespace OCA\PersonalFinances\Db;

use OCP\IDb;
use OCP\AppFramework\Db\Mapper;

class TransactionMapper extends Mapper {

    public function __construct(IDb $db) {
        parent::__construct($db, 'personalfinances_transactions', '\OCA\PersonalFinances\Db\Transaction');
    }

    public function find($id, $userId) {
        $sql = 'SELECT * FROM *PREFIX*personalfinances_transactions WHERE id = ? AND user_id = ?';
        return $this->findEntity($sql, [$id, $userId]);
    }

    public function findAll($userId) {
        $sql = 'SELECT * FROM *PREFIX*personalfinances_transactions WHERE user_id = ?';
        return $this->findEntities($sql, [$userId]);
    }

    public function findAllAccount($accountId, $userId) {
        $sql = 'SELECT * FROM *PREFIX*personalfinances_transactions WHERE account = ? AND user_id = ?';
        return $this->findEntities($sql, [$accountId, $userId]);
    }

    public function balanceAccount($accountId, $userId) {
        $sql = 'SELECT SUM(amount) AS balance FROM *PREFIX*personalfinances_transactions WHERE account = ? AND user_id = ?';
        $sql = $this->db->prepare($sql);
        $sql->bindParam(1, $accountId, \PDO::PARAM_INT);
        $sql->bindParam(2, $userId, \PDO::PARAM_STR);
        $sql->execute();
        $row = $sql->fetch();
        $sql->closeCursor();

        $balance = $row['balance'];

        return $balance;
    }

}