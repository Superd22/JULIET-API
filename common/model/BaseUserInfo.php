<?php namespace JULIET\API\Common\Model;

use JULIET\api\Phpbb;
use JULIET\API\db;

class BaseUserInfo extends JulietModel {
    public $username;
    public $id;
    public $avatar;

    protected $_type = [
        'id' => 'integer'
    ];

    function __construct($id) {
        parent::__construct($id);
        if(!$this['id'] || !($this['id'] > 0)) throw new \Exception("no ID in BaseUserInfo");
    }

}