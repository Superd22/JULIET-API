<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Ships\models\ShipType as ShipTypeModel;

use JULIET\api\Ships\models as Models;


class CrewCompliment {
    
    private $variant;
    private $variant_id;
    
    public function __construct($variant_id) {
        $this->variant = new ShipVariant($variant_id);
        $this->variant_id = (integer) $variant_id;
    }
    
    public static function get_crew_compliment_of_variant($variant_id) {
        $crew_compliment = new \JULIET\api\Ships\models\CrewCompliment($variant_id);
        
        $crew_compliment->set_positions(self::get_crew_positions($variant_id));
        $crew_compliment->set_crew(self::get_crew_members($variant_id));
        
        return $crew_compliment;
    }
    
    public static function get_crew_positions($variant_id) {
        $variant_id = (integer) $variant_id;
        $mysqli = db::get_mysqli();
        
        $sql = "SELECT * FROM star_ships_variant_positions WHERE template_id='{$variant_id}'";
        $query = $mysqli->query($sql);
        
        $positions = [];
        if($query) while($crew = $query->fetch_assoc()) {
            $positions[] = new Models\CrewPosition($crew);
        }
        
        return $positions;
    }
    
    public static function get_crew_members($variant_id) {
        $variant_id = (integer) $variant_id;
        $mysqli = db::get_mysqli();
        
        $sql = "SELECT * FROM star_ships_variant_crew WHERE template_id='{$variant_id}'";
        $q = $mysqli->query($sql);
        
        $crew_members = [];
        if($q) while($crew = $q->fetch_assoc()) {
            if($crew['user_id']) {
                $crew['user'] = \JULIET\API\Common\Main::getUsersById($crew['user_id']);
                $crew_members[] = new Models\CrewMember($crew);
            }
        }
        
        return $crew_members;        
    }
    
}


?>