<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Ships\models\ShipType as ShipTypeModel;


class Hangar {
    
    /** Array of ships in this hangar */
    public $ships;
    /** Current user has permission on this hangar */
    public $canAdmin = false;

    protected $_target;

    
    public function __construct() {  }
    
    /**
    * Main function to get a Hangar instance of the ships in the
    * specified player's hangar
    * @param player_id forum id of the target
    * @return Hangar a populated hangar object
    */
    public static function getPlayerHangar($player_id = 0) {
        $player_id = Rights::handle_user_id((integer) $player_id);
        $mysqli = db::get_mysqli();
        
        // Should never happen.
        if($player_id < 2) throw new \Exception("INVALID TARGET");
        
        $hangar = new HangarPlayer();
        $ships = [];
        $db = $mysqli->query("SELECT * FROM star_ships WHERE owner = '{$player_id}'");
        while($list = $db->fetch_assoc()) {
            $ships[] = new ShipModel($list);
        }
        
        $hangar->setShips($ships);
        $hangar->setRights(Rights::user_can("USER_CAN_ADMIN_SHIPS", $player_id));
        
        return $hangar;
    }
    
    /**
    * Get all the existing ship types
    * @return ShipType[] an array of ship types
    */
    public static function getAllShipsType() {
        $mysqli = db::get_mysqli();
        
        $shipTypes = [];
        $ships = $mysqli->query("SELECT * FROM star_ship");
        while($list = $ships->fetch_assoc()) $shipTypes[] = new ShipTypeModel($list);
        
        return $shipTypes;
    }
    
    public static function getShipTypeById($ship_type_id) {
        
    }
    
    /**
    * Setter function for the ships held by this hangar
    */
    public function setShips($ships) {
        $this->ships = $ships;
    }
    
    public function setRights($right) {
        $this->canAdmin = $right;
    }
}
?>