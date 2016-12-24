<?php namespace JULIET\api; 

    require_once(__DIR__."/Tags.php");
    
    $r3->any("/Tags/*", 'JULIET\api\Tags');
?>