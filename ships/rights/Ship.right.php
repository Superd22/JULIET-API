<?php namespace JULIET\api\Ships\Rights;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

/**
* Class for all the permissions calculations regarding to a Ship/Ships
*/
class Ship {
    
    /**
    * If an user can edit a given ship (change name)
    *
    * @param [type] $user
    * @param [type] $ship
    * @return boolean
    */
    public static function user_can_edit_ship($user, $ship) {
        $user = Rights::handle_user_id($user);
        
        if(Rights::is_admin($user)) return true;
        
        self::fetch_ship_if_needed($ship);
        if($user == $ship->owner) return true;
        
        return false;
    }
    
    /**
     * If an user can affect/unaffect tags to a given ship
     * (as in "yes, you're allowed to manipulate tags for this vessel")
     * 
     * User might still need additioanl rights to assigns specitif tags
     *
     * @param [type] $user
     * @param [type] $tag
     * @param [type] $ship
     * @return void
     */
    public static function user_can_give_tags_to_ship($user, $ship) {
        return self::user_can_edit_ship($user, $ship);
    }

    /**
     * If an user can admin all the variants of a given ship
     *
     * @param [type] $user
     * @param [type] $ship
     * @return void
     */
    public static function user_can_admin_variants_of_ship($user, $ship) {
        return self::user_can_edit_ship($user,$ship);
    }
    
    
    /**
     * Helper method to fetch a ship object if needed
     *
     * @param [integer|\models\Ship] $ship
     * @return \models\Ship ship
     */
    private static function fetch_ship_if_needed(&$ship) {
        if($ship instanceof \JULIET\api\Ships\models\Ship) return;
        if(is_numeric($ship) && $ship > 0) {
            $rShip = new \JULIET\api\Ships\helpers\Ship($ship);
            $ship = $rShip->get_info();
            return;
        }
        else {
            $ship = new \JULIET\api\Ships\models\Ship($ship);
            return;
        }
        
        throw new \Exception("[RIGHTS-Ship] Wrong target supplied");
    }
    
    
}
?>