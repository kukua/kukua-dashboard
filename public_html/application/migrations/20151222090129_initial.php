<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_initial extends CI_Migration {

    public function up() {
        $this->db->query("CREATE TABLE `groups` (
              `id` mediumint(8) UNSIGNED NOT NULL,
              `name` varchar(20) NOT NULL,
              `description` varchar(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("INSERT INTO `groups` (`id`, `name`, `description`) VALUES
            (1, 'admin', 'Administrator'),
            (2, 'members', 'General User')");

        $this->db->query("
            CREATE TABLE `login_attempts` (
              `id` int(11) UNSIGNED NOT NULL,
              `ip_address` varchar(15) NOT NULL,
              `login` varchar(100) NOT NULL,
              `time` int(11) UNSIGNED DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("CREATE TABLE `stations` (
              `id` int(11) NOT NULL,
              `name` varchar(255) NOT NULL,
              `station_id` varchar(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("INSERT INTO `stations` (`id`, `name`, `station_id`) VALUES
            (1, 'mwangoi',      'sivad_ndogo_a5e4d2c1'),
            (2, 'mavumo',       'sivad_ndogo_a687dcd8'),
            (3, 'migambo',      'sivad_ndogo_a468d67c'),
            (4, 'mshizii',      'sivad_ndogo_9f113b00'),
            (5, 'baga',         'sivad_ndogo_890d85ba'),
            (6, 'makuyuni',     'sivad_ndogo_1e2e607e'),
            (7, 'rauya',        'sivad_ndogo_9f696fb0'),
            (8, 'mandakamnono', 'sivad_ndogo_841d300b'),
            (9, 'sanyo',        'sivad_ndogo_7aa19521'),
            (10, 'ibadan',      'sivad_ndogo_fab23419')");

        $this->db->query("CREATE TABLE `users` (
              `id` int(11) UNSIGNED NOT NULL,
              `ip_address` varchar(15) NOT NULL,
              `username` varchar(100) DEFAULT NULL,
              `password` varchar(255) NOT NULL,
              `country` varchar(100) NOT NULL,
              `salt` varchar(255) DEFAULT NULL,
              `email` varchar(100) NOT NULL,
              `first_name` varchar(255) NOT NULL,
              `last_name` varchar(255) NOT NULL,
              `activation_code` varchar(40) DEFAULT NULL,
              `forgotten_password_code` varchar(40) DEFAULT NULL,
              `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
              `remember_code` varchar(40) DEFAULT NULL,
              `created_on` int(11) UNSIGNED NOT NULL,
              `last_login` int(11) UNSIGNED DEFAULT NULL,
              `active` tinyint(1) UNSIGNED DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `country`, `salt`, `email`, `first_name`, `last_name`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`) VALUES
            (1, '127.0.0.1', 'sbrn', '$2y$08$Uzs3UV5tf9k4S9CPPP2GH.XenXSZL6V1W3mYVQUidAnc73NYM0aPK', 'Tanzania', '', 'siebren@kukua.cc', 'Siebren', 'Kranenburg', '', NULL, NULL, NULL, 1268889823, 1450773051, 1)");

        $this->db->query("CREATE TABLE `users_groups` (
              `id` int(11) UNSIGNED NOT NULL,
              `user_id` int(11) UNSIGNED NOT NULL,
              `group_id` mediumint(8) UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
            (1, 1, 1),
            (2, 1, 2)");

        $this->db->query("CREATE TABLE `users_stations` (
          `id` int(11) NOT NULL,
          `user_id` int(11) NOT NULL,
          `station_id` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("ALTER TABLE `groups` ADD PRIMARY KEY (`id`)");
        $this->db->query("ALTER TABLE `login_attempts` ADD PRIMARY KEY (`id`)");
        $this->db->query("ALTER TABLE `stations` ADD PRIMARY KEY (`id`)");
        $this->db->query("ALTER TABLE `users` ADD PRIMARY KEY (`id`)");

        $this->db->query("ALTER TABLE `users_groups`
            ADD PRIMARY KEY (`id`),
            ADD UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
            ADD KEY `fk_users_groups_users1_idx` (`user_id`),
            ADD KEY `fk_users_groups_groups1_idx` (`group_id`)");

        $this->db->query("ALTER TABLE `users_stations` ADD PRIMARY KEY (`id`)");
        $this->db->query("ALTER TABLE `groups` MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3");

        $this->db->query("ALTER TABLE `login_attempts` MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT");
        $this->db->query("ALTER TABLE `stations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11");
        $this->db->query("ALTER TABLE `users` MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2");
        $this->db->query("ALTER TABLE `users_groups` MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3");
        $this->db->query("ALTER TABLE `users_stations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
        $this->db->query("ALTER TABLE `users_groups`
          ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
          ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION");
    }

    public function down() {
        $this->dbforge->drop_table('users_stations');
        $this->dbforge->drop_table('users_groups');
        $this->dbforge->drop_table('users');
        $this->dbforge->drop_table('stations');
        $this->dbforge->drop_table('login_attempts');
        $this->dbforge->drop_table('groups');
    }
}
