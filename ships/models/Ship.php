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
    public $type;
    public $templates;
    
    public function __construct($ship) {
        if($ship !== null && $ship['id'] !== null) {
            $this->id      = (integer) $ship['id'];
            $this->name    = $ship['name'];
            $this->type_id = (integer) $ship['type_id'];
            $this->owner   = (integer) $ship['owner'];
        }
        elseif($ship !== null && is_numeric($ship) && (integer) $ship > 0) {
            $this->id      = (integer) $ship;
        }
    }

    public function set_type(ShipType $type) {
        $this->type = $type;
    }

    public function set_variants($variants) {
        $this->templates = $variants;
    }

    public function fetch_type() {
        $type = new \JULIET\api\Ships\helpers\ShipType($this->type_id);
        $this->set_type($type->get_info());
    }

    public function fetch_variants() {
        $this->set_variants(\JULIET\api\Ships\helpers\ShipVariant::get_variants_of_ship($this->id));
    }
}