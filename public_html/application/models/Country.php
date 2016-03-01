<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Models
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Country extends CI_Model {

    const TABLE = "countries";

	protected $_id;
	protected $_code;
	protected $_name;
    protected $_created;

	/**
	 * @access public
	 * @return void
	 */
    public function __construct() {
        parent::__construct();

        $this->_id = null;
        $this->_code = null;
        $this->_name = null;
        $this->_created = (new DateTime())->format(DateTime::ISO8601);
    }

	/**
	 * @access public
	 * @throws InvalidArgumentException
	 * @param  int
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
	 * @throws InvalidArgumentException
	 * @param  string
	 * @return void
	 */
	public function setCode($code) {
		if (!is_string($code)) {
			throw new InvalidArgumentException("No valid code supplied");
		}
		$this->_code = $code;
	}

	/**
	 * @access public
	 * @return void
	 */
	public function getCode() {
		return $this->_code;
	}

	/**
	 * @access public
	 * @throws InvalidArgumentException
	 * @param  string
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
	 * @throws InvalidArgumentException
	 * @param  string
	 * @return void
	 */
	public function setCreated($created) {
		if (!is_string($created)) {
			throw new InvalidArgumentException("No valid create date supplied");
		}
		$this->_created = $created;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getCreated() {
		return $this->_created;
	}

    /**
     * @access public
     * @param  Array $data
     * @return Country
     */
    public function populate($data) {
        if (isset($data["id"])) {
            $this->setId($data["id"]);
        }
        if (isset($data["code"])) {
            $this->setCode($this->db->escape_str($data["code"]));
        }
        if (isset($data["name"])) {
            $this->setName($this->db->escape_str($data["name"]));
        }
        if (isset($data["created"])) {
            $this->setCreated($data["created"]);
        }
        return $this;
    }

	/**
	 * Converts object to array
	 *
	 * @access public
	 * @return Array
	 */
	public function toArray() {
		return [
			'id'		=> $this->getId(),
			'code'		=> $this->getCode(),
			'name'		=> $this->getName(),
			'created'	=> $this->getCreated()
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
            $object = new Country();
            $result[] = $object->populate($value);
        }
        return $result;
    }

    /**
     * @access public
     * @return Country
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
     * @return Boolean
     */
    public function delete($id = null) {
        if ($id === null) {
            $this->db->where("id", $this->getId());
            $this->db->delete(self::TABLE);
            return true;
        } elseif (is_numeric($id)) {
            $this->db->where("id", $id);
            $this->db->delete(self::TABLE);
            return true;
        }
        return false;
    }

    /**
     * @access private
     * @return boolean
     */
    private function _validate() {
        return true;
    }
}
