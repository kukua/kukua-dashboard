<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Models
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Station extends CI_Model {

	const TABLE = "stations";

	protected $_id;
	protected $_region_id;
	protected $_name;
	protected $_device_id;
	protected $_sim_id;
	protected $_latitude;
	protected $_longitude;
	protected $_elevation;
	protected $_active;
	protected $_link;

	public function __construct() {
		parent::__construct();

		$this->_id = null;
		$this->_region_id = null;
		$this->_name = null;
		$this->_device_id = null;
		$this->_sim_id = null;
		$this->_latitude = null;
		$this->_longitude = null;
		$this->_elevation = null;
		$this->_active = 1;
		$this->_link = null;
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
	 * @param  enum(0,1) $active
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setRegionId($regionId) {
		if (!is_numeric($regionId)) {
			throw new InvalidArgumentException("No valid region id supplied");
		}
		$this->_region_id = $regionId;
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getRegionId() {
		return $this->_region_id;
	}

	/**
	 * @access public
	 * @param  string $name
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setName($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException("No valid name supplied");
		}
		$this->_name = $name;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @access public
	 * @param  string $deviceId
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setDeviceId($deviceId) {
		if (!is_string($deviceId)) {
			throw new InvalidArgumentException("No valid device id supplied");
		}
		$this->_device_id = $deviceId;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getDeviceId() {
		return $this->_device_id;
	}

	/**
	 * @access public
	 * @param  string $simId
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setSimId($simId) {
		if (!is_string($simId)) {
			throw new InvalidArgumentException("No valid sim id supplied");
		}
		$this->_sim_id = $simId;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getSimId() {
		return $this->_sim_id;
	}

	/**
	 * @access public
	 * @param  string $latitude
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setLatitude($latitude) {
		if (!is_string($latitude)) {
			throw new InvalidArgumentException("No valid latitude supplied");
		}
		$this->_latitude = $latitude;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getLatitude() {
		return $this->_latitude;
	}

	/**
	 * @access public
	 * @param  string $longitude
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setLongitude($longitude) {
		if (!is_string($longitude)) {
			throw new InvalidArgumentException("No valid longitude supplied");
		}
		$this->_longitude = $longitude;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getLongitude() {
		return $this->_longitude;
	}

	/**
	 * @access public
	 * @param  string $elevation
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setElevation($elevation) {
		if (!is_string($elevation)) {
			throw new InvalidArgumentException("No valid elevation supplied");
		}
		$this->_elevation = $elevation;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getElevation() {
		return $this->_elevation;
	}

	/**
	 * @access public
	 * @param  enum(0,1) $active
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setActive($active) {
		if (!is_numeric($active)) {
			throw new InvalidArgumentException("No valid active state supplied");
		}
		$this->_active = $active;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getActive() {
		return $this->_active;
	}

	/**
	 * @access public
	 * @param  string $link
	 * @throws InvalidArgumentException
	 */
	public function setLink($link) {
		if (!is_string($link)) {
			throw new InvalidArgumentException("No valid link supplied");
		}
		$this->_link = $link;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getLink() {
		return $this->_link;
	}

	/**
	 * @access public
	 * @param  Array $data
	 * @throws InvalidArgumentException
	 * @return Station
	 */
	public function populate($data) {
		if (!is_array($data)) {
			throw new InvalidArgumentException("No valid data supplied");
		}
		if (isset($data["id"])) {
			$this->setId($data["id"]);
		}
		if (isset($data["region_id"])) {
			$this->setRegionId($data["region_id"]);
		}
		if (isset($data["device_id"])) {
			$this->setDeviceId($this->db->escape_str($data["device_id"]));
		}
		if (isset($data["sim_id"])) {
			$this->setSimId($this->db->escape_str($data["sim_id"]));
		}
		if (isset($data["latitude"])) {
			$this->setLatitude($this->db->escape_str($data["latitude"]));
		}
		if (isset($data["longitude"])) {
			$this->setLongitude($this->db->escape_str($data["longitude"]));
		}
		if (isset($data["elevation"])) {
			$this->setElevation($this->db->escape_str($data["elevation"]));
		}
		if (isset($data["name"])) {
			$this->setName($this->db->escape_str($data["name"]));
		}
		if (isset($data["active"])) {
			$this->setActive($data["active"]);
		}
		if (isset($data["link"])) {
			$this->setLink($data["link"]);
		}
		return $this;
	}

	/**
	 * Converts object to array
	 *
	 * @access public
	 * @return void
	 */
	public function toArray() {
		return [
			'id'			=> $this->getId(),
			'region_id'		=> $this->getRegionId(),
			'name'			=> $this->getName(),
			'device_id'		=> $this->getDeviceId(),
			'sim_id'		=> $this->getSimId(),
			'latitude'		=> $this->getLatitude(),
			'longitude'		=> $this->getLongitude(),
			'elevation'		=> $this->getElevation(),
			'active'		=> $this->getActive(),
			'link'			=> $this->getLink(),
		];
	}

	/**
	 * Save current state of object
	 *
	 * @access public
	 * @return boolean
	 */
	public function save() {
		$return = false;
		if ($this->_validate() !== false) {

			//insert new record
			if (is_null($this->getId()) === true) {
				if ($this->db->insert(self::TABLE, $this->toArray())) {
					$this->setId($this->db->insert_id());
					$return = $this;
				}

			//update existing record
			} else {
				$this->db->where('id', $this->getId());
				if ($this->db->update(self::TABLE, $this->toArray())) {
					$return = $this;
				}
			}
		}
		return $return;
	}

	/**
	 * Load all stations
	 *
	 * @access public
	 * @return Array
	 */
	public function load() {
		$this->db->select("*");
		$this->db->from(self::TABLE);
		$get = $this->db->get()->result_array();

		$result = [];
		if (!is_null($get)) {
			foreach($get as $row) {
				$object = new Station();
				$result[] = $object->populate($row);
			}
		}
		return $result;
	}

	/**
	 * Find a station by device id
	 *
	 * @access public
	 * @param  string deviceId
	 * @throws InvalidArgumentException
	 * @return null | Station
	 */
	public function findByDeviceId($deviceId) {
		$this->db->select("*");
		$this->db->from(self::TABLE);
		$this->db->where("device_id", $deviceId);
		$this->db->where("active", 1);
		$get = $this->db->get()->row_array();
		if (!is_null($get)) {
			return $this->populate($get);
		}
		return false;
	}

	/**
	 * Find a station by region id
	 *
	 * @access public
	 * @param  string regionId
	 * @throws InvalidArgumentException
	 * @return null | Station
	 */
	public function findByRegionId($regionId) {
		if (!is_numeric($regionId)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}

		$this->db->select("*");
		$this->db->from(self::TABLE);
		$this->db->where("region_id", $regionId);
		$this->db->where("active", 1);
		$get = $this->db->get()->result_array();

		$result = [];
		if (!is_null($get)) {
			foreach($get as $row) {
				$object = new Station();
				$result[] = $object->populate($row);
			}
		}
		return $result;
	}

	/**
	 * Find a station by region and user id
	 *
	 * @access public
	 * @param  string regionId
	 * @param  string userId
	 * @throws InvalidArgumentException
	 * @return null | Station
	 */
	public function findByRegionIdAndUserId($regionId, $userId) {
		if (!is_numeric($regionId) || !is_numeric($userId)) {
			throw new InvalidArgumentException("Invalid param(s) supplied");
		}

		$this->db->select("main.*");
		$this->db->from(self::TABLE . " as main");
		$this->db->where("region_id", $regionId);
		$this->db->where("main.active", 1);
		$this->db->join(
			"users_stations us",
			"us.user_id = '". $userId . "' " .
			"AND us.station_id = main.id"
		);
		$get = $this->db->get()->result_array();

		$result = [];
		if (!is_null($get)) {
			foreach($get as $row) {
				$object = new Station();
				$result[] = $object->populate($row);
			}
		}
		return $result;
	}

	public function findByUserId($userId) {
		if (!is_numeric($userId)) {
			throw new InvalidArgumentException("Invalid param(s) supplied");
		}

		$this->db->select("main.*");
		$this->db->from(self::TABLE . " as main");
		$this->db->where("main.active", 1);
		$this->db->join(
			"users_stations us",
			"us.user_id = '". $userId . "' " .
			"AND us.station_id = main.id"
		);
		$get = $this->db->get()->result_array();

		$result = [];
		if (!is_null($get)) {
			foreach($get as $row) {
				$object = new Station();
				$result[] = $object->populate($row);
			}
		}
		return $result;
	}

	/**
	 * Find station by id
	 *
	 * @access public
	 * @param  int $id
	 * @throws InvalidArgumentException
	 * @return Station
	 */
	public function findById($id, $active = false) {
		if (!is_numeric($id)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}

		$this->db->select("*");
		$this->db->from(self::TABLE);
		$this->db->where("id", $id);
		if ($active === true) {
			$this->db->where('active', 1);
		}

		$get = $this->db->get()->row_array();
		if (!is_null($get)) {
			return $this->populate($get);
		}
		return false;
	}

	/**
	 * Find station by name
	 *
	 * @access public
	 * @param  string $name
	 * @return Array
	 */
	public function findByName($name) {
		$this->db->select("*");
		$this->db->from(self::TABLE);
		$this->db->where("name", $name);
		$get = $this->db->get();

		$result = [];
		foreach($get->result_array() as $key => $value) {
			$result[] = $this->populate($value);
		}
		return $result;
	}

	/**
	 * Delete station by id
	 *
	 * @access public
	 * @param  int $id
	 * @return boolean
	 */
	public function delete($id) {
		$item = $this->findById($id);
		if ($item->getId() !== false) {
			if ($this->db->delete(self::TABLE, array('id' => $item->getId()))) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Validates object
	 *
	 * @access private
	 * @return boolean
	 */
	private function _validate() {
		return true;
	}
}
