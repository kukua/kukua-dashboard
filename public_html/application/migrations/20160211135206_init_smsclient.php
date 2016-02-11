<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_init_smsclient extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                'unsigned' => true,
                "auto_increment" => true,
            ],
            "name" => [
                "type" => "VARCHAR",
                "constraint" => 100,
                "null" => TRUE
            ],
            "location" => [
                "type" => "VARCHAR",
                "constraint" => 100,
            ],
            "number" => [
                "type" => "VARCHAR",
                "constraint" => 15,
            ],
            "created" => [
				"type" => "DATETIME",
				"null" => TRUE,
            ]
        ]);
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("smsclients");
    }

    public function down() {
        $this->dbforge->drop_table("smsclients");
    }
}
