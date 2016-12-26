<?php namespace JULIET\api; 

    require_once(__DIR__."/Users.php");
    
    $r3->any("/Users/*", 'JULIET\api\Users');
?>