<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @since	14-04-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class UserStations extends CI_Model {

	const TABLE = 'users_stations';

	protected $_id;
	protected $_user_id;
	protected $_station_id;

	/**
	 * Class constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->_id = null;
		$this->_user_id = null;
		$this->_station_id = null;
	}

	/**
	 * @access public
	 * @param  int $id
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setId($id) {
		if (!is_numeric($id)) {
			throw new InvalidArgumentException("No valid id supplied");
		}
		$this->_id = $id;
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @access public
	 * @param  int $userId
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setUserId($userId) {
		if (!is_numeric($userId)) {
			throw new InvalidArgumentException("No valid user id supplied");
		}
		$this->_user_id = $userId;
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getUserId() {
		return $this->_user_id;
	}

	/**
	 * @access public
	 * @param  int $stationId
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setStationId($stationId) {
		if (!is_numeric($stationId)) {
			throw new InvalidArgumentException("No valid station id supplied");
		}
		$this->_station_id = $stationId;
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getStationId() {
		return $this->_station_id;
	}

	/**
	 * @access public
	 * @param  int $userId
	 * @throws InvalidArgumentException
	 * @return Station
	 */
	public function findStationsByUserId($userId) {
		if (!is_numeric($userId)) {
			throw new InvalidArgumentException("No valid user id supplied");
		}

		$this->db->select("*");
		$this->db->from(self::TABLE);
		$this->db->where("user_id", $userId);
		$dbResult = $this->db->get()->result_array();
		$result = [];
		if (!is_null($dbResult)) {
			foreach($dbResult as $row) {
				$object = (new Station())->findById($row["station_id"]);
				$result[] = $object;
			}
		}
		return $result;
	}

	/**
	 * Based on user stations, return region
	 *
	 * @access public
	 * @param  int $userId
	 * @throws InvalidArgumentException
	 * @return Region
	 */
	public function findRegionsByUserId($userId) {
		if (!is_numeric($userId)) {
			throw new InvalidArgumentException("No valid user id given");
		}

		$this->db->select("s.region_id");
		$this->db->from(self::TABLE . " us");
		$this->db->where("user_id", $userId);
		$this->db->join("stations s", "us.station_id=s.id");
		$this->db->group_by("region_id");

		$dbResult = $this->db->get()->result_array();
		$result = [];
		foreach($dbResult as $station) {
			$result[] = (new Region())->findById($station["region_id"]);
		}
		return $result;
	}

	/**
	 *
	 */
	public function saveBatch($userId, $data) {
		$this->db->where("user_id", $userId);
		$this->db->delete(self::TABLE);

		$insert = [];
		if (!empty($data)) {
			foreach($data as $key => $userStation) {
				$insert[$key]["user_id"] = $userId;
				$insert[$key]["station_id"] = $userStation;
			}

			if (!empty($insert)) {
				if ($this->db->insert_batch(self::TABLE, $insert) !== false) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Delete entries by station id
	 *
	 * @access public
	 * @param  int $stationId
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function deleteByStationId($stationId) {
		if (!is_numeric($stationId)) {
			throw new InvalidArgumentException("No valid param supplied");
		}

		$this->db->where("station_id", $stationId);
		$this->db->delete(self::TABLE);
	}
}
