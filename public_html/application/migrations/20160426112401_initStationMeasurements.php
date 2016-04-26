<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_initStationMeasurements extends CI_Migration {

    public function up() {

		/* create table */
		$fields = [
			'id' => [
				'type' => 'int',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'station_id' => [
				'type' => 'int',
				'constraint' => 11
			],
			'name' => [
				'type' => 'varchar',
				'constraint' => 100
			],
			'column' => [
				'type' => 'varchar',
				'constraint' => 100
			]
		];

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('stations_measurements');
    }

	public function down() {

		/* drop table */
		$this->db->drop_table('stations_measurements');
    }
}
