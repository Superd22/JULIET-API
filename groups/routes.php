<?php namespace JULIET\api; 
    require_once(__DIR__."/Groups.php");
    
    $r3->any("/Groups/*", 'JULIET\api\groups\Groups');
?>