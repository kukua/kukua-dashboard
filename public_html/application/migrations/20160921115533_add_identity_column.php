<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_identity_column extends CI_Migration {

    public function up() {
        $this->dbforge->add_column('users', [
            'identity' => [
                'type' => 'VARCHAR',
                'null' => false,
                'constraint' => 255,
                'after' => 'email'
            ]
        ]);

        $this->db->query("UPDATE `users` SET `identity` = `email`");
    }

    public function down() {
        $this->dbforge->drop_column('users', 'identity');
    }
}
