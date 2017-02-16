<?php
namespace OCA\PersonalFinances\Db;

use OCP\IDb;
use OCP\AppFramework\Db\Mapper;

class BankMapper extends Mapper {

    public function __construct(IDb $db) {
        parent::__construct($db, 'personalfinances_banks', '\OCA\PersonalFinances\Db\Bank');
    }

    public function find($id, $userId) {
        $sql = 'SELECT * FROM *PREFIX*personalfinances_banks WHERE id = ? AND user_id = ?';
        return $this->findEntity($sql, [$id, $userId]);
    }

    public function findAll($userId) {
        $sql = 'SELECT * FROM *PREFIX*personalfinances_banks WHERE user_id = ?';
        return $this->findEntities($sql, [$userId]);
    }

    public function Exists($bankname, $userId) {
        $sql = 'SELECT * FROM *PREFIX*personalfinances_banks WHERE name = ? AND user_id = ?';
        try {
            $this->findEntity($sql, [$bankname, $userId]);
        } catch (DoesNotExistException $e) {
            return false;
        }
        return true;
    }

}