<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Ships\models\ShipType as ShipTypeModel;

use JULIET\api\Ships\models as Models;


class CrewMember {
    public static function update(Models\CrewMember $member) {
        if($position->id == 0) return $this->insert($member);

       /* $mysqli = db::get_mysqli();

        $sql = "UPDATE star_ships_variant_crew SET
        name = '{$mysqli->real_escape_string($position->name)}'
        WHERE id={$position->id}";

        $q = $mysqli->query($sql); */

        //if($mysqli->error) throw new \Exception($mysqli->error);
        return $member;
    }

    private static function insert(Models\CrewMember $member) {
        $mysqli = db::get_mysqli();

        $sql = "INSERT INTO star_ships_variant_crew (template_id, user_id, job_id) VALUES
        (
            '{$member->template_id}',
            '{$member->user_id}',
            '{$member->job_id}'
        )";

        $q = $mysqli->query($sql);

        if($mysqli->error) throw new \Exception($mysqli->error);
        else {
            $member->id = $mysqli->insert_id;
            return $member;
        }
    }

    public static function delete(Models\CrewMember $member) {
        $mysqli = db::get_mysqli();

        $sql = "DELETE FROM star_ships_variant_crew WHERE id='{$member->id}' LIMIT 1";
        $q = $mysqli->query($sql);

        if($mysqli->error) throw new \Exception($mysqli->error);
        else {
            return true;
        }
    }
    
}


?>