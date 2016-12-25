<?php namespace JULIET\api\Tags\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Tag {
    public $id;
    public $name;
    public $img;
    public $type;
    public $INFO;
    public $count;
    public $parent;
    public $rights_from;
    public $cat = "tag";
    
    
    protected $tag_id;
    protected $tag_category;
    private $db;
    
    public function __construct($tag_id, $tag_category = "tag") {
        if(is_numeric($tag_id)) {
            $this->id = $tag_id;
            $this->tag_category = $tag_category;
        }
        elseif(isset($tag_id)) {
            foreach($tag_id as $pp => $val)
            if(isset($pp) && isset($val)) $this->{$pp} = $val;
        }
        else throw new \Exception("NO_TAG_ID");
            
        $this->db = db::get_mysqli();
    }
    
    public function get_count() {
        $ct = $mysqli->query('SELECT COUNT(*) FROM star_tags_af WHERE tag_id = "'.$this->id.'" ');
        $ct = $ct->fetch_assoc();
        
        $this->count = $ct["COUNT(*)"];
        
        return $this->count;
    }
    
    public static function get_all_tags($user_id = null, $ship_id = null) {
        Phpbb::make_phpbb_env();
        global $user;
        if($userid == 1) $userid = $user->data['user_id'];
        $where = "";
        
        if($shipid > 0) {
            $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE ship_id='".$userid."')";
        }
        elseif ($userid > 0) {
            $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE user_id='".$userid."')";
        }
        
        $return = [];
        
        $tags = $mysqli->query('SELECT * FROM star_tags '.$where.' ORDER BY id DESC');
        while($list = $tags->fetch_assoc()) {
            $tag = new Tag($list);
            if($tag->get_count() > 0) $return[] = $tag;
        }
        
        return $return;
    }

    public static function get_name_by_id($id) {
        if(!($id > 0)) throw new \Exception("INVALID_ID");
        $id = (integer) $id;
        $mysql =  db::get_mysqli();
        
        $sql = "SELECT name from star_tags WHERE id='{$id}'";
        $test = $mysql->query($sql);

        return $test->fetch_assoc();
    }

    public static function get_tags_from_user($user_id) {
        $user_id = (integer) $user_id;
        return get_all_tags($user_id);
    }
    
    public function remove() {
        if($this->is_normal_tag()) {
            // Remove TAG
            $sql = "DELETE FROM star_tags WHERE id='{$this->id}'";
            $this->db->query($sql);
            
            // Remove affectations
            $sql = "DELETE FROM star_tags_af WHERE tag_id='{$this->id}'";
            $this->db->query($sql);
            
            // Remove parent / rights from / gettable from
            $this->update_heritage();

            return true;
        }
    }
    
    private function update_heritage($new_target = 0) {
        $new_target = (integer) $new_target;
        
        $sql0 = "UPDATE star_tags SET parent = '{$new_target}' WHERE parent='{$this->id}'";
        $sql1 = "UPDATE star_tags SET rights_from = '{$new_target}' WHERE rights_from='{$this->id}'";
        //$sql2 = "UPDATE star_tags SET get_from = '{$new_target}' WHERE get_from='{$this->id}'";
        
        $this->db->query($sql0);
        $this->db->query($sql1);
        //$this->db->query($sql2);
    }
    
    public function migrate($new_id) {
        $new_id = (integer) $new_id;
        if($new_id > 0) {
            if($this->is_normal_tag()) {
                // Remove old tag
                $sql = "DELETE FROM star_tags WHERE id='{$this->id}'";
                $this->db->query($sql);
                
                // Switch affectations
                $sql = "UPDATE star_tags_af SET tag_id='{$new_id}' WHERE tag_id='{$this->id}'";
                $this->db->query($sql);
                
                $this->update_heritage($new_id);
                return self::get_single_tag((integer) $new_id);
            }
        }
        else throw new \Exception("NEED_MIGRATE_TARGET");       
    }

    public static function get_single_tag($tag) {
        $mysql =  db::get_mysqli();
        if(is_numeric($tag)) $where = "WHERE id='{$tag}'";
        elseif(is_string($tag)) $where = "WHERE name='{$mysql->real_escape_string($tag)}'";
        else {throw new \Exception("WRONG_TAG_ARG"); return false;}

        $sql = "SELECT * FROM star_tags ".$where;
        $q = $mysql->query($sql);

        return new Tag($q->fetch_assoc());
    }
    
    public function update($new) {
        if($this->is_normal_tag()) {
            $name = $this->db->real_escape_string($new["name"]);
            $img = $this->db->real_escape_string($new["img"]);
            $restricted = (integer) $new["restricted"];
            $type = (integer) $new["type"];
            $parent = (integer) $new["parent"];
            $rights_from = (integer) $new["rights_from"];
            //$get_from = (integer) $new["get_from"];
            
            $sql = "UPDATE star_tags
            SET
            name='{$name}',
            img='{$img}',
            restricted='{$restricted}',
            type='{$type}',
            parent='{$parent}',
            rights_from='{$rights_from}'
            WHERE id = '{$this->id}'
            ";
            return $this->db->query($sql);
        }
        else throw new \Exception("CANT_UPDATE_SPECIAL_TAGS");
        }
    
    public static function get_rights_from($tag_id)  {
        $mysql =  db::get_mysqli();
        
        if($this->is_normal_tag()) {
            $sql = "SELECT rights_from FROM star_tags WHERE id = '{$tag_id}' LIMIT 1";
            $ct = $mysqli->query($sql);
            $ct = $ct->fetch_assoc();
            
            if($ct["rights_from"] > 0) return $ct["rights_from"];
        }
        return false;
    }
    
    public static function user_has_tag($user_id, $tag_id, $tag_category = "tag") {
        $user_id = (integer) $user_id;
        $tag_id = (integer) $tag_id;
        
        if($this->is_normal_tag()) {
            $mysql =  db::get_mysqli();
            $sql = "SELECT COUNT(*) FROM star_tags_af WHERE user_id='{$user_id}' AND tag_id='{$tag_id}' LIMIT 1";
            $ct = $mysqli->query($sql);
            $ct = $ct->fetch_assoc();
            if($ct["COUNT(*)"] > 0) return true;
        }
        return false;
    }
    
    public static function create($tag_name, $tag_category = "tag") {
        if(empty($tag_name)) throw new \Exception("NEED_TAG_NAME");
        $mysql =  db::get_mysqli();
        
        // Nouveau TAG.
        $title = $mysql->real_escape_string($tag_name);
        $insert = $mysql->query("INSERT INTO star_tags (name, img)
        VALUES('".$title."','')");
        
        $select = $mysql->query("SELECT id FROM star_tags WHERE name='".$title."'");
        $id = $select->fetch_assoc();
        
        return new Tag($id['id']);
    }
    
    public function affect($user_id) {
        $user_id = (integer) $user_id;

        if($this->is_normal_tag() && $user_id > 0) {
            $sql = "INSERT INTO star_tags_af
            (tag_id, user_id)
            VALUES ('{$this->id}', '{$user_id}')";
            return $this->db->query($sql);
        }
        return false;
    }
    
    public function unaffect($user_id) {
        $user_id = (integer) $user_id;
        if($this->is_normal_tag()) {
            $sql = "DELETE FROM star_tags_af WHERE user_id='{$user_id}' AND tag_id='{$this->id}'";
            return $this->db->query($sql);
        }
    }
    
    public function is_normal_tag() {
        return ($this->cat == "tag");
    }
    
    
    
}