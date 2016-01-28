<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class Migration_init_countries extends CI_Migration {

    public function up() {
        //
        //Create countries table
        //
        $this->dbforge->add_field([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                'unsigned' => true,
                "auto_increment" => true,
            ],
            "code" => [
                "type" => "VARCHAR",
                "constraint" => 100,
            ],
            "name" => [
                "type" => "VARCHAR",
                "constraint" => 255,
            ],
            "created" => [
				"type" => "DATETIME",
				"null" => TRUE,
            ]
        ]);
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("countries");

        //
        //Extend stations with country id
        //
        $this->dbforge->add_column("stations", [
            "country_id" => [
                "type" => "INT",
                "constraint" => 11
            ],
            "active" => [
                "type" => "INT",
                "constraint" => 1,
                "default" => 1
            ],
        ]);

        //
        //Create user countries table
        //
        $this->dbforge->add_field([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                'unsigned' => true,
                "auto_increment" => true,
            ],
            "user_id" => [
                "type" => "INT",
                "constraint" => 11
            ],
            "country_id" => [
                "type" => "INT",
                "constraint" => 11
            ]
        ]);
        $this->dbforge->add_key("id", true);
        $this->dbforge->create_table("users_countries");

        //
        //Remove unused table
        //
        $this->dbforge->drop_table('users_stations');
        $this->dbforge->drop_column("users", "country");
    }

    public function down() {
        $this->dbforge->drop_table("countries");
        $this->dbforge->drop_column("stations", "country_id");
        $this->dbforge->drop_column("stations", "active");
        $this->dbforge->drop_table("users_countries");

        $this->db->query("CREATE TABLE `users_stations` (
          `id` int(11) NOT NULL,
          `user_id` int(11) NOT NULL,
          `station_id` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        $this->db->query("ALTER TABLE `users_stations` ADD PRIMARY KEY (`id`)");
        $this->db->query("ALTER TABLE `users_stations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_column("users", [
            "country" => [
                "type" => "VARCHAR",
                "constraint" => 255
            ]
        ]);
    }
}
