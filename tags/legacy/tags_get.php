<?php
use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

Phpbb::make_phpbb_env();
if(Rights::user_can("USER_IS_SIBYLLA")) {
	$mysqli = db::get_mysqli();
	
	$i = 0;
    // Récupération de si oui où non on désire un membre en particulier
    $userid = $_POST['user'];
    $shipid = $_POST['ship'];

    // Si on veut un ship.
    if($shipid > 0) $userid = 0;
    // Si on veut le membre courant
    $userid = Rights::handle_user_id($userid);
    
    // SI on veut récupérer uniquement les T.A.G.S de user
    $where = "";
    if($shipid > 0) {
        $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE ship_id='".$userid."')";
    }
    elseif ($userid > 0) {
        $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE user_id='".$userid."')";
    }
    // Récupération des T.A.G.S propres.
    $tag = $mysqli->query('SELECT * FROM star_tags '.$where.' ORDER BY ID DESC');
    while($list = $tag->fetch_assoc()) {
        
        $list['count'] = 0;
        $list['client_id'] = $i;
        $list["cat"] = "tag";
        $ct = $mysqli->query('SELECT COUNT(*) FROM star_tags_af WHERE tag_id = "'.$list['id'].'" ');
        $ct = $ct->fetch_assoc();
        
        $list['count'] = $ct["COUNT(*)"];
        if($list['count'] > 0) {
            $tags[$i] = $list;
            $i++;
        }
    }
    
    
    $where1 = $where2 = '';
    if($userid > 0) {
        $where1 = "WHERE id IN (SELECT type_id FROM star_ships WHERE owner='".$userid."')";
        $where2 = "WHERE ID IN (SELECT grade FROM star_fleet WHERE id_forum='".$userid."')";
    }
    
    if(!$shipid) {
        // Récupération de tous le reste
        // Ships maj becoze lol
        $ships = $mysqli->query('SELECT id,name,ico FROM star_ship '.$where1);
        echo $mysqli->error;
        
        while($ship = $ships->fetch_assoc()) {
            $oneship = array("id" => $ship['id'],"name" => $ship['name'],"img" => $ship['ico'], "client_id"=>$i , "cat" =>"ship", "type" => 0, "restricted" => 1);
            
            $dada = $mysqli->query('SELECT COUNT(*) FROM star_fleet WHERE FIND_IN_SET("'.$oneship['id'].'", ships)');
            $ct = $dada->fetch_assoc();
            
            $oneship['count'] = $ct["COUNT(*)"];
            
            $tags[$i] = $oneship;
            $i++;
        }
        
        // Ranks/Grade
        $ranks = $mysqli->query('SELECT ID, name, url FROM star_rank '.$where2);
        $theRanks = array();
        while ($rank = $ranks->fetch_assoc()) {
            $onerank = array("id" => $rank['ID'], "name" => utf8_encode($rank['name']), "client_id" => $i, "img" => utf8_encode($rank['url']), "cat" => "rank", "type" => 0, "restricted" => 1);
            
            $count = $mysqli->query('SELECT COUNT(*) FROM star_fleet WHERE grade="'.$rank['ID'].'"');
            $ct = $count->fetch_assoc();
            
            $onerank['count'] = $ct['COUNT(*)'];
            $tags[$i] = $onerank;
            $i++;
        }
    }
    $mysqli->close();

    print_r(Response::json_response($tags));
}

?>