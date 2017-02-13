<?php namespace JULIET\api\calendar\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Event {
    protected $id;
    protected $start, $end, $autho, $perm, $private, $title, $text, $del, $membersMax, $topic;
    protected $invitations;

    function __construct($id) {
        if(is_numeric($id)) $this->id = $id;
        elseif(isset($id['id']) && isset($id["title"])) 
            foreach($id as $pp => $val)
                $this->{$pp} = $val; 
    }

    public static function create_event($payload) {
        $mysqli = db::get_mysqli();
        $event = self::mysqlise_event($payload);
        $try = $mysqli->query("INSERT INTO ju_events
            (title, text, private, start, end, perm, author, membersMax, topic)
            VALUES (
              '".$event["title"]."',
              '".$event["text"]."',
              '".$event["private"]."',
              '".$event["start"]."',
              '".$event["end"]."',
              '".$event["perm"]."',
              '".$user->data['user_id']."',
              '".$event["membersMax"]."',
              '".$event["topic"]."'
            )");
            
        if($try && !$mysqli->error) return $mysqli->insert_id;
        else throw new \Exception($mysqli->error);    
    }    

    public static function update_event($event) {
        $e = new Event($event);
        return $e->update();
    }

    public function update($new = array()) {
        $push = mysqlise_event(array_merge((array) $this, $new));
        $mysqli = db::get_mysqli();

        $try = $mysqli->query("UPDATE ju_events SET
          title='".$push["title"]."',
          text='".$push["text"]."',
          private='".$push["private"]."',
          start='".$push["start"]."',
          end='".$push["end"]."',
          perm='".$push["perm"]."',
          membersMax='".$push["membersMax"]."',
          topic='".$push["topic"]."'
          WHERE id='".$push['id']."' ");

        if($try) return true;
        else throw new \Exception($mysqli->error);
    }

    public static function delete_event($id) {
        $e = new Event((integer) $id);

        return $e->delete();
    }

    public function delete() {
        $mysqli = db::get_mysqli();
        $try = $mysqli->query("UPDATE ju_events SET del='1' WHERE id='".$this->id."'");

        return $try;
    }

    private static function mysqlise_event($event) {
        $mysqli = db::get_mysqli();

      $event['title']       = $mysqli->real_escape_string($event['title']);
      $event['text']        = $mysqli->real_escape_string($event['text']);
      $event['private']     = (integer) $event['private'];
      $event['start']       = (integer) $event['start'];
      $event['end']         = (integer) $event['end'];
      $event['membersMax']  = (integer) $event['membersMax'];
      $event['topic']       = (integer) $event['topic'];
      $event['perm']        = implode(',',$event['perm']);

      return $event;
    }

    public static function get_single_event($id) {
        $e = new Event((integer) $id);

        return $e->get_single();
    }

    public function get_single() {
        $mysqli = db::get_mysqli();

        if(!$this->title) {
            $query = $mysqli->query("SELECT * FROM ju_events WHERE id ='".$this->id."' AND del='0' LIMIT 1");
            $e = new Event($query->fetch_assoc());
            return $e->get_single();
        }
        
        $this->perm = (array) explode(",",$this->perm);
        foreach($this->perm as $key=>$perm) {
            if($perm == "") unset($this->perm[$key]);
        }

        $this->membersMax = (integer) $this->membersMax;
        $this->perm = array_values($this->perm);


        $invits = $mysqli->query("SELECT * FROM ju_events_invit WHERE id_event='".$this->id."'");
        while($invit = $invits->fetch_assoc()) {
            if($invit['type'] < 0) $tags[] = $invit;
            elseif($invit['type'] == 2) $grps[] = $invit;
            elseif($invit['confirm'] > 0) $membrs[] = $invit;
            else $rsvp[] = $invit;
        }

        $this->invitations['groupes'] = $grps;
        $this->invitations['members'] = $membrs;
        $this->invitations['invits'] = $rsvp;
        $this->invitations['tags'] = $tags;
    }

}