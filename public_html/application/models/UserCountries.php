<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Models
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class UserCountries extends CI_Model {

    const TABLE = "users_countries";

    protected $_id;
    protected $_user_id;
    protected $_country_id;

	/**
	 * @access public
	 * @return void
	 */
    public function __construct() {
		parent::__construct();

		$this->_id			= null;
		$this->_user_id		= null;
		$this->_country_id	= null;
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
	 * @param  int $countryId
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
	 * @param  array $data
	 * @return UserCountries
     */
    public function populate($data) {
        $object = new UserCountries();
        if (isset($data["id"])) {
            $object->setId($data["id"]);
        }
        if (isset($data["user_id"])) {
            $object->setUserId($data["user_id"]);
        }
        if (isset($data["country_id"])) {
            $object->setCountryId($data["country_id"]);
        }
        return $object;
    }

    /**
	 * @access public
	 * @param  int $id
	 * @param  array $data
	 * @return boolean
     */
    public function save($id, $data) {
		/* delete current user points */
        $this->db->where("user_id", $id);
        $this->db->delete(self::TABLE);

        $inserting = [];
        foreach($data as $key => $country_id) {
            if (is_numeric($country_id)) {
                $inserting[$key]["user_id"]    = $id;
                $inserting[$key]["country_id"] = $country_id;
            }
        }

		/* add new user points */
        if (!empty($inserting)) {
            $this->db->insert_batch(self::TABLE, $inserting);
		}

		return true;
    }

    /**
	 * @access public
	 * @param  int $id
	 * @param  boolean $huge
	 * @return Array
     */
    public function findByUserId($id, $huge = false) {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("user_id", $id);
        $get = $this->db->get();

        $result = [];
        foreach($get->result_array() as $key => $value) {
            $result[$key]["uc"] = $this->populate($value);
            if ($huge !== false) {
                $country = new Countries();
                $result[$key]["country"] = $country->findById($value["country_id"]);
            }
        }
        return $result;
    }

    /**
	 * @access public
	 * @param  string $x (column)
	 * @param  string $y (value)
	 * @return void
     */
    public function deleteWhere($x, $y) {
		$this->db->select("*");
		$this->db->where($x, $y);
		$this->db->from(self::TABLE);
		$get = $this->db->get();

		if (count($get->result_array())) {
			$this->db->where($x, $y);
			$this->db->delete(self::TABLE);
		}
    }
}
