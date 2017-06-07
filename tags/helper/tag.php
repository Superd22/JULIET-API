<?php namespace JULIET\api\Tags\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Tag {
    /** id of this tag */
    public $id;
    /** name of this tag */
    public $name;
    /** img of this tag */
    public $img;
    public $type;
    /** @deprecated */
    public $INFO;
    public $count;
    public $parent;
    public $rights_from;
    public $cat = "tag";
    public $herited_from;
    public $restricted;
    /** Array of targets (thing having this tag) */
    public $targets = [];
    
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
            $this->enforce_types();
        }
        else throw new \Exception("NO_TAG_ID");
            
        $this->db = db::get_mysqli();
    }
    
    private function enforce_types() {
        $this->id = (integer) $this->id;
        $this->restricted = (integer) $this->restricted;
    }

    public function get_count() {
        $mysqli = db::get_mysqli();
        $ct = $mysqli->query('SELECT COUNT(*) FROM star_tags_af WHERE tag_id = "'.$this->id.'" ');
        $ct = $ct->fetch_assoc();
        
        $this->count = $ct["COUNT(*)"];
        
        return $this->count;
    }

    public function has_heritage() {
        return $this->herited_from != null;
    }

    public function set_heritage($id, $type) {
        $this->herited_from = array( "id" => (integer) $id, "target_type" => $type );
    }
    
    public static function get_all_tags($user_id = null, $ship_id = null, $ship_type_id = null, $ship_template_id = null, $ressource_id = null) {
        Phpbb::make_phpbb_env();
        global $user;
        if($userid == 1) $userid = $user->data['user_id'];
        $where = "";
        $herit = null;
        $mysqli = db::get_mysqli();
        if ($user_id > 0) {
            $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE user_id='".(integer) $userid."')";
        }
        elseif($ship_id > 0) {
            $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE ship_id='".(integer) $ship_id."')";
            $herit = new \JULIET\api\Ships\helpers\Ship($ship_id);
        }
        elseif($ship_type_id > 0) {
            $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE shipType_id='".(integer) $ship_type_id."')";
            $herit = new \JULIET\api\Ships\helpers\ShipType($ship_type_id);
        }
        elseif($ship_template_id > 0) {
            $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE ship_variant_id='".(integer) $ship_template_id."')";
            $herit = new \JULIET\api\Ships\helpers\ShipVariant((integer) $ship_template_id);
        }
        elseif($ressource_id > 0) {
            $where = "HAVING id in (SELECT tag_id FROM star_tags_af WHERE ressource_id='".(integer) $ressource_id."')";
        }
        $return = [];
        
        $tags = $mysqli->query('SELECT * FROM star_tags '.$where.' ORDER BY id DESC');

        // [HERITAGE] LAND
        if($herit) {
            $herited_tags = $herit->get_herited_tags();
            foreach($herited_tags as $tag) {
                if($tag && $tag->id)
                $return[$tag->id] = $tag;
            }
        }
        
        while($list = $tags->fetch_assoc()) {
            $tag = new Tag($list);
            // if($tag->get_count() > 0) $return[] = $tag;
            // Prevent doubles
            $return[$tag->id] = $tag;
        }

        return array_values($return);
    }


    
    public static function get_tags_from_ship(\JULIET\api\Ships\models\Ship $ship) {
        $shipid = (integer) $ship->id;
        return self::get_all_tags(null,$shipid);
    }
    
    public static function get_tags_from_ship_variant(\JULIET\api\Ships\models\ShipVariant $ship) {
        $shipid = (integer) $ship->id;
        return self::get_all_tags(null,null,null,$shipid);
    }
    
    public static function get_tags_from_ship_model(\JULIET\api\Ships\models\ShipType $ship) {
        $shiptypeid = (integer) $ship->id;
        return self::get_all_tags(null,null,$shiptypeid);
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
        
        $sql = "SELECT * FROM star_tags ".$where." LIMIT 1";
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
        $mysqli =  db::get_mysqli();
        
        $sql = "SELECT rights_from FROM star_tags WHERE id = '{$tag_id}' LIMIT 1";
        $ct = $mysqli->query($sql);
        $ct = $ct->fetch_assoc();
            
        if($ct["rights_from"] > 0) return $ct["rights_from"];

        return false;
    }
    
    public static function user_has_tag($user_id, $tag_id, $tag_category = "tag") {
        $user_id = (integer) $user_id;
        $tag_id = (integer) $tag_id;
        
        $mysql =  db::get_mysqli();
        $sql = "SELECT COUNT(*) FROM star_tags_af WHERE user_id='{$user_id}' AND tag_id='{$tag_id}' LIMIT 1";
        $ct = $mysql->query($sql);
        $ct = $ct->fetch_assoc();
        if($ct["COUNT(*)"] > 0) return true;

        return false;
    }
    
    public static function create($tag_name, $tag_category = "tag") {
        if(empty($tag_name)) throw new \Exception("NEED_TAG_NAME");
        $mysql =  db::get_mysqli();
        
        // Nouveau TAG.
        $title = $mysql->real_escape_string($tag_name);
        $insert = $mysql->query("INSERT INTO star_tags (name, img)
        VALUES('".$title."','')");
        
        $select = $mysql->query("SELECT * FROM star_tags WHERE name='".$title."'");
        $tg = $select->fetch_assoc();
        
        return new Tag($tg);
    }
    
    
    /**
    * Affect the current Tag to the user
    */
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
    
    /**
    * Affect to ship
    */
    public function affect_ship(JULIET\api\Ships\models\ShipType $ship) {
        $ship_id = (integer) $ship->id;
        
        if($this->is_normal_tag() && $ship_id > 0) {
            $sql = "INSERT INTO star_tags_af
            (tag_id, ship_id)
            VALUES ('{$this->id}', '{$ship_id}')";
            return $this->db->query($sql);
        }
        
        return false;
    }
    
    /**
    * Un affects current tag to the ship
    */
    public function unaffect_ship(JULIET\api\Ships\models\ShipType $ship) {
        $ship_id = (integer) $ship->id;
        if($this->is_normal_tag()) {
            $sql = "DELETE FROM star_tags_af WHERE ship_id='{$ship_id}' AND tag_id='{$this->id}'";
            return $this->db->query($sql);
        }
    }

    public function affect_ship_template(JULIET\api\Ships\models\ShipVariant $ship) {
        $ship_id = (integer) $ship->id;
 
        if($this->is_normal_tag() && $ship_id > 0) {
            $sql = "INSERT INTO star_tags_af
            (tag_id, ship_variant_id)
            VALUES ('{$this->id}', '{$ship_id}')";
            return $this->db->query($sql);
        }
        
        return false;
    }
    
    
    public function unaffect_ship_template(JULIET\api\Ships\models\ShipVariant $ship) {
        $ship_id = (integer) $ship->id;
        if($this->is_normal_tag()) {
            $sql = "DELETE FROM star_tags_af WHERE ship_variant_id='{$ship_id}' AND tag_id='{$this->id}'";
            return $this->db->query($sql);
        }
    }

    /**
    * Affect to ship
    */
    public function affect_ship_model(JULIET\api\Ships\models\Ship $ship) {
        $ship_id = (integer) $ship->id;
        
        if($this->is_normal_tag() && $ship_id > 0) {
            $sql = "INSERT INTO star_tags_af
            (tag_id, shipType_id)
            VALUES ('{$this->id}', '{$ship_id}')";
            return $this->db->query($sql);
        }
        
        return false;
    }
    
    /**
    * Un affects current tag to the ship
    */
    public function unaffect_ship_model(JULIET\api\Ships\models\Ship $ship) {
        $ship_id = (integer) $ship->id;
        if($this->is_normal_tag()) {
            $sql = "DELETE FROM star_tags_af WHERE shipType_id='{$ship_id}' AND tag_id='{$this->id}'";
            return $this->db->query($sql);
        }
    }
    
    public function is_normal_tag() {
        return ($this->cat == "tag");
    }
    

    /**
    * Get all the info about this tag + who has it
    * @param $tag_name the name of the tag to fetch
    * @param $all get all type of ressources (only user if false)
    * @return a Tag with ressources info, or null.
    */
    public static function get_tag_info($tag_name, $all = false) {
        if(!is_string($tag_name) || empty($tag_name)) throw new \Exception("NO VALID TAG NAME");

        $mysqli =  db::get_mysqli();
        $tags = $mysqli->query('SELECT * FROM star_tags WHERE name="'.$mysqli->real_escape_string($tag_name).'" LIMIT 1');
        $tag = $tags->fetch_assoc();

        // Holds our basics information.
        $rTag = new Tag($tag);
        // Get our ressources
        $rTag->fetch_owner_of_this($all);

        return $rTag;
    }

    /**
    * Get all the ressources who posses this tag
    * @param $all get all type of ressources (only users if false)
    */
    public function fetch_owner_of_this($all = false) {
        $mysqli =  db::get_mysqli();

        // Get everything that is *directly* tied to us
        $sql = "SELECT * FROM star_tags_af WHERE tag_id={$this->id}";
        $query = $mysqli->query($sql);

        while($child = $query->fetch_assoc()) {
            $id = $type = $img = $name = null;
            if($this->should_add_child($child, $all)) {
                $type = $this->type_of_child($child);
                switch($type) {
                    case "user":
                        $id = $child['user_id'];
                        $info = \JULIET\API\Common\Main::getUsersById($id);
                        $img = $info['avatar'];
                        $name = $info['username'];
                    break;
                    case "ship":
                        $id = $child['ship_id'];
                        $ship = new \JULIET\api\Ships\helpers\Ship($id);
                        $info = $ship->get_info();
                        $img = $info->ico;
                        $name = $info->name;
                    break;
                    case "ship_type":
                        $id = $child['shipType_id'];
                        $ship = new \JULIET\api\Ships\helpers\ShipType($id);
                        $info = $ship->get_info();
                        $img = $info->ico;
                        $name = $info->name;
                    break;
                }

                if($id != null)
                $this->targets[] = new \JULIET\api\Tags\model\TagTarget($id, $info, $type, $img, $name);
            }
        }

    }

    private function should_add_child($child, $all) {
        return $all || !empty($child['user_id']);
    }
    
    private function type_of_child($child) {
        if(!empty($child['user_id'])) return "user";
        if(!empty($child['ship_id'])) return "ship";
        if(!empty($child['shipType_id'])) return "ship_type";
        if(!empty($child['ship_variant_id'])) return "ship_variant";
        if(!empty($child['ressource_id'])) return "ressource";
    }
    
}