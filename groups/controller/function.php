<?php namespace JULIET\api\groups\controller;

use \JULIET\api\groups\helper\group as AGroup;
use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\api\groups\model\GroupFunctionModel;
use JULIET\api\groups\model\GroupFunctionAffectationModel;

class GroupFunction {
    /**
    * @var mysqli
    */
    protected $db;
    
    public function __construct() {
        $this->db = db::get_mysqli();
    }
    

    public function view($fnId) {
        $fnId = (integer) $fnId;
        $sql = "SELECT * FROM star_squad_fn WHERE id='{$fnId} LIMIT 1";
        $q = $this->db->query($sql);

        $fn = null;
        if($q) {
            $fn = new GroupFunctionModel($q->fetch_assoc());

            $fn['rights'] = json_decode($fn['rights']);
            $fn['owners'] = $this->get_affectation_of($fnId);
        }
        return $fn;
    }

    public function get_affectation_of($fnId) {
        $fnId = (integer) $fnId;

        $sql = "SELECT * FROM star_squad_fn_af WHERE fn_id='{$fnId}";

        $q = $this->db->query($sql);
        $ret = [];
        if($q)
        while($af = $q->fetch_assoc()) {
            $ret[] = new GroupFunctionAffectationModel($af);
        }

        return $ret;
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

    /**
     * Affects a a player to a given function
     *
     * @param integer $fnId target function 
     * @param integer $userId target iser
     * @return integer id of the affectation
     */
    public function affect($fnId, $userId) {
        $fnId = (integer) $fnId;
        $userId = (integer) $userId;

        $sql = "INSERT INTO star_squad_fn_af (fn_id, user_id, group_id)
        VALUES ({$fnId}, {$userId}, (SELECT group_id FROM star_squad_fn WHERE id={$fn_id}))";

        $q = $this->db->query($sql);
        
        if($this->db->error) throw new \Exception($this->db->error);
        return $this->db->insert_id;      
    }

    /**
     * De-affects a player from a given function
     *
     * @param integer $fnId target function
     * @param integer $userId the user to remove from the function
     * @return void
     */
    public function de_affect($fnId, $userId) {
        $fnId = (integer) $fnId;
        $userId = (integer) $userId;
        
        $sql = "DELETE FROM star_squad_fn_af WHERE id={$fn_id} AND user_id={$userId}";

        $q = $this->db->query($sql);
        if($this->db->error) throw new \Exception($this->db->error);
        return true;
    }
}