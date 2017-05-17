<?php namespace JULIET\API\Rights;

use JULIET\api\Phpbb;
class Main {
  const USER_CAN_ADMIN_TAGS = "USER_CAN_ADMIN_TAGS";
  const USER_CAN_ADMIN_RANKS = "USER_CAN_ADMIN_RANKS";
  const USER_CAN_ADMIN_SHIPS = "USER_CAN_ADMIN_SHIPS";
  const USER_CAN_SEE_ADMIN_LOGS = "USER_CAN_SEE_ADMIN_LOGS";
  const USER_CAN_ADMIN_EVENT = "USER_CAN_ADMIN_EVENT";

  const GROUP_GO = 17;
  const GROUP_ADMIN = 1;
  const GROUP_CHEF_SECTOR = 35;

  const GROUP_SIBYLLA = 13;

  private static function mysqli() {
      $mysqli = \JULIET\API\db::get_mysqli();
      if(isset($mysqli)) return $mysqli;
      else throw new \Exception("NO MYSQLI");
  }

  public static function handle_user_id($user_id) {
    Phpbb::make_phpbb_env();
    global $user;

    if(is_numeric($user_id) && $user_id > 0) return $user_id;
    else if (is_numeric($user_id)) {
      if(isset($user) && isset($user->data['user_id'])) return $user->data['user_id'];
      else throw new \Exception("USER_NOT_LOGGED_IN");
    }
    else if (is_array($user_id)) {
      foreach($user_id as $id)
        $try[] = SELF::handle_user_id($id);
      return $try;
    }
  }
  public static function user_can($right, $user_id = 0, $target = false) {
    return self::has_right($right, $user_id, $target);
  }

  public static function has_right($right, $user_id = 0, $target = false) {
    switch($right) {
      case "USER_CAN_ADMIN_TAGS":
      case "USER_CAN_ADMIN_RANKS":
      case "USER_CAN_ADMIN_SHIPS":
      case "USER_CAN_SEE_ADMIN_LOGS":
        // lEGACY : user_id is target here.
        return SELF::has_admin_on_player(0, $user_id);
      break;
      case "USER_CAN_ADMIN_EVENT":
        return SELF::can_admin_event($user_id, $target);
      break;
      case "USER_IS_SIBYLLA":
        return SELF::is_sibylla($user_id);
      case "USER_IS_LOGGED_IN":
        return SELF::is_logged_in();
      case "USER_CAN_ADMIN_TAG":
        return SELF::can_admin_tag($user_id, $target);
      case "USER_CAN_SEE_JULIET":
        return SELF::user_can_see_juliet($user_id);
      case "USER_IS_ADMIN": 
        return SELF::is_admin($user_id);
      case "HYDRATE_USER":
        return SELF::hydrate_ju_user();
    }
  }

  public static function hydrate_ju_user() {
    $user_id = self::handle_user_id(0);

    return [
            "userId"  => $user_id,
            "isAdmin" => self::is_admin($user_id),
           ];
  }

  public static function can_admin_event($user_id, $target) {
    $user_id = SELF::handle_user_id($user_id);
    if(SELF::is_admin($user_id)) return true;

    $sql = "SELECT author,perm FROM ju_events WHERE id='".$target."' LIMIT 1";
    $test = $this->db->query($sql);
    $ri = $test->fetch_assoc();

    $test = array();
    $test[] = $ri['author'];
    $test = array_merge($test, explode(',',$ri['perm']) );

    return in_array($user_id,$test);
  }

  public static function user_can_see_juliet($user_id) {
    if(!self::is_logged_in()) {
      throw new \Exception("USER_NOT_LOGGED_IN");
      return false;
      }
    else if (!self::is_sibylla($user_id)) {
      throw new \Exception("USER_NOT_SIBYLLA");
      return false;
    }
    else return true;
  }

  public static function can_admin_tag($user_id, $target, $deep = false) {
    // The TAG ID.
    $target = (integer) $target;
    
    if($target == 0) return false;
    if(!$deep) $user_id = SELF::handle_user_id($user_id);
    if(SELF::is_admin($user_id)) return true;
    if($deep && \JULIET\api\Tags\helper\Tag::user_has_tag($user_id, $target)) return true;

    return can_admin_tag($user_id, \JULIET\api\Tags\helper\Tag::get_rights_from($target), true);
  }

  private static function has_admin_on_player($user_id = 0, $target = 0) {
    if(SELF::is_admin($user_id)) return true;
    else return ($user_id === $target);
  }

  private static function is_admin($user_id = 0) {
    $user_id = SELF::handle_user_id($user_id);
    $sql = "SELECT * FROM testfo_user_group WHERE user_id='{$user_id}'";

    $r = self::mysqli()->query($sql);
    while($ad = $r->fetch_assoc()) {
			$groups[] = $ad['group_id'];
		}
    
    if (isset($groups) && (in_array(self::GROUP_ADMIN,$groups) || in_array(self::GROUP_GO,$groups) || in_array(self::GROUP_CHEF_SECTOR,$groups))) return true;
    return false;
  }

  public static function is_sibylla($user_id = 0) {
    $user_id = SELF::handle_user_id($user_id);
    $sql = "SELECT * FROM star_fleet WHERE id_forum='{$user_id}' AND pending='0' LIMIT 1";

    $r = SELF::mysqli()->query($sql);
    $user = $r->fetch_assoc();

    if(isset($user['id'])) return true;
    return false;
  }

  public static function is_logged_in() {
    Phpbb::make_phpbb_env();
    global $user;
    if(isset($user) && isset($user->data['user_id']) && $user->data['user_id'] > 1) return true;
    return false;
  }

}
