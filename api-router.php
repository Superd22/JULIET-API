<?php namespace JULIET\api;

use Respect\Rest\Router;

class APIRouter {

    private static $_r3;

    private function __construct() {
        APIRouter::$_r3 = new Router("/API/Juliet");
    }

    public static function get_router() {
        if( is_null(APIRouter::$_r3) ) new APIRouter();
        
        return APIRouter::$_r3;        
    }
}

?>
