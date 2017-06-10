<?php namespace JULIET\api\Ships\models;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class CrewMember {
    
    public $template_id;
    public $id;
    public $user_id;
    public $job_id;
    public $user;
    
    public function __construct($crew) {
        if($crew !== null && $crew['template_id'] !== null) {
            $this->id = (integer) $crew['id'];
            $this->template_id = (integer) $crew['template_id'];
            $this->user_id = (integer) $crew['user_id'];
            $this->job_id = (integer) $job_id['user_id'];
            $this->user = $user;
        }
        else throw new \Exception("INVALID CREW MEMBER TARGET");
    }
}