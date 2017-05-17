<?php namespace JULIET\api\Tags\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

require_once(__DIR__."/rank.php");
require_once(__DIR__."/ship.php");
require_once(__DIR__."/tag.php");

class Tags {

    public static function search_tag($f, $all=false) {
        $mysqli = db::get_mysqli();
        $f = $mysqli->real_escape_string($f);
        $tags = array();
        // Récupération des T.A.G.S propres.
        $tag = $mysqli->query('SELECT * FROM star_tags WHERE name LIKE "%'.$f.'%" ORDER BY name DESC');
        while($list = $tag->fetch_assoc()) {
            $tags[] = new Tag($list);
        }

        if($all) {
            $ranks = $mysqli->query("SELECT * FROM star_rank WHERE name LIKE '%".$f."%' ORDER BY name DESC");
            while($r = $ranks->fetch_assoc()) {
                $r["img"] = $r["url"];
                $r["id"] = $r["ID"];
                $r['type'] = "rank";
                
                $tags[] = new TagRank($r);
            }
            
            $ships = $mysqli->query("SELECT * FROM star_ship WHERE name LIKE '%".$f."%' ORDER BY name DESC");
            while($s = $ships->fetch_assoc()) {
                $s['img'] = $s['ico'];
                $s['type'] = "ship";
                
                $tags[] = new TagShip($r);
            }
        }

        return $tags;
    }

}
?>