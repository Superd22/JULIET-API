<?php namespace JULIET\api\groups\controller;

use \JULIET\api\groups\helper\group as AGroup;
use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Group {

    protected $db;

    public function __construct() {
        $this->db = db::get_mysqli();
    }

    /**
     * push a new group in the db
     *
     * @param string $name the name of the group
     * @param integer $parent the parentId of the group
     * @return id of the newly created group
     */
    public function create($name, $parent = 0) {
        $parent = (integer) $parent;
        $sql = "INSERT INTO star_squad (nom, subsquad) VALUES ('{$this->db->real_escape_string($name)}', {$parent})";
        $this->db->query($sql);

        if($this->db->error) throw new \Exception($this->db->error);
        return $this->db->insert_id;
    }

    public function update(AGroup $group) {

    }

    public function remove() {

    }

    public function assign_member_to_group($member, $group) {

    }

    /**
     * Fetches a group with all its information from the db
     *
     * @param integer $group_id the id to fetch for
     * @return AGroup the fetched group
     */
    public function get_group($group_id) {
        $group_id = (integer) $group_id;

        $sql = "SELECT * FROM star_squad WHERE id='{$group_id}' LIMIT 1";
        $query = $this->db->query($sql);

        if($this->db->error) throw new \Exception($this->db->error);

        return new AGroup($query->fetch_assoc());
    }

    public function get_user_group($user) {

    }

    
}

?>
