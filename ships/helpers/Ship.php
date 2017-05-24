<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Rights\Main as Rights;

class Ship {

    protected $_ship_id;

    public function __construct($ship_id) {
        $ship_id = (integer) $ship_id;
        if( !($ship_id > 0) ) throw new \Exception("NO TARGET FOR SHIP CREATION");
        $this->_ship_id = (integer) $ship_id;
    }

    public function get_parents_for_heritage() {
        $mysqli = db::get_mysqli();

        $return = [];

        // We're a a ship so we get our type.
        $sql = "SELECT type_id FROM star_ships WHERE id='{$this->_ship_id}' LIMIT 1";
        $query = $mysqli->query($sql);

        $parent = $query->fetch_assoc();

        if((integer) $parent['type_id'] > 0)
        $return[] = (integer) $parent['type_id'];

        return $return;
    }

    public function get_herited_tags() {
        // We want to get all the tags from our model
        $return[] = [];
        foreach($this->get_parents_for_heritage() as $herit) {
            $type = new \JULIET\api\Ships\models\ShipType($herit);
            $tags = \JULIET\api\Tags\helper\Tag::get_tags_from_ship_model( $type );

            foreach($tags as $tag) {
                if(!$tag->has_heritage())
                $tag->set_heritage($herit, "shipModel");
                
                // Prevent doubles
                $return[$tag->id] = $tag;
            }
        }

        return array_values($return);
    }

    public function rename($new_name) {
        $mysqli = db::get_mysqli();
        $f = $mysqli->real_escape_string($new_name);        
        
        $rename = $mysqli->query("UPDATE star_tags SET name='{$f}' WHERE id='{$this->_ship_id}' LIMIT 1");

        return !$mysqli->error;
    }

    public function delete() {
        $mysqli = db::get_mysqli();
        $query = $mysqli->query("DELETE FROM star_ships WHERE id = {$this->_ship_id } LIMIT 1");
        return true;
    }

    public function get_owner() {
        $mysqli = db::get_mysqli();

        $ad = $mysqli->query("SELECT owner FROM star_ships WHERE id={$this->_ship_id}");
        $dada = $ad->fetch_assoc();

        return $dada['owner'];
    }

    public function update(ShipModel $ship) {
        $mysqli = db::get_mysqli();
        $ad = $mysqli->query("UPDATE star_ships SET
        name='{$mysqli->real_escape_string($ship->name)}',
        owner={$ship->owner},
        type_id={$ship->type_id} 
        WHERE id={$this->_ship_id}");

        if($mysqli->error) throw new \Exception($mysqli->error);
        else return $ship;
    }

    public function get_info() {
        $mysqli = db::get_mysqli();
        $sql = "SELECT * FROM star_ships WHERE id={$this->_ship_id} LIMIT 1";
        $query = $mysqli->query($sql);
        $ship = $query->fetch_assoc();

        return new \JULIET\api\Ships\models\Ship($ship);
    }
}
?>