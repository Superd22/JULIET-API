<?php namespace JULIET\api; 
    require_once(__DIR__."/TS3.php");
    
    error_reporting(-1);
    $r3->any("/TS3/*", 'JULIET\api\TS3');
?>