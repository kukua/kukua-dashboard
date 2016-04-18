<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_data_stations_users extends CI_Migration {

	public function up() {
		$this->db->query("TRUNCATE `stations`");
		$this->db->query("TRUNCATE `users_stations`");

		$this->db->query("INSERT INTO `stations` (`id`, `region_id`, `name`, `device_id`, `sim_id`, `latitude`, `longitude`, `elevation`, `active`) VALUES
(1,1,'Mshizii','4a0000009f113b00',8944538523007969432,'-4.8199534','38.35222','',1),
(2,1,'Mandakamnono','4a000000841d300b',8944538523007969408,'-3.3830366','37.381298','',1),
(3,1,'Mavumo','4a000000a687dcd8',NULL,NULL,NULL,NULL,1),
(4,1,'Makuyuni','4a0000001e2e607e',NULL,NULL,NULL,NULL,1),
(5,1,'Rauya','4a0000009f696fb0',NULL,NULL,NULL,NULL,1),
(6,1,'Mwangoi','4a000000a5e4d2c1',NULL,NULL,NULL,NULL,1),
(7,1,'Baga','4a000000890d85ba',NULL,NULL,NULL,NULL,1),
(8,1,'Sanyo','4a0000007aa19521',NULL,NULL,NULL,NULL,1),
(10,2,'Nigeria3','4a000000fab23419',8944538523007962452,'','','',1);");

		$this->db->select("*");
		$this->db->from("users");
		$users = $this->db->get()->result_array();

		$stations = [1,2,3,4,5,6,7,8];
		$i = 0;
		foreach($users as $user) {
			foreach($stations as $key => $stationId) {
				$data[$i]["user_id"] = $user["id"];
				$data[$i]["station_id"] = $stationId;
				$i++;
			}
		}
		$this->db->insert_batch("users_stations", $data);
    }

    public function down() {
		$this->db->query("TRUNCATE `stations`");
		$this->db->query("TRUNCATE `users_stations`");
    }
}
