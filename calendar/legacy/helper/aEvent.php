<?php namespace JULIET\Calendar\helper;
use JULIET\api\db;
class aEvent extends aArrayAccess {

  public $id, $start, $end, $author, $perm, $private, $title, $text, $del, $membersMax, $topic;

  private function __construct($event) {
      $this->parse_mysql($event);
  }

  private function parse_mysql($event) {
    foreach($event as $propertie => $value)
      $this->{$propertie} = $value;
  }

  public static function get_event($eId) {
    global $mysqli;
	  $mysqli = db::get_mysqli();
    if(is_numeric($eId)) {
      $try = $mysqli->query("SELECT * FROM ju_events WHERE id='".$eId."' LIMIT 1");
      $event = $try->fetch_assoc();
    }
    else if (is_array($eId)) $event = $eId;

    return new aEvent($event);
  }


}
?>
