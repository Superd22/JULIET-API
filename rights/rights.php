<?php namespace JULIET\api;

require_once(__DIR__."/helper/Main.php");

use Respect\Rest\Routable;

class Rights implements Routable {

    public function __construct() {
        Phpbb::make_phpbb_env();
    }

    public function get($right) {
        $user_id = $_GET["user_id"];
        $target = $_GET["target"];
        try {
            $return = Response::json_response(Rights\Main::has_right($right, $user_id, $target));
            return $return;
        } catch(\Exception $e) {
            if($e->getCode() === 0) return Response::json_error($e->getMessage());
            else return Response::json_response($return, $e->getMessage());
        }
    }
}

?>