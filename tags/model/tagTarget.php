<?php namespace JULIET\api\Tags\model;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class TagTarget {
    public $id;
    public $type;
    public $img;
    public $name;
    public $target;
    
    public function __construct($id, $target, $type="user", $img="", $name="") {
        $this->id     = (integer) $id;
        $this->type   = $type;
        $this->img    = $img;
        $this->name   = $name;
        $this->target = $target;
    }
}