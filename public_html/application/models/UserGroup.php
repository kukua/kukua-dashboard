<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @since	14-04-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class UserGroup extends CI_Model {
	const TABLE = 'users_groups';

	protected $_id;
	protected $_user_id;
	protected $_group_id;

	/**
	 * @access public
	 * @param  int $userId
	 * @param  Array $data
	 * @return boolean
	 */
	public function saveBatch($userId, $data = Array()) {
		if (!is_numeric($userId) || empty($data)) {
			throw new InvalidArgumentException("Invalid params supplied");
		}

		$this->db->where('user_id', $userId);
		$this->db->delete(self::TABLE);

		$insert = [];
		if (!empty($data)) {
			foreach($data as $key => $groupId) {
				$insert[$key]["user_id"] = $userId;
				$insert[$key]["group_id"] = $groupId;
			}

			if (!empty($insert)) {
				if ($this->db->insert_batch(self::TABLE, $insert) !== false) {
					return true;
				}
			}
		}
		return false;
	}
}
