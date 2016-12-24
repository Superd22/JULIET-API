<?php
use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

Phpbb::make_phpbb_env();
$mysqli = db::get_mysqli();
// Récupération de si oui où non on désire un membre en particulier
$f = request_var("f","");
$m = request_var("mod","");
$d = file_get_contents('php://input');


if($f == '' && $m == '' && $d != '') {
    $d = json_decode($d, true);
    $f = $d['f'];
    $m = $d['mod'];
}
$f = $mysqli->real_escape_string($f);
// Récupération des T.A.G.S propres.
$tag = $mysqli->query('SELECT * FROM star_tags WHERE name LIKE "%'.$f.'%" ORDER BY name DESC');
while($list = $tag->fetch_assoc()) {
    $list['pretty_type'] = "Tag";
    $tags[] = $list;
}

if($m == "ALL") {
    $ranks = $mysqli->query("SELECT * FROM star_rank WHERE name LIKE '%".$f."%' ORDER BY name DESC");
    while($r = $ranks->fetch_assoc()) {
        $r["img"] = $r["url"];
        $r["id"] = $r["ID"];
        $r['type'] = 1;
        $r['pretty_type'] = "Rank";
        
        $tags[] = $r;
    }
    
    $ships = $mysqli->query("SELECT * FROM star_ship WHERE name LIKE '%".$f."%' ORDER BY name DESC");
    while($s = $ships->fetch_assoc()) {
        $s['img'] = $s['ico'];
        $s['type'] = 2;
        $s['pretty_type'] = "Ship";
        
        $tags[] = $s;
    }
}



$mysqli->close();
print_r(Response::json_response($tags));
?>