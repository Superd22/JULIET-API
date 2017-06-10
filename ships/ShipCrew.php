<?php namespace JULIET\api;

use Respect\Rest\Routable;
use JULIET\api\Ships\helpers\Ship;
use JULIET\api\Ships\models as Models;
use JULIET\api\Ships\Rights as R;

class ShipCrew extends CommonRoutable {
    protected $TREAT_POST_AS_GET = true;

    
    protected function switch_get($path) {
        switch($path) {
            case "positionUpdate":
                $position = Models\CrewPosition($_REQUEST['position']);
                if( R\ShipTemplate::user_can_edit_crew_of_ship_template(0, $position->template_id) ) 
                    $return = Ships\helpers\CrewPosition::update($position);
                else throw new \Exception("USER_NO_RIGHTS");

            case "positionDelete":
                $position = Models\CrewPosition($_REQUEST['position']);
                if( R\ShipTemplate::user_can_edit_crew_of_ship_template(0, $position->template_id) ) 
                    $return = Ships\helpers\CrewPosition::delete($position);
                else throw new \Exception("USER_NO_RIGHTS");

            case "memberAdd":
                $member = Models\CrewMember($_REQUEST['member']);
                if( R\ShipTemplate::user_can_edit_crew_of_ship_template(0, $position->template_id) ) 
                    $return = Ships\helpers\CrewMember::update($member);
                else throw new \Exception("USER_NO_RIGHTS");
            case "memberDelete":
                $member = Models\CrewMember($_REQUEST['member']);
                if( R\ShipTemplate::user_can_edit_crew_of_ship_template(0, $position->template_id) ) 
                    $return = Ships\helpers\CrewMember::delete($member);
                else throw new \Exception("USER_NO_RIGHTS");
        }
    }

}