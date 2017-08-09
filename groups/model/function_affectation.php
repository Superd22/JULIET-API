<?php namespace JULIET\api\groups\model;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\API\Common\Model\JulietModel;

class GroupFunctionAffectationModel extends JulietModel {
    public $id, $fn_id, $user_id, $group_id;
    
    protected $_type = [
        'id' => 'integer',
        'fn_id' => 'integer',
        'user_id' => 'integer',
        'group_id' => 'integer',
    ];
    
    function __construct($id) {
        if(is_numeric($id)) $id = ['id' => $id];
        parent::__construct($id);

        if(!$this['id'] || !($this['id'] > 0)) throw new \Exception("Wrong ID for GroupFunctionAffectationModel");
    }
    
    
    
    
}