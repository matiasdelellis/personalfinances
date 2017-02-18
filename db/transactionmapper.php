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
        $sql = 'SELECT * FROM *PREFIX*personalfinances_transactions WHERE account = ? user_id = ?';
        return $this->findEntities($sql, [$accountId, $userId]);
    }

}