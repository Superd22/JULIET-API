<?php namespace JULIET\api;

use Respect\Rest\Routable;
use JULIET\api\Ships\helpers\Ship;

class ShipType extends CommonRoutable {
    protected $TREAT_POST_AS_GET = true;

    
    protected function switch_get($path) {
        switch($path) {
            case "update":
                if(!isset($_REQUEST['shipType'])) throw new \Exception("NO_SHIP_TARGET");
                $shipType = new Ships\models\ShipType($_REQUEST['shipType']);
                if(Rights\Main::user_can("USER_IS_ADMIN")) $return = Ships\helpers\ShipType::update_ship_type($shipType);
                else throw new \Exception("USER_NO_RIGHTS");
            break;

            case "delete": 
                if(!isset($_REQUEST['shipTypeId'])) throw new \Exception("NO_SHIP_TARGET");
                $id = $_REQUEST['shipTypeId'];
                if(Rights\Main::user_can("USER_IS_ADMIN")) $return = Ships\helpers\ShipType::delete_ship_type((integer) $id);
                else throw new \Exception("USER_NO_RIGHTS");
            break;

            case "getAll":
                if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) return Ships\helpers\Hangar::getAllShipsType();
                else throw new \Exception("USER_NO_RIGHTS");
            break;            
        }
    }

}