<?php namespace JULIET\api;

use Respect\Rest\Routable;

class CommonRoutable implements Routable {
    
    /**
    * If we want to treat GET and POST request with the same function (switch_get)
    *
    * @var boolean true for post as get, false for post using switch_post()
    */
    protected $TREAT_POST_AS_GET;
    
    public function __construct() {
    }
    
    public function get($filename = "") {
        $method_name = "get_".$filename;
        if(strpos($filename, "php") !== false) {
            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
        }
        else {
            try {
                if(method_exists($this, $method_name)) $this->display_return(call_user_func([$this,$method_name],$filename));
                else $this->display_return($this->switch_get($filename));
                }
            catch(\Exception $e) {
                $this->display_error($e);
            }
        }
    }
    
    public function post($filename = "") {
        $_REQUEST = array_merge($_GET, json_decode(file_get_contents('php://input'), true));
        if(strpos($filename, "php") !== false) {
            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
        }
        else {
            try {

                $get_name = "get_".$filename;
                $post_name = "post_".$filename;

                if(method_exists($this, $post_name)) $return = call_user_func([$this,$post_name],$filename);

                else if($this->TREAT_POST_AS_GET) {
                    if(method_exists($this, $get_name))  $return = call_user_func([$this,$get_name],$filename);
                    else $return = $this->display_return($this->switch_get($filename));
                }
                else $return = $this->switch_post($filename);
                    
                $this->display_return($return);
            }
            catch(\Exception $e) {
                $this->display_error($e);
            }
        }
    }
    
    private function display_return($return) {
        print_r(Response::json_response($return));
    }
    
    private function display_error(\Exception $e) {
        print_r(Response::json_error($e->getMessage()));
    }
    
    
}

?>