<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_addUserRole extends CI_Migration {

	public function up() {
		$data = [
			'name' => 'manager',
			'description' => 'Managers'
		];
		$this->db->insert('groups', $data);
    }

	public function down() {
		$this->db->select('*');
		$this->db->from('groups');
		$this->db->where('name', 'manager');
		$get = $query->get()->result();

		if (is_object($get)) {
			$this->db->where('id', $get->id);
			$this->db->delete('groups');
		}
    }
}
