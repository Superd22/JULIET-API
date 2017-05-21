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
        // legacy
        $_POST = json_decode(file_get_contents('php://input'), true);
        // new
        $_REQUEST = array_merge($_GET,json_decode(file_get_contents('php://input'), true));
        if(strpos($filename, "php") !== false) {
            require_once(__DIR__."/legacy/".str_replace("php", ".php", $filename));
        }
        else $this->switch_get($filename);
    }

    private function switch_get($path) {
        try {
            if(!isset($_REQUEST['user_id'])) $_REQUEST['user_id'] = 0;
            $_REQUEST['user_id'] = Rights\Main::handle_user_id($_REQUEST['user_id']);
            switch($path) {
                case "create":
                    $return = Tag::create($_REQUEST["name"]);
                break;
                case "update":
                    $tag = new Tag($_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->update($_REQUEST);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "affect":
                    $tag = new Tag($_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->affect($_REQUEST['user_id']);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "unaffect":
                    $tag = new Tag($_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->unaffect($_REQUEST['user_id']);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "delete":
                    $tag = new Tag($_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->remove();
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "migrate":
                    $tag = new Tag((integer) $_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->migrate((integer) $_REQUEST['target']);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "getTagsAShip":
                    $ship = new Ships\models\Ship($_REQUEST['ship']);
                    if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) $return = Tag::get_tags_from_ship($ship);
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "getTagsAShipModel":
                    if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) $return = Tag::get_tags_from_ship_model(new Ships\models\ShipType($_REQUEST['shipModel']));
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "getTagsAShipVariant":
                    if(Rights\Main::user_can("USER_CAN_SEE_JULIET")) $return = Tag::get_tags_from_ship_variant(new Ships\models\ShipVariant($_REQUEST['shipTemplate']));
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "affectShip":
                    $tag = new Tag($_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->affect_ship(new Ships\models\Ship($_REQUEST['ship']));
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "unaffectShip":
                    $tag = new Tag($_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->unaffect_ship(new Ships\models\Ship($_REQUEST['ship']));
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "affectShipModel":
                    $tag = new Tag($_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->affect_ship_model(new Ships\models\ShipType($_REQUEST['shipModel']));
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "unaffectShipModel":
                    $tag = new Tag($_REQUEST['id']);
                    if(Rights\Main::user_can("USER_CAN_ADMIN_TAG", 0, $_REQUEST["id"])) $return = $tag->unaffect_ship_model(new Ships\models\ShipType($_REQUEST['shipModel']));
                    else throw new \Exception("USER_NO_RIGHTS");
                break;
                case "getNameById":
                    $return = Tag::get_name_by_id($_REQUEST['id']);
                break;
                case "searchTags":
                    $return = Tags\helper\Tags::search_tag($_REQUEST['f'],$_REQUEST['all']);
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