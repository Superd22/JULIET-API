<?php namespace JULIET\api\groups\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Main {

    public static function get_all_groups($limit = -1, $offset = 0) {
		$mysqli = db::get_mysqli();
        $lm = "";
        if($limit >= 0) $lm = "LIMIT {$offset}, {$limit}";
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM star_squad {$lm}";

        $d = $mysqli->query($sql);
        $r = array();
        while($gp = $d->fetch_assoc()) {
            $r[] = new Group($gp);
        }

        $d->free_result();
        $d = $mysqli->query("SELECT FOUND_ROWS() AS count");
        $count = $d->fetch_assoc();



        return array('count' => $count['count'], 'data' => $r);
    }

}