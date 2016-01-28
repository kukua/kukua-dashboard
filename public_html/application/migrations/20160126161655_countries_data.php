<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_countries_data extends CI_Migration {

    public function up() {
        $this->db->query("INSERT INTO `countries` (`code`, `name`, `created`) VALUES ('tz', 'Tanzania', NULL), ('ng', 'Nigeria', NULL), ('gh', 'Ghana', NULL), ('mz', 'Mozambique', NULL), ('ke', 'Kenya', NULL)");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 1");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 2");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 3");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 4");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 5");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 6");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 7");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 8");
        $this->db->query("UPDATE `stations` SET `country_id` = '1' WHERE `id` = 9");
        $this->db->query("UPDATE `stations` SET `country_id` = '2' WHERE `id` = 10");

        $result = $this->db->query("select * from users");
        foreach($result->result() as $key => $user) {
            $this->db->query("INSERT INTO `users_countries` (`user_id`, `country_id`) VALUES('" . $user->id . "', '1')");
        }
    }

    public function down() {
        //No need to delete the inserted data - previous migration removes tables
    }
}
