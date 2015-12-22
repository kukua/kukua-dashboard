<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_init_feedback extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
			'user_id' => array(
				'type' => 'INT',
				'constraint' => 11,
			),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'feedback' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
			'created' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
            ),
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table("users_feedback");
    }

    public function down() {
        $this->dbforge->drop_table('users_feedback');
    }
}
