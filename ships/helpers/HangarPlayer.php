<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Ships\models\ShipType as ShipTypeModel;

class HangarPlayer extends Hangar {

    protected $_user;

    public function __construct($target_id = 0)  {
        $target_id = Rights::handle_user_id((integer) $target_id);
        if($target_id < 2) throw new \Exception("INVALID HANGAR TARGET ID");

        $this->_target = "player";
        $this->_user = (integer) $target_id;
    }

    /**
    * Register a new ship in this hangar
    * @param ship the ship model to register
    * @return the id of the newly registered ship
    */
    public function registerShip($ship_model_id) {
        if( !($ship_model_id > 0) ) throw new \Exception("NO TARGET FOR SHIP CREATION");
        $mysqli = db::get_mysqli();
        // Ajout du ship.
		$query = $mysqli->query("INSERT INTO star_ships
					(type_id,owner)
					VALUES ('".$ship_model_id."','".$this->_user."')
					");

		$query = $mysqli->query("SELECT * FROM star_ships WHERE id='{$mysqli->insert_id}' LIMIT 1");
		$last = $query->fetch_assoc();

		if(!$mysqli->error) {
			// ju_send_notif(1,"ship","@u_".Rights::handle_user_id(0)." : nouveau vaisseau @vs_".$last['MAX(Id)']);
            return new ShipModel($last);
		}
        else throw new \Exception("DB_ERROR");
    }

    public function unregisterShip($ship_id) {
        return $this->deleteShip($ship_id);
    }

    public function deleteShip($ship_id) {
        $ship = new Ship($ship_id);
        $ship->delete();
    }


}

?>