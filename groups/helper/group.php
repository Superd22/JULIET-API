<?php namespace JULIET\api\groups\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Group {
    protected $id;
    protected $type, $nom, $logo, $ban, $members, $max_members, $recruit;
    protected $abr, $description, $perm, $subsquad, $pending;

    function __construct($id) {
        if(is_numeric($id)) $this->id = $id;
        elseif(isset($id['id']) && isset($id["nom"])) 
            foreach($id as $pp => $val)
                $this->{$pp} = $val; 
    }

    


}