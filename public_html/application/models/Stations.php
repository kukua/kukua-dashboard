<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Stations extends CI_Model {

    const TABLE = "stations";

    public $id;
    public $country_id;
    public $station_id;
    public $name;
    public $active;

    public function __construct() {
        parent::__construct();

        $this->id = null;
        $this->country_id = null;
        $this->station_id = null;
        $this->name = null;
        $this->active = 1;
    }

    public function populate($data) {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }
        if (isset($data["country_id"])) {
            $this->country_id = $data["country_id"];
        }
        if (isset($data["station_id"])) {
            $this->station_id = $this->db->escape_str($data["station_id"]);
        }
        if (isset($data["name"])) {
            $this->name = $this->db->escape_str($data["name"]);
        }
        if (isset($data["active"])) {
            $this->active = $data["active"];
        }
        return $this;
    }

    /**
     * @access public
     * @return boolean
     */
    public function save() {
        if ($this->_validate() === false) {
            return false;
        }

        if (is_null($this->id) === true) {
            if ($this->db->insert(self::TABLE, (array) $this)) {
                $this->id = $this->db->insert_id();
                return $this;
            }
        } else {
            $this->db->where('id', $this->id);
            if ($this->db->update(self::TABLE, (array) $this)) {
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
            $object = new Stations();
            $result[] = $object->populate($value);
        }
        return $result;
    }

    /**
     * @access public
     * @return Countries
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

    public function delete($id) {
        if ($this->findById($id) !== false) {
            if ($this->db->delete(self::TABLE, array('id' => $id))) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     */
    public function deleteWhere($x, $y) {
        $this->db->where($x, $y);
        $this->db->delete(self::TABLE);
    }

    /**
     * @access private
     * @return boolean
     */
    private function _validate() {
        return true;
    }

}
