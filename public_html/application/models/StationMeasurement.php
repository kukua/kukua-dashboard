<?php defined("BASEPATH") OR exit("No direct script access allowed");

class StationMeasurement extends CI_Model {

	const TABLE = 'stations_measurements';

	protected $_id;
	protected $_station_id;
	protected $_name;
	protected $_column;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * @access public
	 * @param  int
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setId($id) {
		if (!is_numeric($id)) {
			throw new InvalidArgumentException("Invalid param supplied");
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
	 * @param  int
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setStationId($stationId) {
		if (!is_numeric($stationId)) {
			throw new InvalidArgumentException("Invalid param supplied");
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
	 * @param  string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setName($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException("Invalid param supplied");
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
	 * @param  string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setColumn($column) {
		if (!is_string($column)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}
		$this->_column = $column;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getColumn() {
		return $this->_column;
	}

	/**
	 * @access public
	 * @param  Array $data
	 * @throws InvalidArgumentException
	 * @return StationMeasurement
	 */
	public function populate($data) {
		if (!is_array($data)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}

		if (isset($data["id"])) {
			$this->setId($data["id"]);
		}
		if (isset($data["station_id"])) {
			$this->setStationId($data["station_id"]);
		}
		if (isset($data["name"])) {
			$this->setName($this->db->escape_str($data["name"]));
		}
		if (isset($data["column"])) {
			$this->setColumn($this->db->escape_str($data["column"]));
		}

		return $this;
	}

	/**
	 * @access public
	 * @return Array
	 */
	public function toArray() {
		return [
			'id' => $this->getId(),
			'station_id' => $this->getStationId(),
			'name' => $this->getName(),
			'column' => $this->getColumn()
		];
	}

	/**
	 * @access public
	 * @param  int $stationId
	 * @return Array
	 */
	public function findByStationId($stationId) {
		if (!is_numeric($stationId)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}

		$this->db->select("*");
		$this->db->where("station_id", $stationId);
		$this->db->from(self::TABLE);
		$rows = $this->db->get()->result_array();

		$result = [];
		foreach($rows as $record) {
			$object = new StationMeasurement();
			$result[] = $object->populate($record);
		}
		return $result;
	}

	/**
	 * @access public
	 * @param  int $id
	 * @return StationMeasurement
	 */
	public function findById($id) {
		if (!is_numeric($id)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}

		$this->db->select("*");
		$this->db->where("id", $id);
		$this->db->from(self::TABLE);
		$record = $this->db->get()->row_array();

		$object = (new StationMeasurement())->populate($record);
		return $object;
	}

	/**
	 * @access public
	 * @return StationMeasurement | boolean
	 */
	public function save() {
        $return = false;
		if ($this->_validate() !== false) {
			if (is_null($this->getId()) === true) {

				/* insert new record */
        	    if ($this->db->insert(self::TABLE, $this->toArray())) {
        	        $this->setId($this->db->insert_id());
        	        $return = $this;
				}
			} else {

				/* update existing record */
        	    $this->db->where('id', $this->getId());
        	    if ($this->db->update(self::TABLE, $this->toArray())) {
        	        $return = $this;
        	    }
        	}
        }
        return $return;
	}

	/**
	 * @access protected
	 * @return boolean
	 */
	protected function _validate() {
		$validStationId = is_numeric($this->getStationId()) == true;
		$validColumn = $this->getColumn() != '';
		if ($validStationId && $validColumn) {
			return true;
		}
		return false;
	}
}
