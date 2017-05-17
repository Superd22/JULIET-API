<?php namespace JULIET\api; 

    require_once(__DIR__."/Ships.php");
    
    $r3->any("/Ships/*", 'JULIET\api\Ships');
?>