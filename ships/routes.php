<?php namespace JULIET\api; 

    require_once(__DIR__."/_.routes.php");
    
    $r3->any("/Ships/*", 'JULIET\api\Ships');
    $r3->any("/Ships/Template/*", 'JULIET\api\ShipTemplates');
    $r3->any("/Ships/Model/*", 'JULIET\api\ShipType');
?>