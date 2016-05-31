<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_init_report extends CI_Migration {

    public function up() {

        $this->dbforge->add_field([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                'unsigned' => true,
                "auto_increment" => true,
            ],
            "region" => [
                "type" => "VARCHAR",
                "constraint" => 100,
            ],
            "station" => [
                "type" => "VARCHAR",
                "constraint" => 100,
            ],
            "measurement" => [
				"type" => "VARCHAR",
				"constraint" => "100",
			],
			"count" => [
				"type" => "INT",
				"constraint" => 11
			],
			"created" => [
				"type" => "DATETIME",
				"null" => TRUE
			]
        ]);
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("reports");
    }

    public function down() {
        $this->dbforge->drop_table('reports');
    }
}
