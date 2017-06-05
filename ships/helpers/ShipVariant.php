<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Rights\Main as Rights;

class ShipVariant {
    
    private $id;
    
    public function __construct($shipTypeId) {
        if( !($shipTypeId > 0) ) throw new \Exception("SHIPVARIANT_ID_TARGET_ERROR");
        $this->id = (integer) $shipTypeId;
    }
    
    /**
    * Fetches the parent of this ShipVariant
    * (can be either a Ship instance or a ShipModel)
    * @param boolean $variant_of_model if we're a variant of a model or not
    * @return integer id of our parent.
    */
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
    
    /**
    * Register a new ship variant in the db
    *
    * @param \JULIET\api\Ships\models\ShipVariant $variant the variant to register
    * @return new inserted variant on sucess
    * @throws Exception on db error
    */
    public static function add(\JULIET\api\Ships\models\ShipVariant $variant) {
        $mysqli = db::get_mysqli();

        if($variant->ship_id > 0) {
            $sql_target = "ship_id";
            $target_id = $variant->ship_id;
        }
        elseif($variant->ship_type_id > 0) {
            $sql_target = "ship_type_id";
            $target_id = $variant->ship_type_id;
        }
        else throw new \Exception("CAN'T ADD VARIANT : NO PARENT ID");


        $ship_id = $variant->ship_id == 0 ? null : $variant->ship_id;
        $ship_type_id = $variant->ship_type_id == 0 ? null : $variant->ship_type_id;

        $sql = "INSERT INTO star_ships_variant (name, {$sql_target}) VALUES
        ('{$mysqli->real_escape_string($variant->name)}',
        '{$target_id}')";
        
        $add_query = $mysqli->query($sql);
        if($mysqli->error) throw new \Exception("[DBERROR] CAN'T ADD VARIANT : ".$mysqli->error);
        else {
            $variant->id = $mysqli->insert_id;
            return $variant;
        }
    }
    
    /**
    * De-registers the given ship variant in the db
    *
    * @param \JULIET\api\Ships\models\ShipVariant $variant
    * @return true on sucess
    */
    public static function remove(\JULIET\api\Ships\models\ShipVariant $variant) {
        $mysqli = db::get_mysqli();
        $sql = "DELETE FROM star_ships_variant WHERE id='{$variant->id}'";
        $query = $mysqli->query($sql);
        
        if($mysqli->error) throw new \Exception("[DBERROR] CAN'T DELETE VARIANT : ".$mysqli->error);
        else return true;
    }
    
    public static function update(\JULIET\api\Ships\models\ShipVariant $variant) {
        $mysqli = db::get_mysqli();

        if($variant->id == 0) return self::add($variant);

        $sql = "UPDATE FROM star_ships_variant SET
        name = '{$mysqli->real_escape_string($variant->name)}'
        WHERE id='{$variant->id}'";
        $query = $mysqli->query($sql);
        
        if($mysqli->error) throw new \Exception("[DBERROR] CAN'T UPDATE VARIANT : ".$mysqli->error);
        else return $variant;
    }
    
    
    /**
    * Checks if this is a variant of a ship instance or of a ship model
    *
    * @return boolean true if variant of model, false otherwise.
    */
    public function is_variant_of_model() {
        $mysqli = db::get_mysqli();
        
        $sql = "SELECT * FROM star_ships_variant WHERE id ='{$this->id}' LIMIT 1";
        $query = $mysqli->query($sql);
        $variant = $query->fetch_assoc();
        
        if($variant['ship_id']) return false;
        elseif($variant['ship_type_id']) return true;
            
        throw new \Exception("TEMPLATE ID: "+$this->id+" HAS INCORECT DB STRUCT");
    }
    
    /**
    * Fetch the tag herited from our parent(s)
    *
    * @return array of tags
    */
    public function get_herited_tags() {
        $variant_of_model = $this->is_variant_of_model();
        
        $herit = $this->get_parents_for_heritage($variant_of_model)[0];
        if(!$herit) return null;
        
        if($variant_of_model) $tags = \JULIET\api\Tags\helper\Tag::get_tags_from_ship_model( new \JULIET\api\Ships\models\ShipType($herit) );
        else $tags = \JULIET\api\Tags\helper\Tag::get_tags_from_ship( new \JULIET\api\Ships\models\Ship($herit) );
            
        $return = [];
        foreach($tags as $tag) {
            if(!$tag->has_heritage())
            $tag->set_heritage($herit, $variant_of_model ? "shipModel" : "ship");
            $return[] = $tag;
        }
        
        return $return;
    }
    
    public function get_info() {
        $mysqli = db::get_mysqli();
        $sql = "SELECT * FROM star_ships_variant WHERE id={$this->id} LIMIT 1";
        $query = $mysqli->query($sql);
        $ship = $query->fetch_assoc();
        
        return new \JULIET\api\Ships\models\ShipVariant($ship);
    }
    
    /**
    * Helper function to get the variants of a given ship instance
    *
    * @param Integer $ship_id the id to check for
    * @return array of ShipVariant
    */
    public static function get_variants_of_ship($ship_id) {
        $mysqli = db::get_mysqli();
        $ship_id = (integer) $ship_id;

        $sql = "SELECT * FROM star_ships_variant WHERE ship_id={$ship_id}";
        $query = $mysqli->query($sql);
    
        $variants = [];
        while($variant = $query->fetch_assoc()) {
            $variants[] = new \JULIET\api\Ships\models\ShipVariant($variant);
        }
        
        return $variants;
    }
}