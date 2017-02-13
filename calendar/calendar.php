<?php namespace JULIET\api;

    require_once(__DIR__."/helper/event.php");
    require_once(__DIR__."/helper/summary.php");

use Respect\Rest\Routable;

class Calendar implements Routable {
    
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
        if(strpos($filename, "php") !== false) {
            header('Content-Type: application/json');
            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
            die();
        }
        else
        switch($filename) {
        }
    }
}
?>