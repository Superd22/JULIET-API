<?php namespace JULIET\api\groups\controller;

use \JULIET\api\groups\helper\AGroup as Group;
use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Main {

    protected $db;

    public function __construct() {
        $this->db = db::get_mysqli();
    }

    public function create($group) {

    }

    public function update() {

    }

    public function remove() {

    }

    public function assign_member_to_group($member, $group) {

    }

    public function get_group() {

    }

    public function get_user_group($user) {

    }

    
}

?>
