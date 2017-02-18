<?php
namespace OCA\PersonalFinances\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Transaction extends Entity implements JsonSerializable {

    protected $date;
    protected $amount;
    protected $account;
    protected $dstAccount;
    protected $paymode;
    protected $flags;
    protected $category;
    protected $info;
    protected $userId;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'amount' => $this->amount,
            'accountid' => $this->account,
            'dstaccount' => $this->dstAccount,
            'paymode' => $this->paymode,
            'flags' => $this->flags,
            'category' => $this->category,
            'info' => $this->info
        ];
    }
}