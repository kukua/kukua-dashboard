<?php defined('BASEPATH') OR exit('No direct script access allowed');

class UserFeedback extends CI_Model {

	const TABLE = "users_feedback";

	protected $_id;
	protected $_user_id;
	protected $_email;
	protected $_feedback;
	protected $_created;
	protected $_completed;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->_id			= null;
		$this->_user_id		= null;
		$this->_email		= null;
		$this->_feedback	= null;
		$this->_created		= (new DateTime())->format(DateTime::ISO8601);
		$this->_completed	= 0;
	}

	/**
	 * @access public
	 * @param  int
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
	 * @return void
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * @access public
	 * @param  int
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
	 * @param  string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setEmail($email) {
		if (!is_string($email)) {
			throw new InvalidArgumentException("No valid email supplied");
		}
		$this->_email = $email;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getEmail() {
		return $this->_email;
	}

	/**
	 * @access public
	 * @param  string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setFeedback($feedback) {
		if (!is_string($feedback)) {
			throw new InvalidArgumentException("No valid feedback supplied");
		}
		$this->_feedback = $feedback;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getFeedback() {
		return $this->_feedback;
	}

	/**
	 * @access public
	 * @param  string
	 * @throws InvalidArgumentException
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
	 * @param  enum(0,1)
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setCompleted($completed) {
		if (!is_numeric($completed)) {
			throw new InvalidArgumentException("No valid completed state supplied");
		}
		$this->_completed = $completed;
	}

	/**
	 * @access public
	 * @return enum(0,1)
	 */
	public function getCompleted() {
		return $this->_completed;
	}

	/**
	 * @access public
	 * @param  Array $data
	 * @return UserFeedback
	 */
	public function populate($data) {
		if (isset($data["id"])) {
			$this->setId($data["id"]);
		}
		if (isset($data["user_id"])) {
			$this->setUserId($data["user_id"]);
		}
		if (isset($data["email"])) {
			$this->setEmail($data["email"]);
		}
		if (isset($data["feedback"])) {
			$this->setFeedback($this->db->escape_str($data["feedback"]));
		}
		if (isset($data["created"])) {
			$this->setCreated($data["created"]);
		}
		if (isset($data["completed"])) {
			$this->setCompleted($data["completed"]);
		}
		return $this;
	}

	/**
	 * @access public
	 * @return Array
	 */
	public function toArray() {
		return [
			'id'		=> $this->getId(),
			'user_id'	=> $this->getUserId(),
			'email'		=> $this->getEmail(),
			'feedback'	=> $this->getFeedback(),
			'created'	=> $this->getCreated(),
			'completed' => $this->getCompleted()
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
	 * @return UserFeedback
	 */
	public function findById($id) {
		$this->db->select("uf.*, u.first_name, u.last_name");
		$this->db->from("users_feedback uf");
		$this->db->join("users u", "uf.user_id = u.id");
		$this->db->where("uf.id", $id);
		$get = $this->db->get();
		return $this->populate($get->row_array());
	}

	/**
	 * @access public
	 * @return UserFeedback
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
	 * @access private
	 * @return boolean
	 */
	private function _validate() {
		$validUserId	= $this->getUserId()  !== null;
		$validFeedback	= $this->getFeedback() !== null;
		if ($validUserId && $validFeedback) {
			return true;
		}
		return false;
	}
}
