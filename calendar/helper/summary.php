<?php namespace JULIET\api\calendar\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Summary {
  public $EVENT, $INVIT, $GROUP, $canSendInv, $IsIn, $Asked;

  private function __construct($data) {
    $this->parse_obj($data);
  }

  private function parse_obj($data) {
    foreach($data as $propertie => $value) {
      $this->{$propertie} = $value;
    }
  }

  public static function get_summary($eId, $user_id = 0) {
    return SELF::ju_cal_invit_info_user($eId, $user_id);
  }

  public static function ju_cal_invit_info_user($eId, $user_id = 0) {
    global $user;
    $mysqli = db::get_mysqli();
    if($user_id === 0) $user_id = $user->data['user_id'];
    if($eId instanceof Event) {$event = $eId; $eId = $event->id;}
    else {
      $event = Event::get_single_event($eId);
    }


    $gInv = $asked = $isIn = false;

    $try = $mysqli->query("SELECT * FROM ju_events_invit WHERE target='".$user_id."' AND id_event='".$eId."' AND type!=2 LIMIT 1");
    $invit = $try->fetch_assoc();

    // On vérifie le statut de l'event.
    if($event->private == 2) {
      // L'event est privé, il faut qu'on soit invité
      if(!$invit["id"]) {
        // On a pas d'invitation au nom du joueur, on check le groupe.
        $try = $mysqli->query("SELECT sq.id, sq.nom,ev.id,ev.type,ev.target
          FROM star_squad as sq, ju_events_invit as ev
          WHERE FIND_IN_SET('".$user_id."', sq.members)
            AND sq.id = ev.target
            AND ev.type = '2'
            AND ev.id_event = '".$eId."'
          LIMIT 1");

        $gInv = $try->fetch_assoc();
        if($gInv) {$can = true;}
      }
      else {$can = true;}
    }
    else {$can = true;}

    // Si le joueur pouvait s'inscrire et qu'il l'a fait
    if($can && $invit["id"]) {
      $asked = true;
      if($invit['confirm'] == 1 OR $event->private == 0) $isIn = true;
    }

    return new Summary(array("EVENT" => $event, "INVIT" => $invit, "GROUP" => $gInv, "canSendInv" => $can, "IsIn" => $isIn, "Asked" => $asked));
  }

}
?>