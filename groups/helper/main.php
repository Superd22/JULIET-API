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

        $f = $mysqli->query("SELECT FOUND_ROWS() AS count");
        $count = $f->fetch_assoc();

        $r = array();
        $gController = new \JULIET\api\groups\controller\Group();
        while($gp = $d->fetch_assoc()) {
            $r[] = $gController->getExtendedGroupFromBaseData($gp);
        }

        $d->free_result();
        $f->free_result();

        return array('count' => $count['count'], 'data' => $r);
    }

}