<?php namespace JULIET\api;

require_once(__DIR__."/helper/main.php");

use Respect\Rest\Routable;
use JULIET\api\Common\Main;

class Common implements Routable {

    public function __construct() {
        Phpbb::make_phpbb_env();
    }

    public function get($where) {
        try {
            $return = $this->switch_get($where);
            return Response::json_response($return);
        } catch(\Exception $e) {
            if($e->getCode() === 0) return Response::json_error($e->getMessage());
            else return Response::json_response($return, $e->getMessage());
        }
    }

    public function switch_get($where) {
        switch($where) {
            case "UserSearch":
                if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) return Main::searchUser($_GET["f"]);
            case "getUserById":
                if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) return Main::getUsersById(explode(',',$_GET["ids"]));
            case "paypal":
            error_reporting(-1);
                if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) return Main::getPayPalAmount();

        }

        throw new \Exception("USER_NO_RIGHTS"); 
    }
}

?>