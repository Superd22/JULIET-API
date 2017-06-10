<?php namespace JULIET\api; 


    require_once(__DIR__."/helpers/Ship.php");
    require_once(__DIR__."/helpers/ShipType.php");
    require_once(__DIR__."/helpers/ShipVariant.php");
    require_once(__DIR__."/helpers/Hangar.php");
    require_once(__DIR__."/helpers/HangarPlayer.php");
    require_once(__DIR__."/helpers/CrewCompliment.php");
    require_once(__DIR__."/helpers/CrewMember.php");
    require_once(__DIR__."/helpers/CrewPosition.php");

    require_once(__DIR__."/models/Ship.php");
    require_once(__DIR__."/models/ShipType.php");
    require_once(__DIR__."/models/ShipVariant.php");
    require_once(__DIR__."/models/CrewCompliment.php");
    require_once(__DIR__."/models/CrewMember.php");
    require_once(__DIR__."/models/CrewPosition.php");

    require_once(__DIR__."/rights/Ship.right.php");
    require_once(__DIR__."/rights/ShipModel.right.php");
    require_once(__DIR__."/rights/ShipTemplate.right.php");

    require_once(__DIR__."/Ships.php");
    require_once(__DIR__."/ShipTemplates.php");
    require_once(__DIR__."/ShipTypes.php");
    require_once(__DIR__."/ShipCrew.php");
?>