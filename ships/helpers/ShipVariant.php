<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Rights\Main as Rights;

class ShipVariant {

    private $id;

    public function __constructor($shipTypeId) {
        if( !($shipTypeId > 0) ) throw new \Exception("SHIPVARIANT_ID_TARGET_ERROR");
        $this->id = (integer) $shipTypeId;
    }

    public function get_parents_for_heritage($variant_of_model = false) {
        $mysqli = db::get_mysqli();

        $return = [];

        if($variant_of_model) {
            // we wanna get our papa model
            $sql = "SELECT ship_type_id as papa FROM star_ships_variant WHERE id='{$this->id}' LIMIT 1";
        }
        else {
            // we wanna get our papa ship
            $sql = "SELECT ship_id as papa FROM star_ships_variant WHERE id='{$this->id}' LIMIT 1";
        }

        $query = $mysqli->query($sql);
        $parent = $query->fetch_assoc();

        if((integer) $parent['papa'] > 0)
        $return[] = (integer) $parent['papa'];

        return $return;
    }

    public function is_variant_of_model() {
        $mysqli = db::get_mysqli();

        $sql = "SELECT * FROM star_ships_variant WHERE id ='{$this->id}' LIMIT 1";
        $query = $mysqli->query($sql);
        $variant = $query->fetch_assoc();

        if($variant['ship_id']) return false;
        elseif($variant['ship_type_id']) return true;

        throw new \Exception("TEMPLATE ID: "+$this->id+" HAS INCORECT DB STRUCT");
    }

    public function get_herited_tags() {
        $variant_of_model = $this->is_variant_of_model();

        $herit = $this->get_parents_for_heritage($variant_of_model)[0];
        if(!$herit) return null;

        if($variant_of_model) $tags = JULIET\api\Tags\helper\Tags::get_tags_from_ship_model( new JULIET\api\Ships\models\ShipType($herit) );
        else $tags = JULIET\api\Tags\helper\Tags::get_tags_from_ship( new JULIET\api\Ships\models\Ship($herit) );

        $return = [];
        foreach($tags as $tag) {
            $tag->set_heritage($tag->id, $variant_of_model ? "shipModel" : "ship");
            $return[] = $tag;
        }

        return $return;
    }
}