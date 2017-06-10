<?php namespace JULIET\api\Ships\models;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class CrewCompliment {
    public $template_id;
    public $positions;
    public $crew;
    
    public function __construct($crew) {
        if($crew !== null && is_numeric($crew) && (integer) $crew > 0) {
            $this->template_id      = (integer) $crew;
        }
        else throw new \Exception("CrewCompliment INVALID TARGET");
    }

    public function set_positions($positions) {
        $this->positions = $positions;
    }

    public function set_crew($crew) {
        $this->crew = $crew;
    }

}