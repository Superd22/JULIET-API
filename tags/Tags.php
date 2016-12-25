<?php namespace JULIET\api;

require_once(__DIR__."/helper/tag.php");
require_once(__DIR__."/helper/rank.php");
require_once(__DIR__."/helper/ship.php");
require_once(__DIR__."/helper/tags.php");

use Respect\Rest\Routable;
use JULIET\api\Tags\helper\tag;
//use JULIET\API\Rights\Main as Rights;

class Tags implements Routable {
    
    public function __construct() {
    }
    
    public function get($filename) {
        if(strpos($filename, "php") !== false) {

            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
        }
        else $this->switch_get($filename);
    }
    
    public function post($filename) {
        $_POST = json_decode(file_get_contents('php://input'), true);
        if(strpos($filename, "php") !== false) {
            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
        }
    }

    private function switch_get($path) {
        try {
            switch($path) {
                case "create":
                    $return = Tag::create($_GET["name"]);
                break;
                case "update":
                    $tag = new Tag($_GET['id']);
                    $return = $tag->update($_GET);
                break;
                case "affect":
                    $tag = new Tag($_GET['id']);
                    $_GET['user_id'] = Rights\Main::handle_user_id($_GET['user_id']);
                    print_r($_GET);
                    $return = $tag->affect($_GET['user_id']);
                break;
                case "unaffect":
                    $tag = new Tag($_GET['id']);
                    $return = $tag->unaffect($_GET['user_id']);
                break;
                case "delete";
                    $tag = new Tag($_GET['id']);
                    $return = $tag->remove();
                break;
                case "migrate";
                    $tag = new Tag((integer) $_GET['id']);
                    $return = $tag->migrate((integer) $_GET['target']);
                break;
                case "getNameById";
                    $return = Tag::get_name_by_id($_GET['id']);
                break;
            }
            print_r(Response::json_response($return));
        }
        catch(\Exception $e) {
            print_r(Response::json_error($e->getMessage()));
        }
       
    }
}

?>