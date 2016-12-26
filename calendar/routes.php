<?php namespace JULIET\api; 

    require_once(__DIR__."/calendar.php");
    
    $r3->any("/Calendar/*", 'JULIET\api\Calendar');
?>