<?php namespace JULIET\api; 

    class db {

        private static $_mysqli;

        private function __construct() {
            require_once(__DIR__."/../../../Flotte/inc/mysql.inc.php");
            self::$_mysqli = new_mysql_co();
        }

        public static function get_mysqli() {
            if(is_null(self::$_mysqli)) new db();

            return self::$_mysqli; 
        }
    }

?>