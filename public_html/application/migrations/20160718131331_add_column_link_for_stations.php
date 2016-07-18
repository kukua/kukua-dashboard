<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_link_for_stations extends CI_Migration {

    public function up() {
		$this->dbforge->add_column('stations', [
			"link" => [
				"type" => "VARCHAR",
				"constraint" => 255
			]
		]);
    }

    public function down() {
		$this->dbforge->drop_column('stations', 'rows');
    }
}
