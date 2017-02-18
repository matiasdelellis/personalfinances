<?php
namespace OCA\PersonalFinances\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\PersonalFinances\Db\Transaction;
use OCA\PersonalFinances\Db\TransactionMapper;

class TransactionService {

    private $transactionmapper;

    public function __construct(TransactionMapper $transactionmapper) {
        $this->transactionmapper = $transactionmapper;
    }

    public function findAll($userId) {
        return $this->transactionmapper->findAll($userId);
    }

    public function findAllAccount($account, $userId) {
        return $this->transactionmapper->findAllAccount($account, $userId);
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
            return $this->transactionmapper->find($id, $userId);

        // in order to be able to plug in different storage backends like files
        // for instance it is a good idea to turn storage related exceptions
        // into service related exceptions so controllers and service users
        // have to deal with only one type of exception
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function create($date, $amount, $account, $dst_account, $paymode, $flags, $category, $info, $userId) {
        $transaction = new Transaction();
        $transaction->setDate($date);
        $transaction->setAmount($amount);
        $transaction->setAccount($account);
        $transaction->setDstAccount($dst_account);
        $transaction->setPaymode($paymode);
        $transaction->setFlags($flags);
        $transaction->setCategory($category);
        $transaction->setInfo($info);
        $transaction->setUserId($userId);

        return $this->transactionmapper->insert($transaction);
    }

    public function update($id, $date, $amount, $account, $dst_account, $paymode, $flags, $category, $info, $userId) {
        try {
            $transaction = $this->transactionmapper->find($id, $userId);
            $transaction->setDate($date);
            $transaction->setAmount($amount);
            $transaction->setAccount($account);
            $transaction->setDstAccount($dst_account);
            $transaction->setPaymode($paymode);
            $transaction->setFlags($flags);
            $transaction->setCategory($category);
            $transaction->setInfo($info);
            $transaction->setUserId($userId);
            return $this->transactionmapper->update($transaction);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function delete($id, $userId) {
        try {
            $transaction = $this->transactionmapper->find($id, $userId);
            $this->transactionmapper->delete($transaction);
            return $transaction;
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

}
