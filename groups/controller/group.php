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

    public function update_legacy() {
        $sql = "SELECT id, members, p_members FROM star_squad";
        
        $q = $this->db->query($sql);
        while($g = $q->fetch_assoc()) {
            $members = explode(",", $g['members']);
            $p_members = explode(",", $g['p_members']);
            foreach($members as $member){
                $this->db->query(" INSERT INTO star_squad_af (group_id, user_id, validated) 
                VALUES ({$g['id']},{$member},1)");}

            foreach($p_members as $member)
                $this->db->query(" INSERT INTO star_squad_af (group_id, user_id) 
                VALUES ({$g['id']},{$member})");
        }
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
        
        $group = self::getExtendedGroupFromBaseData($query->fetch_assoc());

        return $group;
    }

    public function getExtendedGroupFromBaseData($baseData) {
        $group = new AGroup($baseData);
        $group['affectations'] = $this->get_group_affected($group['id']);

        return $group;
    }
    
    /**
     * Returns all the things affected to a given group
     * (users + ship + ressources)
     *
     * @param integer $group_id target group
     * @return [users => user[], ships => ship[], ressources => ressource[]]
     */
    public function get_group_affected($group_id) {
        $group_id = (integer) $group_id;
        $users = $ships = $ressources = [];

        // Get all the affectations for this group
        $sql = "SELECT * from star_squad_af WHERE group_id={$group_id}";
        $q = $this->db->query($sql);

        if($q)
        while($affected_thing = $q->fetch_assoc()) {
            
            if($affected_thing['user_id'] > 0) {
                $users[] = GroupAffectation::getUserAffectation($affected_thing);
            }

            else if($affected_thing['ship_id'] > 0) {
                $ship = new \JULIET\api\Ships\helpers\Ship($id);
                $ship->get_info();
                $ships[] = $ship->get_info();
            }

            else if($affected_thing['ressource_id'] > 0) {
                /**
                 * @todo 
                 */
            }

            // If we can't determine what type of affectation this is
            else throw new \Exception("undefined affectation type in get_group_affected()");
        }

        return ["users" => $users, "ships" => $ships, "ressources" => $ressources];
    }

    public function get_user_group($user) {

    }

    
}

?>
