<?php namespace JULIET\api\groups\controller;

use \JULIET\api\groups\helper\group as AGroup;
use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class GroupAffectation {
    protected $db;

    public function __construct() {
        $this->db = db::get_mysqli();
    }

    public static function getUserAffectation($affectation_row) {
        if(!$affectation_row['user_id']) throw new \Exception("wrong affectation row supplied to getUserAffectation, user_id expected.");
        
        $baseUser = \JULIET\API\Common\Main::getUsersById($affectation_row['user_id']);


        $baseUser->group_id = (integer) $affectation_row['group_id'];
        $baseUser->type = "user";

        

        
        return $baseUser;
    }

}
