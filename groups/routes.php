<?php namespace JULIET\api; 
    require_once(__DIR__."/_.routes.php");
    
    $r3->any("/Groups/*", 'JULIET\api\groups\Groups');
?>