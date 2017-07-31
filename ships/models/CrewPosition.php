<?php namespace JULIET\api\Ships\models;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class CrewPosition {
    
    public $template_id;
    public $id;
    public $name;
    public $parent;
    
    public function __construct($crew) {
        if($crew !== null && $crew['template_id'] !== null) {
            $this->id = (integer) $crew['id'];
            $this->template_id = (integer) $crew['template_id'];
            $this->parent = (integer) $crew['parent'];
            $this->name = $crew['name'];
        }
        else throw new \Exception("INVALID CREW POSITION TARGET");
    }
}