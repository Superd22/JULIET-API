<?php namespace JULIET\api\Ships\Rights;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class ShipTemplate {
    
    public static function user_can_give_tags_to_ship_template($user, $ship) {
        $user = Rights::handle_user_id($user);
        self::fetch_ship_template($ship);

        if(Rights::is_admin($user)) return true;
        if($ship->ship_id) return Ship::user_can_give_tags_to_ship($user, $ship->ship_id);
        else if($ship->ship_type_id) return ShipModel::user_can_give_tags_to_ship_model($user, $ship->ship_type_id);
    }

    public static function user_can_edit_crew_of_ship_template($user, $ship) {
        $user = Rights::handle_user_id($user);
        self::fetch_ship_template($ship);

        if(Rights::is_admin($user)) return true;
        // If we're not admin we can't currently edit templates of models
        if($ship->ship_type_id > 0) return false;

        return Ship::user_can_edit_ship($user, $ship->ship_id);
    }
    
    private static function fetch_ship_template(&$ship) {
        if($ship instanceof \JULIET\api\Ships\models\ShipVariant) return;
        if(is_numeric($ship) && $ship > 0) {
            $rShip = \JULIET\api\Ships\helpers\ShipVariant($ship);
            $rShip->get_info();
            
            $ship = $rShip;

            return;
        }
        else {
            $ship = new \JULIET\api\Ships\models\ShipVariant($ship);
            return;
        }
        
        throw new \Exception("[RIGHTS-Template] Wrong target supplied");
    }
}
?>