<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_stations_to_users extends CI_Migration {

	public function up() {
		/** Remove previous columns **/
		$this->dbforge->drop_column('stations', 'country_id');
		$this->dbforge->drop_column('stations', 'station_id');

		/** Add new columns **/
        $fields = [
			'device_id' => [
				'type' => 'VARCHAR',
				'constraint' => 16,
			],
            'region_id' => [
                'type' => 'INT',
                'constraint' => 11,
				'null' => true
			],
			'sim_id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'null' => true
			],
			'latitude' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true
			],
			'longitude' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true
			],
			'elevation' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => true
			]
        ];
        $this->dbforge->add_column('stations', $fields);
		$this->db->query("TRUNCATE `stations`");
    }

	public function down() {
		/** Remove new columns **/
		$this->dbforge->drop_column('stations', 'device_id');
        $this->dbforge->drop_column('stations', 'region_id');
		$this->dbforge->drop_column('stations', 'sim_id');
		$this->dbforge->drop_column('stations', 'latitude');
		$this->dbforge->drop_column('stations', 'longitude');
		$this->dbforge->drop_column('stations', 'elevation');

		/** Add previous columns  **/
		$fields = [
			'station_id' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'country_id' => [
				'type' => 'INT',
				'constraint' => 11
			]
		];
		$this->dbforge->add_column('stations', $fields);

		// Truncate table, and insert "old" records
		$this->db->query("TRUNCATE `stations`");
		$this->db->query("INSERT INTO `stations` (`id`, `name`, `station_id`, `country_id`, `active`) VALUES
(1,'mwangoi','sivad_ndogo_a5e4d2c1',1,0),
(2,'mavumo','sivad_ndogo_a687dcd8',1,0),
(3,'migambo','sivad_ndogo_a468d67c',1,1),
(4,'mshizii','sivad_ndogo_9f113b00',1,1),
(5,'baga','sivad_ndogo_890d85ba',1,1),
(6,'makuyuni','sivad_ndogo_1e2e607e',1,1),
(7,'rauya','sivad_ndogo_9f696fb0',1,1),
(8,'mandakamnono','sivad_ndogo_841d300b',1,1),
(9,'sanyo','sivad_ndogo_7aa19521',1,1),
(12,'Ibadan','sivad_ndogo_fab23419',2,1),
(14,'Ghana01','sivad_ndogo_61304542',3,1),
(15,'Ghana02','sivad_ndogo_201aac83',3,1),
(16,'Chitengo','rainstation_22b6e462',4,1),
(17,'Bebedo','rainstation_d4547788',4,1),
(21,'data','SensorData;deviceId=d3a0b8f6143344ae',7,1),
(28,'Chitengo','rainstation_22b6e462',6,1),
(29,'Bebedo','rainstation_d4547788',6,1),
(31,'forecast','Foreca;id=100156918;type=hourly',1,1),
(32,'Ibadan2','rainstation_92dffd96',2,1),
(33,'SNV03','sivad_snv_1fea7d73',5,1),
(34,'Nigel UK','sivad_ndogo_fc77b785',6,1),
(35,'SNV01','sivad_snv_ae9d67e6',5,1),
(36,'SNV02','sivad_snv_19496582',5,1),
(37,'Ibadan3','sivad_ndogo_846560ac',2,1),
(39,'Data','SensorData;deviceId=9a590d91291c41ae',7,1),
(40,'SODAQ0001','433bc630122841ae',6,1),
(41,'SODAQ0001','433bc630122841ae',7,1),
(42,'433B','SensorData;deviceId=433bc630122841ae',8,1),
(43,'4712','SensorData;deviceId=47127165143841ae',8,1),
(44,'435A','SensorData;deviceId=435a4d38153941ae',8,1),
(45,'E616','SensorData;deviceId=e616a5cf292e41ae',8,1),
(46,'C6AF','SensorData;deviceId=c6afaa84170041ae',8,1),
(47,'5D0B','SensorData;deviceId=5d0b54ec131e41ae',8,1),
(48,'5131','SensorData;deviceId=5131fc0f120241ae',8,1),
(49,'0A41','SensorData;deviceId=0a41639c171241ae',8,1),
(50,'FFAF','SensorData;deviceId=ffaf5c19163c41ae',8,1),
(51,'1416','SensorData;deviceId=14169265172c41ae',8,1),
(52,'FF4C','SensorData;deviceId=ff4c5188293e41ae',8,1),
(54,'LBS_0001','433bc630122841ae',8,1),
(55,'Nigel test uk','sivad_ndogo_fc77b785',2,1),
(56,'London','sivad_ndogo_fc77b785',9,1),
(57,'Itay','SensorData;deviceId=464ba446172341ae',10,1)");
    }
}
