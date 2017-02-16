<?php
namespace OCA\PersonalFinances\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\PersonalFinances\Db\Account;
use OCA\PersonalFinances\Db\AccountMapper;


class AccountService {

    private $mapper;

    public function __construct(AccountMapper $mapper){
        $this->mapper = $mapper;
    }

    public function findAll($userId) {
        return $this->mapper->findAll($userId);
    }

    private function handleException ($e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new NotFoundException($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function find($id, $userId) {
        try {
            return $this->mapper->find($id, $userId);

        // in order to be able to plug in different storage backends like files
        // for instance it is a good idea to turn storage related exceptions
        // into service related exceptions so controllers and service users
        // have to deal with only one type of exception
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function create($name, $type, $initial, $userId) {
        $account = new Account();
        $account->setName($name);
        $account->setType($type);
        $account->setInitial($initial);
        $account->setUserId($userId);
        return $this->mapper->insert($account);
    }

    public function update($id, $name, $type, $intial, $userId) {
        try {
            $account = $this->mapper->find($id, $userId);
            $account->setName($title);
            $account->setType($type);
            $account->setInitial($initial);
            return $this->mapper->update($account);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function delete($id, $userId) {
        try {
            $account = $this->mapper->find($id, $userId);
            $this->mapper->delete($account);
            return $account;
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

}