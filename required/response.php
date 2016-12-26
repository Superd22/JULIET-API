<?php namespace JULIET\api;

class Response {
    public static function json_response($message, $non_blocking_error = false) {
        header('Content-Type: application/json');
        $return = array(
        "data" => $message,
        "error" => false,
        );
        
        if($non_blocking_error) $return["msg"] = $non_blocking_error;
        return json_encode($return);
    }
    
    public static function json_error($error_message) {
        header('Content-Type: application/json');
        $return = array(
        "data"  => false,
        "error" => true,
        "msg"   => $error_message,
        );
        
        return json_encode($return);
    }
    
    private static function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = self::utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }
    
}

?>