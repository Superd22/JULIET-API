<?php namespace JULIET\api;

require_once(__DIR__."/helpers/Ship.php");
require_once(__DIR__."/helpers/Hangar.php");
require_once(__DIR__."/models/Ship.php");
require_once(__DIR__."/models/ShipType.php");
use Respect\Rest\Routable;
use JULIET\api\Tags\helpers\Ship;
//use JULIET\API\Rights\Main as Rights;

class Ships implements Routable {
    
    public function __construct() {
    }
    
    public function get($filename) {
        if(strpos($filename, "php") !== false) {
            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
        }
        else $this->switch_get($filename);
    }
    
    public function post($filename) {
        $_POST = json_decode(file_get_contents('php://input'), true);
        if(strpos($filename, "php") !== false) {
            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
        }
    }

    private function switch_get($path) {
        try {
            if(!isset($_GET['user_id'])) $_GET['user_id'] = 0;

            switch($path) {
                case "getPlayerHangar":                    
                    if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) $return = Ships\helpers\Hangar::getPlayerHangar($_GET['user_id']);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;

                case "getAllShipTypes":
                    if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) $return = Ships\helpers\Hangar::getAllShipsType();
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
            }

            print_r(Response::json_response($return));
        }
        catch(\Exception $e) {
            print_r(Response::json_error($e->getMessage()));
        }
       
    }
}

?>