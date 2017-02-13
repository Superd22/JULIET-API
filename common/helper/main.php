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


}