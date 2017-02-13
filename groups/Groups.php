<?php namespace JULIET\api\groups;

require_once(__DIR__."/helper/group.php");
require_once(__DIR__."/helper/main.php");

use Respect\Rest\Routable;
use JULIET\api\user;
use JULIET\api\Response;

class Groups implements Routable {
    
    function __construct() {
    }
    
    public function post($filename) {
        $this->get($filename);
    }
    
    public function get($filename) {
        try {
            $return = $this->switch_get($filename);
        }
        catch(\Exception $e) {
            if($e->getCode() > 0) print_r(Response::json_response($return, $e->getMessage()));
            else print_r(Response::json_error($e->getMessage()));
                return;
        }
        print_r(Response::json_response($return));
    }
    
    private function switch_get($filename) {
        switch($filename) {
            case "LIST_GROUPS":
                return helper\Main::get_all_groups();
            break;
        }
    }
}
?>