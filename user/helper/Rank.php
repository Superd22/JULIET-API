<?php namespace JULIET\api\user;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
class aRank {
    public $ID, $name, $url, $type, $stars, $pos;
    
    function __construct($rank) {
        if(is_numeric($rank)) {
            $rank = self::get_rank_info($rank);
        }
        
        if(is_array($rank))
            foreach($rank as $pp=>$val)
                if($pp) $this->{$pp} = $val;
    }
    
    public static function get_rank_info($rank) {
        $rank = (integer) $rank;
        $mysql = db::get_mysqli();
        
        $sql = "SELECT * FROM star_rank WHERE ID='{$rank}' LIMIT 1";
        $d = $mysql->query($sql);
        $r = $d->fetch_assoc();
        
        return $r;
    }

    public static function get_fleet_star($fleet = 1, $_star = null) {
        $fleet = (integer) $fleet;
        $mysql = db::get_mysqli();
        if($_star !== null && $_star >= 0) $where = " AND stars='{$_star}'";

        $sql = "SELECT * from star_rank WHERE type='{$fleet}' {$where}";
        $q = $mysql->query($sql);


        $return = [];
        while($r = $q->fetch_assoc()) 
            $return[] = new aRank($r);

        return $return;
    }
}

?>