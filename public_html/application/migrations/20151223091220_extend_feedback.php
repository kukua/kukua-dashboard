<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_extend_feedback extends CI_Migration {

    public function up() {
        $fields = [
            'completed' => [
                'type' => "INT",
                'constraint' => 1,
                'default' => 0
            ]
        ];
        $this->dbforge->add_column('users_feedback', $fields);
    }

    public function down() {
        $this->dbforge->drop_column('users_feedback', 'completed');
    }
}
