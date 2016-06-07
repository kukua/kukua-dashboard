<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @since	31-05-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Report extends CI_Model {

	const TABLE = 'reports';

	protected $_id;
	protected $_region;
	protected $_station;
	protected $_rows;
	protected $_created;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct($content = Array()) {
		parent::__construct();

		if (!empty($content)) {
			$this->populate($content);
		}
	}

	/**
	 * @access public
	 * @param  int $id
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setId($id) {
		if (!is_numeric($id)) {
			throw new InvalidArgumentException("No valid param supplied");
		}
		$this->_id = $id;
	}

	/**
	 * @access public
	 * @return int $this->_id
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @access public
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setRegion($region) {
		if (!is_string($region)) {
			throw new InvalidArgumentException("No valid param supplied");
		}
		$this->_region = $region;
	}

	/**
	 * @access public
	 * @return string $this->_region
	 */
	public function getRegion() {
		return $this->_region;
	}

	/**
	 * @access public
	 * @param  string $station
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setStation($station) {
		if (!is_string($station)) {
			throw new InvalidArgumentException("No valid param supplied");
		}
		$this->_station = $station;
	}

	/**
	 * @access public
	 * @return string $this->_station
	 */
	public function getStation() {
		return $this->_station;
	}

	/**
	 * @access public
	 * @param  string $row
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setRows($row) {
		if (!is_string($row)) {
			throw new InvalidArgumentException("No valid param supplied");
		}
		$this->_rows = $row;
	}

	/**
	 * @access public
	 * @return string $this->_rows
	 */
	public function getRows() {
		return $this->_rows;
	}

	/**
	 * @access public
	 * @param  string $created
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setCreated($created) {
		if (!is_string($created)) {
			throw new InvalidArgumentException("No valid param supplied");
		}
		$this->_created = (string) $created;
	}

	/**
	 * @access public
	 * @return string $this->_created
	 */
	public function getCreated() {
		return $this->_created;
	}

	/**
	 * @access public
	 * @param  Array $data
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function populate($data = Array()) {
		if (!is_array($data)) {
			throw new InvalidArgumentException("No valid param supplied");
		}

		if (isset($data["id"])) {
			$this->setId($data["id"]);
		}
		if (isset($data["region"])) {
			$this->setRegion($data["region"]);
		}
		if (isset($data["station"])) {
			$this->setStation($data["station"]);
		}
		if (isset($data["rows"])) {
			$this->setRows($data["rows"]);
		}
		if (isset($data["created"])) {
			$this->setCreated($data["created"]);
		}

		return $this;
	}

	/**
	 * Transform current object to key => value array
	 *
	 * @access public
	 * @return Array
	 */
	public function toArray() {
		return [
			'id' => $this->getId(),
			'region' => $this->getRegion(),
			'station' => $this->getStation(),
			'rows' => $this->getRows(),
			'created' => $this->getCreated()
		];
	}

	/**
	 * Save current state of object
	 *
     * @access public
	 * @return boolean | Report
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
	 * Load report
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
				$object = new Report();
        	    $result[] = $object->populate($row);
			}
		}
        return $result;
    }

	/**
	 * Validate object
	 *
	 * @access public
	 * @return boolean
	 */
	protected function _validate() {
		return true;
	}
}
