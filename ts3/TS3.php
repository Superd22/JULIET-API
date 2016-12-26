<?php namespace JULIET\api;

require_once(__DIR__."/helper/TS.php");

use Respect\Rest\Routable;
use JULIET\api\TS3\helper\TS as TS;

class TS3 implements Routable {
    protected $TS;
    
    function __construct() {
        $this->TS = new TS();
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
    
    private function switch_get($route) {
        switch($route) {
            case "USER_STATUS":
                return $this->TS->get_user_status();
                break;
            
            case "SERVER_STATUS":
                break;
            
            case "REMOVE_USER":
                return $this->TS->unregister_user((integer) $_GET['id']);
            break;
    }
}
}
?>