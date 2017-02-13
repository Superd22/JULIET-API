<?php namespace JULIET\api\user;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
class aUser {
    public $id,$id_forum,$fleet,$grade,$prenom,$nom,$callsign, $pending, $handle, $activite, $notif;
    public $avatar;
    public $rank;
    private $db;

    public function __construct($user, $full = false) {
        if(is_numeric($user)) {
            $user = $full ? self::get_all_user_info($user) : self::get_user_info($user);
        }
        if(is_array($user))
        foreach($user as $pp=>$val)
            if($pp) $this->{$pp} = $val;


        $this->db = db::get_mysqli();
    }

    public static function get_user_info($user_id) {
        $user_id = Rights::handle_user_id($user_id);

        $mysql = db::get_mysqli();
        $sql = "SELECT * FROM star_fleet WHERE id_forum='{$user_id}' LIMIT 1";
        $q = $mysql->query($sql);
        $r = $q->fetch_assoc();

        return $r;
    }

    public static function get_all_user_info($user_id) {
        $user_id = Rights::handle_user_id($user_id);
        $r = self::get_user_info($user_id);

        $r["avatar"] = Main::ju_get_avatar($user_id);
        $r["rank"] = new aRank($r["grade"]);
        $r["pseudo"] = Main::get_names($user_id);

        return $r;
    }
}
?>