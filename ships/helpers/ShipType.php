<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Rights\Main as Rights;

class ShipType {
    
    private $id;

    public function __construct($shipTypeId) {
        if( !($shipTypeId > 0) ) throw new \Exception("SHIPTYPE_ID_TARGET_ERROR");
        $this->id = (integer) $shipTypeId;
    }

    public function get_parents_for_heritage() {
        $mysqli = db::get_mysqli();
        $return = [];

        // We're a ship type, so we need to check if we have to get something from parents
        $sql = "SELECT parent FROM star_ship WHERE id='{$this->id}' LIMIT 1";
        $query = $mysqli->query($sql);

        $parent = $query->fetch_assoc();

        if((integer) $parent['parent'] > 0)
        $return[] = (integer) $parent['parent'];
        return $return;
    }

    public function get_herited_tags() {
        $herit = (integer) $this->get_parents_for_heritage()[0];
        if(!$herit) return null;

        $tags = \JULIET\api\Tags\helper\Tag::get_tags_from_ship_model( new \JULIET\api\Ships\models\ShipType($herit) );
        $return = [];
        foreach($tags as $tag) {
            if(!$tag->has_heritage()) $tag->set_heritage((integer) $herit, "shipModel");
            $return[] = $tag;
        }

        return $return;
    }

    public static function add_ship_type(\JULIET\api\Ships\models\ShipType $ship) {
        if($ship->id < 0) throw new \Exception("SHIP_ID_ERROR");
        if($ship->id != 0) return self::update_ship_type($ship);

        $mysqli = db::get_mysqli();
        $query = $mysqli->query("INSERT INTO star_ship
			(name,type, ico, parent)
					VALUES 
                    (
                    '".$mysqli->real_escape_string($ship->name)."',
                    '".$mysqli->real_escape_string($ship->type)."',
                    '".$mysqli->real_escape_string($ship->ico)."',
                    '".(integer) $ship->parent."'
                    )
            ");
        
        if($mysqli->error) throw new \Exception($mysqli->error);
        else {
            $ship->id = $mysqli->insert_id;
            return $ship;
        }
    }
    
    public static function delete_ship_type($ship_type_id) {
        $ship_type_id = (integer) $ship_type_id;
        $mysqli = db::get_mysqli();
        $ad = $mysqli->query("DELETE FROM star_ship WHERE id='{$ship_type_id}' LIMIT 1");

        if($mysqli->error) throw new \Exception($mysqli->error);
        else {
            return true;
        }
    }

    public static function update_ship_type(\JULIET\api\Ships\models\ShipType $ship) {
        if($ship->id == 0) return self::add_ship_type($ship);
        $mysqli = db::get_mysqli();

        $ad = $mysqli->query("UPDATE star_ship SET
        name='{$mysqli->real_escape_string($ship->name)}',
        type='{$mysqli->real_escape_string($ship->type)}',
        ico='{$mysqli->real_escape_string($ship->ico)}',
        parent={$ship->parent} 
        WHERE id={$ship->id} LIMIT 1");
        
        if($mysqli->error) throw new \Exception($mysqli->error);
        else return $ship;
    }

    public function get_info() {
        $mysqli = db::get_mysqli();
        $sql = "SELECT * FROM star_ship WHERE id={$this->id} LIMIT 1";
        $query = $mysqli->query($sql);
        $ship = $query->fetch_assoc();

        return new \JULIET\api\Ships\models\ShipType($ship);
    }
}