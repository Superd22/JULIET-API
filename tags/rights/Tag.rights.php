<?php namespace JULIET\api\Tags\Rights;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;

class Tag {
    
    public static function user_can_admin_tag($user_id, $tag) {
        $user = Rights::handle_user_id($user_id);
        $tag = self::fetch_tag_object($tag);
        
        // If we have no target
        if($tag->id == 0) return false;
        // If we're admin.
        if(Rights::is_admin($user_id)) return true;

        function deep_check($uid, $tag_id) {
            // We have a parent tag that gives ownership
            if(\JULIET\api\Tags\helper\Tag::user_has_tag($uid, $tag_id)) return true;
            // check parent of this parent
            $parent = \JULIET\api\Tags\helper\Tag::get_rights_from($tag_id);
            if($parent) return deep_check($uid, $parent);
            else return false;
        }
        
        return deep_check($user_id, $tag->id);
    }
    
    public static function user_can_give_tag_to_user($user_id, $tag, $target_user) {
        $user_id = Rights::handle_user_id($user_id);
        $target_user = Rights::handle_user_id($target_user);
        $tag = self::fetch_tag_object($tag);


        if(Rights::is_admin($user_id)) return true;
        // We can assign un-restricted items to ourselves
        if($tag->restricted == 0 && $user_id == $target_user) return true;
        if($tag->restricted == 1) {
            // We need to have admin privileges on this tag to be able to assign it to anyone.
            return self::user_can_admin_tag($user_id,$tag);
        }
    }
    public static function user_can_give_tag_to_ship($user_id, $tag, $target_ship) {
        $user = Rights::handle_user_id($user);
        $tag = self::fetch_tag_object($tag);
        
    }
    public static function user_can_give_tag_to_ship_model($user_id, $tag, $target_ship_model) {
        $user = Rights::handle_user_id($user);
        $tag = self::fetch_tag_object($tag);
        
    }
    public static function user_can_give_tag_to_ship_template($user_id, $tag, $target_ship_template) {
        $user = Rights::handle_user_id($user);
        $tag = self::fetch_tag_object($tag);
    }
    
    private static function fetch_tag_object($tag) {
        if($tag instanceof \JULIET\api\Tags\helper\Tag)
        return $tag;
        
        $rTag = \JULIET\api\Tags\helper\Tag::get_single_tag($tag);
        return $rTag;
    }
}