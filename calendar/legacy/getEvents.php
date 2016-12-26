<?php
use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
Phpbb::make_phpbb_env();
if(Rights::user_can("USER_IS_SIBYLLA")) {
    $mysqli = db::get_mysqli();
    require_once(__DIR__."/getInvit.php");
    
    $eStart = $_REQUEST['eStart'];
    $eMod = $_REQUEST['eMod'];
    
    $all = false;
    
    switch($eMod) {
    case "day":   $delta = 86400;   break;
    case "week":  $delta = 691200;  break;
    case "month": $delta = 22118400;  break;
    case "all": $all = true; break;
    }
                
                $events = array();
                if(!$all) $query = $mysqli->query("SELECT * FROM ju_events WHERE start >=".($eStart-$delta)." AND start <= ".($eStart+$delta)." AND del='0' ");
                else  $query = $mysqli->query("SELECT * FROM ju_events WHERE start >=".time()." AND del='0' LIMIT 20");

                while($event = $query->fetch_assoc()) {
                    $try = \ju_cal_invit_info_user($event['id']);
                    
                    foreach($try["EVENT"] as $pp => $v)
                    $try[$pp] = $v;
                    
                    $events[] = $try;
                }
                print_r(json_encode($events));
                
  }
            ?>