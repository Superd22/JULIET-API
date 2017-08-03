<?php namespace JULIET\api; 
    require_once(__DIR__."/_.routes.php");
    
    $r3->any("/Groups/*", 'JULIET\api\groups\Groups');
    $r3->any("/Groups/Group/*", 'JULIET\api\groups\Group');
?>