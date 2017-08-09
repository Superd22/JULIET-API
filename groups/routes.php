<?php namespace JULIET\api; 
    require_once(__DIR__."/_.routes.php");
    
    $r3->any("/Groups/Group/Function/*", 'JULIET\api\groups\GroupFunction');
    $r3->any("/Groups/Group/*", 'JULIET\api\groups\Group');
    $r3->any("/Groups/*", 'JULIET\api\groups\Groups');
?>