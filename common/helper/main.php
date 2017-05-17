<?php namespace JULIET\API\Common;

use JULIET\api\Phpbb;
use JULIET\API\db;
class Main {

  const GROUP_GO = 17;
  const GROUP_ADMIN = 1;
  const GROUP_CHEF_SECTOR = 35;
  const GROUP_SIBYLLA = 13;

  private static function mysqli() {
      $mysqli = db::get_mysqli();
      if(isset($mysqli)) return $mysqli;
      else throw new \Exception("NO MYSQLI");
  }

  public static function searchUser($user) {
      $db = self::mysqli();

      $user = $db->real_escape_string($user);
      $sql = "SELECT username, user_id FROM testfo_users, star_fleet  WHERE username LIKE '%".$user."%' 
      AND user_id = id_forum";

      $q = $db->query($sql);

      return $q->fetch_all(MYSQLI_ASSOC);
  }

    public static function getUsersById($ids) {
        function fetchUserById($id) {
            $myqsli = db::get_mysqli();
            $id = (integer) $id;

            $sql = "SELECT username, user_id as id FROM testfo_users WHERE user_id = '{$id}' LIMIT 1";
            $q = $myqsli->query($sql);

            $user = $q->fetch_assoc();
            $user['avatar'] = Main::get_user_avatar($user['id']);
            if($user["username"]) return $user;
            else return false;
        }

        $r = [];
        foreach($ids as $id)
            $r[] = fetchUserById($id);

        return $r;   
    }

    public static function get_user_avatar($user_id) {
        $mysqli = db::get_mysqli();
        $id = (integer) $user_id;
    
        $sql = "SELECT user_avatar,user_avatar_type from testfo_users WHERE user_id='{$id}' LIMIT 1";
        $d = $mysqli->query($sql);

        $avatar = $d->fetch_assoc();

        if($avatar['user_avatar_type'] == "avatar.driver.remote") return $avatar['user_avatar'];
        elseif(!empty($avatar['user_avatar'])) return "https://www.starcitizen.fr/Forum/download/file.php?avatar=".$avatar['user_avatar'];
        return "https://starcitizen.fr/Forum/images/avatars/gallery/Personnages/uee2.png";
    }


}