<?php namespace JULIET\api\Tags\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class TagShip extends Tag {
    public $cat = "ship";
    
    public function __construct($ship) {
        parent::__construct($ship, "ship");
    }
    
    public static function get_all_tags($user_id = null, $ship_id = null, $ship_type_id = null, $ship_template_id = null, $ressource_id = null) {
        Phpbb::make_phpbb_env();
        global $user;
        
        $where = '';
        if($userid == 1) $userid = $user->data['user_id'];
        if($userid > 0)
        $where = "WHERE id IN (SELECT type_id FROM star_ships WHERE owner='".$userid."')";
        
        $ships = $mysqli->query('SELECT id,name,ico FROM star_ship '.$where);
        
        while($ship = $ships->fetch_assoc()) {
            $oneship = array("id" => $ship['id'], "name" => $ship['name'],"img" => $ship['ico'], "type" =>"ship");
            $tag = new TagShip($oneship);
            if($tag->get_count() > 0) $return[] = $tag;
        }
        
        return $return;
    }
    
    public function get_count() {
        $dada = $mysqli->query('SELECT COUNT(*) FROM star_fleet WHERE FIND_IN_SET("'.$this->id.'", ships)');
        $ct = $dada->fetch_assoc();
        
        $this->count = $ct["COUNT(*)"];
        
        return $this->count;
    }
    
    /**
    * Get all the info about this tag + who has it
    * @param $tag_name the name of the tag to fetch
    * @param $all get all type of ressources (only user if false)
    * @return a Tag with ressources info, or null.
    */
    public static function get_tag_info($tag_name, $all = false) {
        if(!is_string($tag_name) || empty($tag_name)) throw new \Exception("NO VALID TAG NAME");
        $mysqli =  db::get_mysqli();
        $tags = $mysqli->query('SELECT * FROM star_ship WHERE name="'.$mysqli->real_escape_string($tag_name).'" LIMIT 1');
        $tag = $tags->fetch_assoc();
        
        // Construction du "TAG" afférant au vaisseau
        $oneship = array("id" => $tag['id'],"name" => $tag['name'],"img" => $tag['ico'],"cat" =>"ship");
        // Holds our basics information.
        $rTag = new TagShip($oneship);

        // Get our ressources
        $rTag->fetch_owner_of_this($all);
        
        return $rTag;
    }

    /**
     * Fetch the owners of this ship
     *
     * @param boolean $all
     * @return void
     */
    public function fetch_owner_of_this($all = false) {
        $mysqli =  db::get_mysqli();
        $own = $mysqli->query('SELECT * FROM star_ships WHERE type_id='.$this->id);
        
        while($owner = $own->fetch_assoc()) {
            // Make sure we're recognized as an user
            $userPacket = array_slice($owner, 0);
            $userPacket['user_id'] = $userPacket['owner'];
            // declare this user as a target for this tag
            $this->declareTarget($userPacket, $all);

            if($all) {
                // Make sure we're recognized as a ship instance
                $shipPacket = array_slice($owner, 0);
                $shipPacket['ship_id'] = $shipPacket['id'];
                // declare this ship instance as a target for this tag
                $this->declareTarget($shipPacket, $all);
            }

        }
    }
}