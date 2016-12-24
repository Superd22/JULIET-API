<?php namespace JULIET\api; 

    require_once(__DIR__."/rights.php");
    
    $r3->any("/Rights/*", 'JULIET\api\Rights');
?>