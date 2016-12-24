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
    
    public static function get_all_tags($user_id = null, $ship_id = null) {
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
}