<?php namespace JULIET\api;

require_once(__DIR__."/helper/User.php");
require_once(__DIR__."/helper/Rank.php");
require_once(__DIR__."/helper/Main.php");

use Respect\Rest\Routable;
use JULIET\api\user;

class Ranks implements Routable {
    
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
            case "GET_FLEET_STAR":
                return user\aRank::get_fleet_star((integer) $_GET['fleet'], (integer) $_GET['star']);
            break;
        }
    }
}
?>