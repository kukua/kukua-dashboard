<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Controllers
 * @since	29-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 * @copyright 2016 Kukua B.V.
 */
class CountryColumn extends CI_Model {

	const TABLE = "countries_columns";

	protected $_id;
	protected $_country_id;
	protected $_name;
	protected $_visible;

	/**
	 * Class constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_id = null;
		$this->_country_id = null;
		$this->_name = "";
		$this->_visible = 0;
	}

	/**
	 * set id
	 *
	 * @access public
	 * @param  int $id
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setId($id) {
		if (!is_numeric($id)) {
			throw new InvalidArgumentException("Id supplied not valid");
		}
		$this->_id = $id;
	}

	/**
	 * Get id
	 *
	 * @access public
	 * @return int
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * set country id
	 *
	 * @access public
	 * @param  int $id
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setCountryId($id) {
		if (!is_numeric($id)) {
			throw new InvalidArgumentException("Country id supplied not valid");
		}
		$this->_country_id = $id;
	}

	/**
	 * Get country id
	 *
	 * @access public
	 * @return int
	 */
	public function getCountryId() {
		return $this->_country_id;
	}

	/**
	 * Set name
	 *
	 * @access public
	 * @param  string $name
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setName($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException("name supplied not valid");
		}
		$this->_name = $name;
	}

	/**
	 * get name
	 *
	 * @access public
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * set visibility
	 *
	 * @access public
	 * @param  int $visible
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setVisible($visible) {
		if (!is_numeric($visible) || $visible < 0 || $visible > 1) {
			throw new InvalidArgumentException("Visibility supplied not valid");
		}
		$this->_visible = $visible;
	}

	/**
	 * get visibility
	 *
	 * @access public
	 * @return int
	 */
	public function getVisible() {
		return $this->_visible;
	}

	/**
	 * Populates object
	 *
	 * @access public
	 * @param  Array $data
	 * @throws InvalidArgumentException
	 * @return CountryColumn
	 */
	public function populate($data = Array()) {
		if (!is_array($data)) {
			throw new InvalidArgumentException("Data supplied not of type array");
		}

		if (isset($data["id"])) {
			$this->setId($data["id"]);
		}
		if (isset($data["country_id"])) {
			$this->setCountryId($data["country_id"]);
		}
		if (isset($data["name"])) {
			$this->setName($data["name"]);
		}
		if (isset($data["visible"])) {
			$this->setVisible($data["visible"]);
		}

		return $this;
	}

	/**
	 * Current object state to array
	 *
	 * @access public
	 * @return Array
	 */
	public function toArray() {
		return [
			'id' => $this->getId(),
			'country_id' => $this->getCountryId(),
			'name' => $this->getName(),
			'visible' => $this->getVisible()
		];
	}

	/**
	 * Find column by id
	 *
     * @access public
     * @param  int id
     * @return CountryColumn
     */
    public function findById($id) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("id", $id);
        $get = $this->db->get()->row_array();
        return $this->populate($get);
    }

	/**
	 * Find by country id
	 *
     * @access public
     * @param  int country id
	 * @param  boolean visible
     * @return CountryColumn
     */
    public function findByCountryId($id, $visible = false) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
		$this->db->where("country_id", $id);

		if ($visible === true) {
			$this->db->where("visible", 1);
		}
   
		$get = $this->db->get()->result_array();

        $result = [];
        if (is_array($get)) {
            foreach($get as $row) {
                $result[] = (new CountryColumn())->populate($row);
            }
        }
        return $result;
    }

    /**
     * Saves current object state
     *
     * @access public
     * @return boolean | Smsclient
     */
    public function save() {
        if ($this->_validate() !== true) {
            return false;
        }

        if (is_null($this->getId()) === true) {
            if ($this->db->insert(self::TABLE, $this->toArray())) {
                $this->setId($this->db->insert_id());
                return $this;
            }
        } else {
            $this->db->where("id", $this->getId());
            if ($this->db->update(self::TABLE, $this->toArray())) {
                return $this;
            }
        }

        return false;
    }

	/**
	 * Deletes database row
	 * 
	 * @access public
	 * @param  int $id
	 * @throws InvalidArgumentException
	 * @return boolean
	 */
    public function delete($id) {
        if (!is_numeric($id)) {
            throw new invalidargumentexception("id supplied not valid");
        }

        $object = $this->findById($id);
        if ($object->getId() !== null) {
            $this->db->delete(self::TABLE, array('id' => $object->getId()));
            return true;
        }
        return false;
    }

	/**
	 * Validates class object
	 *
     * @access protected
     * @return boolean
     */
    protected function _validate() {
        $validCountryId		= $this->getCountryId() != null;
        $validNameValue		= $this->getName() != "" || $this->getName() != null;
        $validVisibleValue	= $this->getVisible() != "" || ($this->getVisible() >= 0 && $this->getVisible() <= 1);

        if ($validCountryId && $validNameValue && $validVisibleValue) {
            return true;
        }
        return false;
    }
}
