<?php namespace JULIET\api;

require_once(__DIR__."/helper/Main.php");

use Respect\Rest\Routable;

class Rights implements Routable {

    public function __construct() {
        Phpbb::make_phpbb_env();
    }

    public function get($right) {
        try {
            $return = Response::json_response(Rights\Main::has_right($right));
            return $return;
        } catch(\Exception $e) {
            if($e->getCode() === 0) return Response::json_error($e->getMessage());
            else return Response::json_response($return, $e->getMessage());
        }
    }
}

?>