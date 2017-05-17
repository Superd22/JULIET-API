<?php namespace JULIET\api\Ships\models;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Ship {
    public $id;
    public $type_id;
    public $name;
    public $owner;
    
    public function __construct($ship) {
        if($ship !== null && $ship['id'] !== null) {
            $this->id      = (integer) $ship['id'];
            $this->name    = $ship['name'];
            $this->type_id = (integer) $ship['type_id'];
            $this->owner   = (integer) $ship['owner'];
        }
    }
}