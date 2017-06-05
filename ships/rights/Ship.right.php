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
        if($ship instanceof \JULIET\api\Ships\models\Ship)
        return $ship;
        if(is_numeric($ship) && $ship > 0) {
            $rShip = \JULIET\api\Ships\helpers\Ship($ship);
            $rShip->get_info();
            
            return $rShip;
        }
        
        throw new \Exception("[RIGHTS-Ship] Wrong target supplied");
    }
    
    
}
?>