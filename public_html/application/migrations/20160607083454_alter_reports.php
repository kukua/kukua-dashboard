<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_alter_reports extends CI_Migration {

	public function up() {
		$this->dbforge->drop_column('reports', 'measurement');
		$this->dbforge->drop_column('reports', 'count');

		$this->dbforge->add_column('reports', [
			"rows" => [
				"type" => "VARCHAR",
				"constraint" => 255
			]
		]);
    }

    public function down() {
		$this->dbforge->drop_column('reports', 'rows');
		$this->dbforge->add_column('reports', [
            "measurement" => [
				"type" => "VARCHAR",
				"constraint" => "100",
			],
			"count" => [
				"type" => "INT",
				"constraint" => 11
			],
		]);
    }
}
