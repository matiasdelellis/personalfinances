<?php
namespace OCA\PersonalFinances\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Account extends Entity implements JsonSerializable {

    protected $name;
    protected $type;
    protected $initial;
    protected $bankId;
    protected $userId;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'initial' => $this->initial,
            'bankid' => $this->bankId
        ];
    }
}