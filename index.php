<?php namespace JULIET\api;
    require __DIR__ . '/vendor/autoload.php';
    
    require_once("api-router.php");
    require_once("required/db.php");
    require_once("required/phpbb.php");
    require_once("required/juliet.php");
    require_once("required/response.php");

    header("Access-Control-Allow-Origin: https://juliet.starcitizen.fr");
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Headers: Content-Type');

    error_reporting(-1);
    $r3 = APIRouter::get_router();

    $r3->any("/", function() {
        echo "ok";
    });


    require_once("rights/routes.php");
    require_once("tags/routes.php");
?>