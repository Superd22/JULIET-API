<?php namespace JULIET\api\Ships\models;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class ShipVariant {
    public $id;
    public $ship_id;
    public $ship_type_id;
    public $name;
    
    public function __construct($ship) {
        if($ship !== null && $ship['id'] !== null) {

            if( !((integer) $ship['ship_id'] > 0) && !((integer) $ship['ship_type_id'] > 0) )
            throw new \Exception("ShipVariant *MUST* HAVE A PARENT");
            $this->id      = (integer) $ship['id'];
            $this->ship_id = (integer) $ship['ship_id'];
            $this->ship_type_id = (integer) $ship['ship_type_id'];
            $this->name    = $ship['name'];
        }
        elseif($ship !== null && is_numeric($ship) && (integer) $ship > 0) {
            $this->id      = (integer) $ship;
        }
        else throw new \Exception("ShipVariant INVALID TARGET");
    }
}