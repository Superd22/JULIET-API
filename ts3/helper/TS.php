<?php namespace JULIET\api\TS3\helper;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\JULIET;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\api\conf\TS3Config;
require_once(__DIR__."/ts3admin.php");
class TS {
    function __construct() {
        $this->tsAdmin = new \ts3admin(TS3Config::$ts3_ip, TS3Config::$ts3_queryport);
    }
    
    public function get_user_status($user_id = 0) {
        $user_id = (integer) Rights::handle_user_id($user_id);
        
        if($this->tsAdmin->getElement('success', $this->tsAdmin->connect())) {
            if(!empty($ts3_user)) { $this->tsAdmin->login(TS3Config::$ts3_user, TS3Config::$ts3_pass); }
            $this->tsAdmin->selectServer(TS3Config::$ts3_port);
            // Verification ID FORUM
            $search = $this->tsAdmin->customsearch('id_forum',$user_id);
            
            // Verification GROUPES TS
            $groups = $this->tsAdmin->serverGroupsByClientID($search['data'][0]['cldbid']);
            foreach($groups['data'] as $group) $sgp[] = $group['sgid'];
            
            // Si le joueur est présent dans le groupe ET a une ident fofo.
            if($search['data'][0]['cldbid'] != '' && !!array_intersect(array(11,19,20,21), $sgp)) {
                $user_TS = $this->tsAdmin->clientDbInfo($search['data'][0]['cldbid']);
                $user_TS = $user_TS['data'];
                
                $return = array("STATUS" => true, "tsUser" => $user_TS);
            }
            else {
                $test = JULIET::get_main_fleet_info($user_id);
                
                $d = 11;
            switch($test["fleet"]) {case 1 : $d = 20;	break;case 2 : $d = 19;	break;case 3 : $d = 21;	break;}
                $TalkToken = $this->tsAdmin->tokenAdd('0', $d, '0', $description ='JULIET AUTO ADD', array("id_forum" => $user_id));
                
                if ($TalkToken['success'] == 1 && $TalkToken['data']['token'] != '') $return = array("STATUS" => false, "token" => $TalkToken['data']['token']);
                else throw new \Exception("TS3_CANT_GENERATE_TOKEN");
                }
        }
        else throw new \Exception("TS3_SERVER_ERROR");
            return $return;
    }
    
    public function get_server_status() {
        
    }
    
    public function unregister_user($user_id = 0) {
        $user_id = (integer) Rights::handle_user_id($user_id);
        if($this->tsAdmin->getElement('success', $this->tsAdmin->connect())) {
            $this->tsAdmin->login(TS3Config::$ts3_user, TS3Config::$ts3_pass);
            $this->tsAdmin->selectServer(TS3Config::$ts3_port);

            $search = $this->tsAdmin->customsearch('id_forum',$user_id);
            foreach([9,23,10,22,11,19,20,21] as $gid)
                $this->tsAdmin->serverGroupDeleteClient($gid, $search['data'][0]['cldbid']);
            
            $del = $this->tsAdmin->clientDbDelete($user_id);
            if($del) return true;
            throw new \Exception("UNKNOWN_TS3_ERROR");
        }
    }
    
    
    
}