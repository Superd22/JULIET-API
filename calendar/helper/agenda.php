<?php namespace JULIET\api\calendar\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Agenda {

    public static function get_events_by_date($eMod="week", $start = 0){
        $all = false;
        $events = array();
        $mysqli = db::get_mysqli();

        switch($eMod) {
            case "day":   $delta = 86400;   break;
            case "week":  $delta = 691200;  break;
            case "month": $delta = 22118400;  break;
            case "all": $all = true; break;
        }

        if(!$all) $query = $mysqli->query("SELECT * FROM ju_events WHERE start >=".($start-$delta)." AND start <= ".($start+$delta)." AND del='0' ");
        else  $query = $mysqli->query("SELECT * FROM ju_events WHERE start >=".time()." AND del='0' LIMIT 20");

        while($event = $query->fetch_assoc()) {
            $try = Summary::get_summary($event['id']);
                    
            foreach($try["EVENT"] as $pp => $v)
                $try[$pp] = $v;
                    
                $events[] = $try;
            }
            
        return $events;
    }

    public static function get_event_archive($post_per_page = 0, $page = 0, $include_del=true) {
        $mysqli = db::get_mysqli();

        if($post_per_page === 0) $post_per_page = 30;
        if($include_del) $del = " WHERE del='0' ";
        $offset = $post_per_page*$page;

        if($post_per_page > 0) $lim = " LIMIT {$offset}, {$post_per_page} ";


        $sql = "SELECT SQL_CALC_FOUND_ROWS * from ju_events {$del} order by id DESC".$lim;
        $q = $mysqli->query($sql);

        
        $events = array();
        while($e = $q->fetch_assoc())
            $events[] = new Event($e);

        $d = $mysqli->query("SELECT FOUND_ROWS() AS count");
        $count = $d->fetch_assoc();

        return array("data" => $events, "count" => $count['count']);
    }
}