<?php namespace JULIET\api\Tags\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class TagRank extends Tag {
    public $cat = "rank";
    
    public function __construct($rank) {
        parent::__construct($rank, "rank");
    }
    
    public static function get_all_tags($user_id = null, $ship_id = null) {
        Phpbb::make_phpbb_env();
        global $user;
        if($userid == 1) $userid = $user->data['user_id'];
        
        $where = "";
        if($userid > 0) $where = "WHERE ID IN (SELECT grade FROM star_fleet WHERE id_forum='".$userid."')";
        
        $ranks = $mysqli->query('SELECT ID, name, url FROM star_rank '.$where);
        $return = [];
        while ($rank = $ranks->fetch_assoc()) {
            $onerank = array("id" => $rank['ID'], "name" => $rank['name'], "client_id" => $i, "img" => $rank['url'], "type" => "rank");
            $tag = new TagRank($onerank);
            if($tag->get_count() > 0) $return[] = $tag;
        }
        
        return $return;
    }
    
    public function get_count() {
        $count = $mysqli->query('SELECT COUNT(*) FROM star_fleet WHERE grade="'.$this->id.'"');
        $ct = $count->fetch_assoc();
        
        $this->count = $ct['COUNT(*)'];

        return $this->count;
    }
}