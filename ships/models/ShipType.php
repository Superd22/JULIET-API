<?php namespace JULIET\api\Ships\models;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class ShipType {
    public $id;
    public $name;
    public $type;
    public $ico;
    public $parent;
    
    public function __construct($ship) {
        if($ship !== null && $ship['id'] !== null) {
            $this->id     = (integer) $ship['id'];
            $this->name   = $ship['name'];
            $this->type   = $ship['type'];
            $this->ico    = $ship['ico'];
            $this->parent = (integer) $ship['parent'];
        }
    }
}