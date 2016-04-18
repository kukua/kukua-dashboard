<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @since	15-04-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Region extends CI_Model {

	const TABLE = 'regions';

	protected $_id;
	protected $_name;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->_id = null;
		$this->_name = null;
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
	 * @return int
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @access public
	 * @param  string $name
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setName($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException("No valid param supplied");
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

	public function toArray() {
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
		];
	}

	/**
	 * @access public
	 * @param  array $data
	 * @return Region
	 */
	public function populate($data) {
		if (!is_array($data)) {
			throw new InvalidArgumentException("No valid param supplied");
		}

		if (isset($data["id"])) {
			$this->setId($data["id"]);
		}
		if (isset($data["name"])) {
			$this->setName($data["name"]);
		}

		return $this;
	}

	/**
	 * Load all regions
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
				$object = new Region();
        	    $result[] = $object->populate($row);
			}
		}
        return $result;
    }

	/**
	 * Find region by id
	 *
	 * @access public
	 * @param  int $id
	 * @throws InvalidArgumentException
     * @return Region | false
     */
    public function findById($id) {
		if (!is_numeric($id)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}

        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("id", $id);
		$get = $this->db->get()->row_array();
		if (!is_null($get)) {
			return $this->populate($get);
		}
		return false;
    }

	/**
	 * Find region by name
	 *
	 * @access public
	 * @param  string $name
	 * @throws InvalidArgumentException
     * @return Region | false
     */
    public function findByName($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}

        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("name", $name);
		$get = $this->db->get()->row_array();
		if (!is_null($get)) {
			return $this->populate($get);
		}
		return false;
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

	protected function _validate() {
		return true;
	}
}
