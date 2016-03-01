<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_station_columns extends CI_Migration {

	public function up() {
		$this->dbforge->drop_column("stations_columns", "key");
		$this->dbforge->add_column("stations_columns", [
			"country_column_id" => [
				"type" => "INT",
				"constraint" => 11,
				"unsigned" => true,
				"null" => false
			]
		]);

		//Clear table stations_columns
		$this->db->query("TRUNCATE stations_columns");
	}

	public function down() {
		$this->dbforge->drop_column("stations_columns", "country_column_id");
		$this->dbforge->add_column("stations_columns", [
			"key" => [
				"type" => "VARCHAR",
				"constraint" => 25,
			],
		]);
	}
}
