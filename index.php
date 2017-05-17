<?php namespace JULIET\api;

require __DIR__ . '/vendor/autoload.php';

require_once("api-router.php");
require_once("required/db.php");
require_once("required/phpbb.php");
require_once("required/juliet.php");
require_once("required/response.php");
require_once("config/ts3.conf.php");
require_once("config/mysql.conf.php");

header("Access-Control-Allow-Origin: https://juliet.starcitizen.fr");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers: Content-Type');

error_reporting(-1);
ini_set("display_errors", "1");
$r3 = APIRouter::get_router();

$r3->any("/", function() {
    echo "ok";
});


require_once("rights/routes.php");
require_once("tags/routes.php");
require_once("ts3/routes.php");
require_once("calendar/routes.php");
require_once("user/routes.php");
require_once("ships/routes.php");
require_once("groups/routes.php");
require_once("common/routes.php");    
?>