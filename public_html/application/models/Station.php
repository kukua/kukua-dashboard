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
    protected $_country_id;
    protected $_station_id;
    protected $_name;
    protected $_active;

    public function __construct() {
        parent::__construct();

        $this->_id = null;
        $this->_country_id = null;
        $this->_station_id = null;
        $this->_name = null;
        $this->_active = 1;
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

	public function getId() {
		return $this->_id;
	}

	/**
	 * @access public
	 * @param  enum(0,1) $active
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setCountryId($countryId) {
		if (!is_numeric($countryId)) {
			throw new InvalidArgumentException("No valid country id supplied");
		}
		$this->_country_id = $countryId;
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getCountryId() {
		return $this->_country_id;
	}

	/**
	 * @access public
	 * @param  string $stationId
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setStationId($stationId) {
		if (!is_string($stationId)) {
			throw new InvalidArgumentException("No valid station id supplied");
		}
		$this->_station_id = $stationId;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getStationId() {
		return $this->_station_id;
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
	 * @param  Array $data
	 * @throws InvalidArgumentException
	 * @return Stations
	 */
	public function populate($data) {
		if (!is_array($data)) {
			throw new InvalidArgumentException("No valid data supplied");
		}
        if (isset($data["id"])) {
            $this->setId($data["id"]);
        }
        if (isset($data["country_id"])) {
            $this->setCountryId($data["country_id"]);
        }
        if (isset($data["station_id"])) {
            $this->setStationId($this->db->escape_str($data["station_id"]));
        }
        if (isset($data["name"])) {
            $this->setName($this->db->escape_str($data["name"]));
        }
        if (isset($data["active"])) {
            $this->setActive($data["active"]);
        }
        return $this;
    }

	/**
	 * @access public
	 * @return void
	 */
	public function toArray() {
		return [
			'id' => $this->getId(),
			'country_id' => $this->getCountryId(),
			'station_id' => $this->getStationId(),
			'name' => $this->getName(),
			'active' => $this->getActive(),
		];
	}

    /**
     * @access public
     * @return boolean
     */
    public function save() {
        if ($this->_validate() === false) {
            return false;
        }

        if (is_null($this->getId()) === true) {
            if ($this->db->insert(self::TABLE, $this->toArray())) {
                $this->setId($this->db->insert_id());
                return $this;
            }
        } else {
            $this->db->where('id', $this->getId());
            if ($this->db->update(self::TABLE, $this->toArray())) {
                return $this;
            }
        }
        return false;
    }

    /**
     * @access public
     * @return Array
     */
    public function load() {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $get = $this->db->get();

        $result = [];
        foreach($get->result_array() as $key => $value) {
            $result[] = $this->populate($value);
        }
        return $result;
    }

    /**
	 * @access public
	 * @param  int $id
	 * @param  boolean $all
     * @return Array
     */
    public function findByCountryId($id, $all = false) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("country_id", $id);
        if ($all === false) {
            $this->db->where("active", 1);
        }
        $get = $this->db->get();

        $result = [];
        foreach($get->result_array() as $key => $value) {
            $object = new Station();
            $result[] = $object->populate($value);
        }
        return $result;
    }

	/**
	 * @access public
	 * @param  string stationId
	 * @return Stations
	 */
    public function findByStationId($stationId) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("station_id", $stationId);
		$get = $this->db->get()->row_array();
		if (!is_null($get)) {
			return $this->populate($get);
		}
		return false;
    }

    /**
	 * @access public
	 * @param  int $id
     * @return Stations
     */
    public function findById($id) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("id", $id);
        $get = $this->db->get();
        return $this->populate($get->row_array());
    }

    /**
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
	 * delete where x = y
	 *
	 * @access public
	 * @param  string $x (column)
	 * @param  string $y (value)
	 * @return void
     */
	public function deleteWhere($x, $y, $recursive = false) {
		$this->db->select("*");
		$this->db->where($x, $y);
		$this->db->from(self::TABLE);
		$get = $this->db->get();

		if (count($get->result_array())) {
			$this->db->where($x, $y);
			$this->db->delete(self::TABLE);
			if ($recursive) {
				foreach($get->result_array() as $result) {
					(new StationColumn())->deleteByStationId($result["id"]);
				}
			}
		}
    }

    /**
     * @access private
     * @return boolean
     */
    private function _validate() {
        return true;
    }
}
