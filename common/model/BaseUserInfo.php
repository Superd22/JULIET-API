<?php namespace JULIET\API\Common\Model;

use JULIET\api\Phpbb;
use JULIET\API\db;

class BaseUserInfo {
    public $username;
    public $id;
    public $avatar;

    public function __construct($user) {
        if($user['id'] && $user['id'] > 0) {
            $this->username = $user['username'];
            $this->id = (integer) $user['id'];
            $this->avatar = $user['avatar'];
        }
    }
}