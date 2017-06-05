<?php namespace JULIET\api;

use Respect\Rest\Routable;
use JULIET\api\Ships\helpers\Ship;

class ShipTemplates extends CommonRoutable {
    protected $TREAT_POST_AS_GET = true;

    
    protected function switch_get($path) {
        switch($path) {
            case "add":
                $shipT = new Ships\models\ShipVariant($_REQUEST['shipTemplate']);
                if(
                    $shipT->ship_id > 0 && Rights\Main::user_can("USER_CAN_EDIT_SHIP", 0, $shipT->ship_id)
                 || $shipT->ship_type_id > 0 && Rights\Main::user_can("USER_IS_ADMIN")
                ) $return = Ships\helpers\ShipVariant::add($shipT);
                else throw new \Exception("USER_NO_RIGHTS");

            break;

            case "delete":
                $shipT = new Ships\models\ShipVariant($_REQUEST['shipTemplate']);
                if(
                    $shipT->ship_id > 0 && Rights\Main::user_can("USER_CAN_EDIT_SHIP", 0, $shipT->ship_id)
                 || $shipT->ship_type_id > 0 && Rights\Main::user_can("USER_IS_ADMIN")
                ) return Ships\helpers\ShipVariant::remove($shipT);
                else throw new \Exception("USER_NO_RIGHTS");
            break;

            case "update":
                $shipT = new Ships\models\ShipVariant($_REQUEST['shipTemplate']);
                if(
                    $shipT->ship_id > 0 && Rights\Main::user_can("USER_CAN_EDIT_SHIP", 0, $shipT->ship_id)
                 || $shipT->ship_type_id > 0 && Rights\Main::user_can("USER_IS_ADMIN")
                ) return Ships\helpers\ShipVariant::update($shipT);
                else throw new \Exception("USER_NO_RIGHTS");
            break;
        }
    }

}