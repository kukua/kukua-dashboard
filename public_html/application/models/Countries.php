<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Countries extends CI_Model {

    const TABLE = "countries";

    public $id;
    public $code;
    public $name;
    public $created;

    public function __construct() {
        parent::__construct();

        $this->id = null;
        $this->code = null;
        $this->name = null;
        $this->created = (new DateTime())->format(DateTime::ISO8601);
    }

    /**
     * @access public
     * @param  Array $data
     * @return Countries
     */
    public function populate($data) {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }
        if (isset($data["code"])) {
            $this->code = $this->db->escape_str($data["code"]);
        }
        if (isset($data["name"])) {
            $this->name = $this->db->escape_str($data["name"]);
        }
        if (isset($data["created"])) {
            $this->created = $data["created"];
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
            $object = new Countries();
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

    /**
     * @access public
     * @return Boolean
     */
    public function delete($id = null) {
        if ($id === null) {
            $this->db->where("id", $this->id);
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
