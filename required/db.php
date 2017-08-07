<?php namespace JULIET\api; 

    class db {

        /**
         * MYSQLi instance
         *
         * @var mysqli
         */
        private static $_mysqli;

        private function __construct() {
            require_once(__DIR__."/../../../Flotte/inc/mysql.inc.php");
            self::$_mysqli = new_mysql_co();

            mysqli_set_charset(self::$_mysqli, "utf8");
        }

        /**
         * Get mysqli instance
         *
         * @return mysqli
         */
        public static function get_mysqli() {
            if(is_null(self::$_mysqli)) new db();

            return self::$_mysqli; 
        }
    }

?>