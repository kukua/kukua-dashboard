<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_init_countries_columns extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                'unsigned' => true,
                "auto_increment" => true,
            ],
            "country_id" => [
                "type" => "INT",
                "constraint" => 11,
            ],
            "name" => [
                "type" => "VARCHAR",
                "constraint" => 25,
            ],
            "visible" => [
				"type" => "INT",
				"constraint" => "1",
            ]
        ]);
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("countries_columns");
    }

    public function down() {
        $this->dbforge->drop_table("countries_columns");
    }
}
