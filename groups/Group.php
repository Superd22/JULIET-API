<?php namespace JULIET\api\groups;

use Respect\Rest\Routable;
use JULIET\api\user;
use JULIET\api\Response;
use JULIET\api\CommonRoutable;
use JULIET\api\Rights\main as Rights;
use JULIET\api\groups\controller\Group as GroupController;

class Group extends CommonRoutable {
    
    private $group;

    function __construct() {
        $this->group = new GroupController();
    }

    public function get_view() {
        if(!Rights::user_can("USER_IS_SIBYLLA")) throw new \Exception("USER_NO_RIGHTS");
        if(!isset($_REQUEST['groupId'])) throw new \Exception("NO_GROUP_ID");
        return $this->group->get_group($_REQUEST['groupId']);
    }
   
    public function post_update() {

    }

    public function post_affectUser() {

    }

    public function post_deAffectUser() {

    }

    public function post_delete() {

    }

    public function post_create() {

    }

}
?>