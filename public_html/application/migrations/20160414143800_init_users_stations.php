<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_init_users_stations extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                'unsigned' => true,
                "auto_increment" => true,
            ],
            "user_id" => [
                "type" => "INT",
                "constraint" => 11,
            ],
            "station_id" => [
                "type" => "INT",
                "constraint" => 11,
            ]
        ]);
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("users_stations");
    }

    public function down() {
		$this->dbforge->drop_table('users_stations');
    }
}
