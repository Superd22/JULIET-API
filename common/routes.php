<?php namespace JULIET\api; 

    require_once(__DIR__."/common.php");
    
    $r3->any("/Common/*", 'JULIET\api\Common');
?>