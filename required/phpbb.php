<?php namespace JULIET\api; 

    class Phpbb {
        public static function make_phpbb_env() {
              if(!defined('IN_PHPBB')) {
                // PHPBB Session managment
                if(!defined("ROOT_PATH"))
                define('ROOT_PATH', __DIR__."/../../../Forum/");

                $phpbb_root_path = ROOT_PATH;
                define('IN_PHPBB', true);

                if (!defined('IN_PHPBB') || !defined('ROOT_PATH')) {
                exit();
                }
                global $request;
                global $phpbb_container;
                global $phpbb_root_path, $user, $auth, $cache, $db, $config, $template, $table_prefix, $phpEx;
                global $request;
                global $phpbb_dispatcher;
                global $symfony_request;
                global $phpbb_log;
                global $phpbb_filesystem;

                $phpEx = "php";
                $phpbb_root_path = ROOT_PATH;
                include($phpbb_root_path . 'common.' . $phpEx);

                $user->session_begin();
                $auth->acl($user->data);
                $request->enable_super_globals();

                global $user;
            }
        }   
    }

?>