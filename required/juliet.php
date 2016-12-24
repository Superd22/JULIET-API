<?php namespace JULIET\api;

use JULIET\api\db;
class JULIET {
    public function __construct() {}
    public static function ju_get_avatar($user_id) {
        $mysqli = db::get_mysqli();
        $user_id = (integer) $user_id;
        $color = $mysqli->query('SELECT user_avatar FROM testfo_users WHERE user_id="'.$user_id.'"');
        $color = $color->fetch_assoc();
        $infos['avatar'] = $color['user_avatar'];
        if (strpos($infos["avatar"],'://') === FALSE)
            $infos["avatar"] = 'https://starcitizen.fr/Forum/download/file.php?avatar='.$infos["avatar"];
        
        elseif ($infos["avatar"] == '')
            $infos["avatar"] = 'http://odeworld.files.wordpress.com/2008/05/cdn10_mydeco_avatar.gif';
        
        return $infos['avatar'];
    }
    
    // FUNCTION get_names
    // Retourne l'id forum de joueur avec leurs noms forum
    // @ids : les noms forums en string séparés
    // ##################################################
    public static function get_id($pseudo) {
        $mysqli = db::get_mysqli();
        $pseudo = explode(',',$pseudo);
        foreach ($pseudo as $name) {
            $sql = $mysqli->query('SELECT user_id FROM testfo_users WHERE username LIKE "'.$name.'"');
            $ds = $sql->fetch_assoc();
            $output[] = $ds['user_id'];
        }
        $output = implode(',',$output);
        return $output;
    }
    // FUNCTION get_names
    // Retourne le nom forum de joueur avec leurs id forum
    // @ids : l'id forums en string séparés ,
    // ##################################################
    public static function get_names($ids) {
        $mysqli = db::get_mysqli();
        // La même qu'au dessus à l'envers
        $pseudo = explode(',',$ids);
        foreach ($pseudo as $name) {
            $sql = $mysqli->query('SELECT username FROM testfo_users WHERE user_id = "'.$name.'"');
            $ds = $sql->fetch_assoc();
            $output[] = $ds['username'];
        }
        $output = implode(',',$output);
        return $output;
    }
}

?>