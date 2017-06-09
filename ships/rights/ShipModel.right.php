<?php namespace JULIET\api\Ships\Rights;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class ShipModel {
    
    public static function user_can_give_tags_to_ship_model($user, $ship) {
        return Rights::is_admin($user);
    }
    
    /**
    * Helper method to fetch a ship object if needed
    *
    * @param [integer|\models\Ship] $ship
    * @return \models\Ship ship
    */
    private static function fetch_ship_if_needed(&$ship) {
        if($ship instanceof \JULIET\api\Ships\models\ShipType)
        return $ship;
        if(is_numeric($ship) && $ship > 0) {
            $rShip = \JULIET\api\Ships\helpers\ShipType($ship);
            $rShip->get_info();
            
            return $rShip;
        }
        
        throw new \Exception("[RIGHTS-ShipModel] Wrong target supplied");
    }
}
?>