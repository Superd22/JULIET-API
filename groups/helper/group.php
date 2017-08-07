<?php namespace JULIET\api\groups\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\API\Common\Model\JulietModel;

class Group extends JulietModel {
    public $id;
    public $type, $nom, $logo, $ban, $members, $max_members, $recruit;
    public $abr, $description, $perm, $subsquad, $pending;
    
    protected $_type = [
        'id' => 'integer',
        'recruit' => 'boolean',
    ];
    
    function __construct($id) {
        if(is_numeric($id)) $id = ['id' => $id];
        parent::__construct($id);

        if(!$this['id'] || !($this['id'] > 0)) throw new \Exception("Wrong ID for GroupModel");
    }
    
    
    
    
}