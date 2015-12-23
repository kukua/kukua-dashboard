<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback_model extends CI_Model {

    const TABLE = "users_feedback";

    public $id;
    public $user_id;
    public $email;
    public $feedback;
    public $created;
    public $completed;

    /**
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->created = (new DateTime())->format(DateTime::ISO8601);
    }

    /**
     * @access public
     * @param  Array $data
     * @return Feedback_model
     */
    public function populate($data) {
        if (isset($data["id"])) {
            $this->id = $data["id"];
        }
        if (isset($data["user_id"])) {
            $this->user_id = $data["user_id"];
        }
        if (isset($data["email"])) {
            $this->email = $data["email"];
        }
        if (isset($data["feedback"])) {
            $this->feedback = $data["feedback"];
        }
        if (isset($data["created"])) {
            $this->created = $data["created"];
        }
        if (isset($data["completed"])) {
            $this->completed = $data["completed"];
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
        $this->db->select("uf.*, u.first_name, u.last_name");
        $this->db->from("users_feedback uf");
        $this->db->join("users u", "uf.user_id = u.id");
        $this->db->order_by("uf.completed", "ASC");
        $this->db->order_by("uf.created", "DESC");
        $get = $this->db->get();
        return $get->result_array();
    }

    /**
     * @access public
     * @return Feedback_model
     */
    public function findById($id) {
        $this->db->select("uf.*, u.first_name, u.last_name");
        $this->db->from("users_feedback uf");
        $this->db->join("users u", "uf.user_id = u.id");
        $this->db->where("uf.id", $id);
        $get = $this->db->get();
        return $get->row();
    }

    /**
     * @access public
     * @return Feedback_model
     */
    public function delete($id) {
        if ($this->findById($id) !== false) {
            if ($this->db->delete(self::TABLE, array('id' => $id))) {
                return true;
            }
        }
        return false;
    }

    /**
     * @access private
     * @return boolean
     */
    private function _validate() {
        $validUserId    = $this->user_id  !== null;
        $validFeedback  = $this->feedback !== null;
        if ($validUserId && $validFeedback) {
            return true;
        }
        return false;
    }
}
