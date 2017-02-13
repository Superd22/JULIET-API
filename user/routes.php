<?php namespace JULIET\api; 

    require_once(__DIR__."/Users.php");
    require_once(__DIR__."/Ranks.php");
    
    $r3->any("/Users/*", 'JULIET\api\Users');
    $r3->any("/Ranks/*", 'JULIET\api\Ranks');
?>