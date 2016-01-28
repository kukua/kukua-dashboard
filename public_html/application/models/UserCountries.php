<?php defined("BASEPATH") OR exit("No direct script access allowed");

class UserCountries extends CI_Model {

    const TABLE = "users_countries";

    public $id;
    public $user_id;
    public $country_id;

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     */
    public function populate($data) {
        $object = new UserCountries();
        if (isset($data["id"])) {
            $object->id = $data["id"];
        }
        if (isset($data["user_id"])) {
            $object->user_id = $data["user_id"];
        }
        if (isset($data["country_id"])) {
            $object->country_id = $data["country_id"];
        }
        return $object;
    }

    /**
     *
     */
    public function save($id, $data) {
        //delete current user points
        $this->db->where("user_id", $id);
        $this->db->delete(self::TABLE);

        $inserting = [];
        foreach($data as $key => $country_id) {
            if (is_numeric($country_id)) {
                $inserting[$key]["user_id"]    = $id;
                $inserting[$key]["country_id"] = $country_id;
            }
        }

        if (!empty($inserting)) {
            $this->db->insert_batch(self::TABLE, $inserting);
        }
    }

    /**
     *
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
     *
     */
    public function deleteWhere($x, $y) {
        $this->db->where($x, $y);
        $this->db->delete(self::TABLE);
    }
}
