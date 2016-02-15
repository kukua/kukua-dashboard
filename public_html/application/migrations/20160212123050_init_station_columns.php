<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_init_station_columns extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                'unsigned' => true,
                "auto_increment" => true,
            ],
            "station_id" => [
                "type" => "INT",
                "constraint" => 11,
            ],
            "key" => [
                "type" => "VARCHAR",
                "constraint" => 25,
            ],
            "value" => [
				"type" => "VARCHAR",
				"constraint" => "100",
            ]
        ]);
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("stations_columns");
    }

    public function down() {
        $this->dbforge->drop_table("stations_columns");
    }
}
