<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Models
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Smsclient extends CI_Model {

    const TABLE = "smsclients";

    protected $_id;
    protected $_name;
    protected $_location;
    protected $_number;
    protected $_created;

    /**
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->_id = null;
        $this->_name = null;
        $this->_location = null;
        $this->_number = null;
        $this->_created = (new DateTime())->format("Y-m-d H:i:s");
    }

    /**
     * @access public
     * @param  int
     * @throws InvalidArgumentException
     * @return void
     */
    public function setId($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("Param supplied not valid");
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
     * @param  string
     * @throws InvalidArgumentException
     * @return void
     */
    public function setName($name) {
        if (!is_string($name)) {
            throw new InvalidArgumentException("Param supplied not valid");
        }
        $this->_name = (string) $name;
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
    public function setLocation($location) {
        if (!is_string($location)) {
            throw new InvalidArgumentException("Param supplied not valid");
        }
        $this->_location = (string) $location;
    }

    /**
     * @access public
     * @return string
     */
    public function getLocation() {
        return $this->_location;
    }

    /**
     * @access public
     * @param  string
     * @throws InvalidArgumentException
     * @return void
     */
    public function setNumber($number) {
        if (!is_string($number)) {
            throw new InvalidArgumentException("Param supplied not valid");
        }
        $this->_number = (string) $number;
    }
    
    /**
     * @access public
     * @return string
     */
    public function getNumber() {
        return $this->_number;
    }

    /**
     * @access public
     * @param  string
     * @throws InvalidArgumentException
     * @return void
     */
    public function setCreated($created) {
        if (!is_string($created)) {
            throw new InvalidArgumentException("Param supplied not valid");
        }
        $this->_created = (string) $created;
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
     * @param  array
     * @throws InvalidArgumentException
     * @return $this
     */
    public function populate($data) {
        if (!is_array($data)) {
            throw new InvalidArgumentException("Param supplied not valid");
        }

        if (isset($data["id"])) {
            $this->setId($data["id"]);
        }
        if (isset($data["name"])) {
            $this->setName($data["name"]);
        }
        if (isset($data["location"])) {
            $this->setLocation($data["location"]);
        }
        if (isset($data["number"])) {
            $this->setNumber($data["number"]);
        }
        if (isset($data["created"])) {
            $this->setCreated($data["created"]);
        }
        return $this;
    }

    /**
     * Find record by id
     *
     * @access public
     * @param  int $id
     * @throws InvalidArgumentException
     * @return Smsclients
     */
    public function findById($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("Param supplied not valid");
        }

        $this->db->select("*");
        $this->db->from(self::TABLE);
        $this->db->where("id", $id);
        $get = $this->db->get()->row_array();
        return $this->populate($get);
    }

    /**
     * Load all database records
     *
     * @access public
     * @return Array
     */
    public function load() {
        $this->db->select("*");
        $this->db->from(self::TABLE);
        $get = $this->db->get();

        $result = [];
        foreach($get->result_array() as $set) {
            $object = (new Smsclient())->populate($set);
            $result[] = $object;
        }

        return $result;
    }

    /**
     * Delete a client by id
     *
     * @access public
     * @param  int $id
     * @throws InvalidArgumentException
     * @return boolean
     */
    public function delete($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("Param supplied not valid");
        }

        $object = $this->findById($id);
        if ($object->getId() !== null) {
            $this->db->delete(self::TABLE, array('id' => $id));
            return true;
        }
        return false;
    }

    /**
     * Saves current object state
     *
     * @access public
     * @return boolean | Smsclients
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
    }

    /**
     * Convert object to array
     *
     * @access public
     * @return Array
     */
    public function toArray() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'location' => $this->getLocation(),
            'number' => $this->getNumber(),
            'created' => $this->getCreated()
        ];
    }

    /**
     * Validate object
     *
     * @access protected
     * @return boolean
     */
    protected function _validate() {
        $validName = is_string($this->getName()) == True;
        $validLocation = is_string($this->getLocation()) == True;
        $validNumber = is_string($this->getNumber()) == True;

        if ($validName && $validLocation && $validNumber) {
            return True;
        }
        return False;
    }
}
