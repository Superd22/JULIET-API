<?php namespace JULIET\api;

require_once(__DIR__."/helpers/Ship.php");
require_once(__DIR__."/models/Ship.php");
require_once(__DIR__."/models/ShipType.php");
use Respect\Rest\Routable;
use JULIET\api\Tags\helpers\Ship;
//use JULIET\API\Rights\Main as Rights;

class Ships implements Routable {
    
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
            if(!isset($_GET['user_id'])) $_GET['user_id'] = 0;
            $_GET['user_id'] = Rights\Main::handle_user_id($_GET['user_id']);
            switch($path) {
                case "getPlayerHangar":
                if
                    $return = Tag::create($_GET["name"]);
                break;
                case "update":
                    $tag = new Tag($_GET['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_GET["id"])) $return = $tag->update($_GET);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "affect":
                    $tag = new Tag($_GET['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_GET["id"])) $return = $tag->affect($_GET['user_id']);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "unaffect":
                    $tag = new Tag($_GET['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_GET["id"])) $return = $tag->unaffect($_GET['user_id']);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "delete";
                    $tag = new Tag($_GET['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_GET["id"])) $return = $tag->remove();
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "migrate";
                    $tag = new Tag((integer) $_GET['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_GET["id"])) $return = $tag->migrate((integer) $_GET['target']);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "getNameById";
                    $return = Tag::get_name_by_id($_GET['id']);
                break;
                case "searchTags";
                    $return = Tags\helper\Tags::search_tag($_GET['f'],$_GET['all']);
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