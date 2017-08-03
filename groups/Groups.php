<?php namespace JULIET\api\groups;

use Respect\Rest\Routable;
use JULIET\api\user;
use JULIET\api\Response;
use JULIET\api\CommonRoutable;

class Groups extends CommonRoutable {
    
    function __construct() {
    }

    public function get_LIST_GROUPS() {
        return helper\Main::get_all_groups();
    }
   
}
?>