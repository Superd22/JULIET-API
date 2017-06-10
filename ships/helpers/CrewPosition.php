<?php namespace JULIET\api\Ships\helpers;

use JULIET\api\Phpbb;
use JULIET\api\db;
use JULIET\api\Response;
use JULIET\api\Rights\Main as Rights;
use JULIET\api\Ships\models\Ship as ShipModel;
use JULIET\api\Ships\models\ShipType as ShipTypeModel;

use JULIET\api\Ships\models as Models;


class CrewPosition {
    public static function update(Models\CrewPosition $position) {
        if($position->id == 0) return $this->insert($position);

        $mysqli = db::get_mysqli();

        $sql = "UPDATE star_ships_variant_positions SET
        name = '{$mysqli->real_escape_string($position->name)}'
        WHERE id={$position->id}";

        $q = $mysqli->query($sql);

        if($mysqli->error) throw new \Exception($mysqli->error);
        else return $position;
    }

    private static function insert(Models\CrewPosition $position) {
        $mysqli = db::get_mysqli();

        $sql = "INSERT INTO star_ships_variant_positions (template_id, name) VALUES
        (
            '{$position->template_id}',
            '{$mysqli->real_escape_string($position->name)}'
        )";

        $q = $mysqli->query($sql);

        if($mysqli->error) throw new \Exception($mysqli->error);
        else {
            $position->id = $mysqli->insert_id;
            return $position;
        }
    }

    public static function delete(Models\CrewPosition $position) {
        $mysqli = db::get_mysqli();

        $sql = "DELETE FROM star_ships_variant_positions WHERE id='{$position->id}' LIMIT 1";
        $q = $mysqli->query($sql);

        if($mysqli->error) throw new \Exception($mysqli->error);
        else {
            return true;
        }
    }
    
}


?>