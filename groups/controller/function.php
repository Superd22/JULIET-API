<?php namespace JULIET\api\groups\controller;

use \JULIET\api\groups\helper\group as AGroup;
use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class GroupFunction {
    /**
    * @var mysqli
    */
    protected $db;
    
    public function __construct() {
        $this->db = db::get_mysqli();
    }
    
    /**
    * Create a new function for an user in a given group
    *
    * @param integer groupId target group
    * @param string name function name
    * @param integer[] function rights
    * @return integer the id of the newly created function
    */
    public function create($groupId, $name = "", $rights = false) {
        $groupId = (integer) $groupId;
        
        $extraCols = $extraVals = "";
        
        if($name) {
            $name =  $this->db->real_escape_string($name);
            $extraCols .= ', name';
            $extraVals .= ", '{$name}'";
        }
        
        if($rights && is_array($rights)) {
            $rights = $this->db->real_escape_string(json_encode($rights));
            $extraCols .= ', rights';
            $extraVals .= ", '{$rights}'";
        }
        
        
        $sql = "INSERT INTO star_squad_fn (group_id {$extraCols})
        VALUES ({$groupId} {$extraVals})";

        $query = $this->db->query($sql);

        if($this->db->error) throw new \Exception($this->db->error);
        return $this->db->insert_id;      
    }

    /**
     * Removes a function and every associated member
     *
     * @param integer $fnId
     * @return boolean
     */
    public function delete($fnId) {
        $fnId = (integer) $fnId;

        $sql = "DELETE * FROM star_squad_fn WHERE id={$fnId}";

        $q = $this->db->query($sql);

        if($this->db->error) throw new \Exception($this->db->error);
        return true;      
    }

    /**
     * Update a function name and or rights
     * @param integer fnId function id
     * @param string name function name
     * @param array rights function rights
     * @return void
     */
    public function update($fnId, $name = false, $rights = false) {
        // Nothing to update
        if($name === false && $rights === false) return true;

        $nameSql = $rightsSql = "";
        $fnId = (integer) $fnId;
        if($name !== false) $nameSql = " AND name='{$this->db->real_escape_string($name)}'";
        if($rights !== false) {
            $rt = json_encode($rights);
            $rightsSql= " AND rights='{$this->db->real_escape_string($rt)}'";
        }

        

        $sql = "UPDATE FROM star_squad_fn SET id={$fnId} {$nameSql} {$rightsSql} WHERE id={$fnId} LIMIT 1";

        $q = $this->db->query($sql);
        if($this->db->error) throw new \Exception($this->db->error);
        return true;
    }

    public function affect($fnId, $userId) {
        $fnId = (integer) $fnId;
        $userId = (integer) $userId;

        $sql = "INSERT INTO star_squad_fn_af (fn_id, user_id, group_id)
        VALUES ({$fnId}, {$userId}, (SELECT group_id FROM star_squad_fn WHERE id={$fn_id}))";

        
    }

    public function de_affect($fnId, $userId) {
        $fnId = (integer) $fnId;
        $userId = (integer) $userId;

    }
}