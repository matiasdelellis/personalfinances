<?php
namespace OCA\PersonalFinances\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Bank extends Entity implements JsonSerializable {

    protected $name;
    protected $userId;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}