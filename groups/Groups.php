<?php namespace JULIET\api\groups;

use Respect\Rest\Routable;
use JULIET\api\user;
use JULIET\api\Response;
use JULIET\api\Rights\main as Rights;
use JULIET\api\CommonRoutable;

class Groups extends CommonRoutable {
    
    function __construct() {
    }

    public function get_LIST_GROUPS() {
        if(!Rights::user_can("USER_IS_SIBYLLA")) throw new \Exception("USER_NO_RIGHTS");
        return helper\Main::get_all_groups();
    }

    
    public function get_updateLegacy() {
        if(!Rights::user_can("USER_IS_ADMIN")) throw new \Exception("USER_NO_RIGHTS");
        return (new \JULIET\api\groups\controller\Group())->update_legacy();
    }
   
}
?>