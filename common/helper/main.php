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
        $sql = "SELECT username, user_id FROM testfo_users, star_fleet  WHERE username COLLATE UTF8_GENERAL_CI LIKE '%".$user."%'
        AND user_id = id_forum";
        
        $found = [];
        
        $q = $db->query($sql);
        while($user = $q->fetch_assoc()) {
            $found[] = new Model\BaseUserInfo($user);
        }
        
        return $found;
    }
    
    private static function fetchUserById($id) {
        $myqsli = db::get_mysqli();
        $id = (integer) $id;
        
        $sql = "SELECT username, user_id as id FROM testfo_users WHERE user_id = '{$id}' LIMIT 1";
        $q = $myqsli->query($sql);
        
        $user = $q->fetch_assoc();
        $user['avatar'] = Main::get_user_avatar($user['id']);
        
        $rUser = new Model\BaseUserInfo($user);
        if($rUser['username']) return $rUser;
        else return false;
        }
    
    public static function getUsersById($ids) {
        if(is_array($ids)) {
            $r = [];
            foreach($ids as $id)
            $r[] = self::fetchUserById($id);
            
            return $r;
        }
        return self::fetchUserById($ids);
        
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
    
    public static function getPayPalAmount() {
        require_once(__DIR__."/../vendors/paypal.api.php");
        
        $return = json_decode(file_get_contents(__DIR__."/../cache/paypal.txt"));
        if(!$return || time() - $return->time > 24*3600) {
            $login = paypalCredentials();
            $paypal = new \Paypal($login['username'], $login['password'], $login['certificate']);
            
            $return = $paypal->call('GetBalance',array(),false);
            
            $return = array(
            "time" => time(),
            "count" => (integer) $return["L_AMT0"]
            );
            
            file_put_contents(__DIR__."/../cache/paypal.txt", json_encode($return));
        }
        
        return $return;
    }
    
}