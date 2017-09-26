<?php
namespace OCA\PersonalFinances\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\Mapper;

class AccountMapper extends Mapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'personalfinances_accounts', '\OCA\PersonalFinances\Db\Account');
    }

    public function find($id, $userId) {
        $sql = 'SELECT * FROM *PREFIX*personalfinances_accounts WHERE id = ? AND user_id = ?';
        return $this->findEntity($sql, [$id, $userId]);
    }

    public function findAll($userId) {
        $sql = 'SELECT * FROM *PREFIX*personalfinances_accounts WHERE user_id = ?';
        return $this->findEntities($sql, [$userId]);
    }

}