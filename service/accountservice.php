<?php
namespace OCA\PersonalFinances\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCA\PersonalFinances\Db\Account;
use OCA\PersonalFinances\Db\AccountMapper;
use OCA\PersonalFinances\Db\Bank;
use OCA\PersonalFinances\Db\BankMapper;
use OCA\PersonalFinances\Db\Transaction;
use OCA\PersonalFinances\Db\TransactionMapper;


class AccountService {

    /**
     * @var IDBConnection
     */
    private $connection;
    private $accountmapper;
    private $bankmapper;
    private $transactionmapper;

    public function __construct(IDBConnection $connection, AccountMapper $accountmapper, BankMapper $bankmapper, TransactionMapper $transactionmapper) {
        $this->connection = $connection;
        $this->accountmapper = $accountmapper;
        $this->bankmapper = $bankmapper;
        $this->transactionmapper = $transactionmapper;
    }

    public function findAll($userId) {
        $builder = $this->connection->getQueryBuilder();
        $query = $builder->select(['a.id', 'a.initial', 'a.type'])
                         ->selectAlias('a.name', 'account_name')
                         ->selectAlias('b.name', 'bank_name')
                         ->from('personalfinances_accounts', 'a')
                         ->innerJoin('a', 'personalfinances_banks', 'b', $builder->expr()->eq('a.bank_id', 'b.id'))
                         ->where($builder->expr()->eq('a.user_id', $builder->createNamedParameter($userId)))
                         ->orderBy('bank_name', 'ASC');
        $result = $query->execute();

        $data = $result->fetchAll();
        $result->closeCursor();

        return $data;
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
            return $this->accountmapper->find($id, $userId);

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
        return $this->accountmapper->insert($account);
    }

    public function update($id, $name, $type, $intial, $userId) {
        try {
            $account = $this->mapper->find($id, $userId);
            $account->setName($title);
            $account->setType($type);
            $account->setInitial($initial);
            return $this->accountmapper->update($account);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function delete($id, $userId) {
        try {
            $account = $this->accountmapper->find($id, $userId);
            $this->accountmapper->delete($account);
            return $account;
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function balance ($id, $userId) {
        $account = $this->accountmapper->find($id, $userId);
        $intrabalance = $this->transactionmapper->balanceAccount($id, $userId);
        return $account->getInitial() + $intrabalance;
    }
}
