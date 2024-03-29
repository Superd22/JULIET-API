<?php namespace JULIET\api;

use Respect\Rest\Routable;
use JULIET\api\Ships\helpers\Ship;
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
        $_REQUEST = array_merge($_GET, json_decode(file_get_contents('php://input'), true));
        if(strpos($filename, "php") !== false) {
            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
        }
        else $this->switch_get($filename);
    }

    private function switch_get($path) {
        try {
            if(!isset($_GET['user_id'])) $_GET['user_id'] = 0;

            switch($path) {
                case "getPlayerHangar":                    
                    if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) $return = Ships\helpers\Hangar::getPlayerHangar($_GET['user_id']);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;

                case "registerNewPlayerShip":
                    if(Rights\Main::user_can("USER_CAN_ADMIN_SHIPS", $_GET['user_id'])) {
                        $hangar = new Ships\helpers\HangarPlayer($_GET['user_id']);
                        $return = $hangar->registerShip((integer) $_GET['type_id']);
                    }
                    else throw new \Exception("USER_NO_RIGHTS");
                break;

                /**
                 * SINGLE SHIP 
                 */
                case "deleteShip":
                    $ship = new Ships\helpers\Ship((integer) $_GET['ship_id']);
                    $owner = $ship->get_owner();
                    
                    if(!$owner) $return = true;
                    if(Rights\Main::user_can("USER_CAN_ADMIN_SHIPS", $owner)) $return =  $ship->delete();
                    else throw new \Exception("USER_NO_RIGHTS");
                break;

                case "updateShip":
                    $shipModel = new Ships\models\Ship($_REQUEST['ship']);
                    $ship = new Ships\helpers\Ship($shipModel->id);
                    $owner = $ship->get_owner();
                    if(!$owner) throw new \Exception("SHIP_DOESNT_EXIST");
                    if(Rights\Main::user_can("USER_CAN_ADMIN_SHIPS", $owner)) $return = $ship->update($shipModel);
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