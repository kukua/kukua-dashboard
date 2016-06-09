<?php
#This file can be called directly

/**
 * @package Controllers
 * @subpackage Api
 * @since	01-03-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Sensordata extends MyController {

	protected $_request;
	protected $_message;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->_request["interval"] = "5m";
	}

	/**
	 * Convert request to data result
	 *
	 * @access public
	 * @fixme  Authentication / request validation
	 * @return void
	 */
	public function get($forecast = false, $data = false) {
		$validRequest	= $this->_validateRequest() !== False;
		$validAuth		= $this->_validateAuth() !== False;

		$post = ($data !== false) ? $data : $this->input->post();

		$post["download"] = false;
		if (isset($post["region"])) {
			$post["multiple"] = true;
		} else {
			$post["multiple"] = false;
		}

		if ($validRequest && $validAuth) {
			$source = new Source($post);
			$res = $source->get($this->_user);
			if ($forecast != false) {
				if ($this->_user) {
					$result = $source->gatherForecast($this->_user);
					if (isset($result[0])) {
						array_push($res, $result[0]);
					}
				}
			}
			echo json_encode($res);
		} else {
			http_response_code(400);
			echo json_encode(["message" => $this->_message]);
		}
		exit;
	}

	/**
	 * @access public
	 * @return void
	 * @depricated
	 */
	public function forecast() {
		if ($this->_validateRequest() !== False) {
			$this->_populate($this->input->post());

			$source = new Source($this->_request);
			echo json_encode($source->gatherForecast());
			exit;
		}
	}

	/**
	 * @access private
	 * @param  Array $data
	 * @return Array
	 */
	private function _populate($data) {
		if (isset($data["region"])) {
			$this->_request["region"] = $data["region"];
		}
		if (isset($data["station"]) && $data["station"] != "") {
			$this->_request["station"] = $data["station"];
		}
		if (isset($data["type"])) {
			$this->_request["type"] = $data["type"];
		}
		if (isset($data["dateFrom"])) {
			$this->_request["dateFrom"] = $data["dateFrom"];
		}
		if (isset($data["dateTo"])) {
			$this->_request["dateTo"] = $data["dateTo"];
		}
		if (isset($data["interval"])) {
			$this->_request["interval"] = $data["interval"];
		}
		if (isset($data["range"])) {
			$this->_request["range"] = $data["range"];
		}
	}

	/**
	 * Check if the request made is valid by
	 * checking if all the required parameters
	 * are given
	 *
	 * @access	 private
	 * @response 200|400
	 * @return	 boolean
	 */
	private function _validateRequest() {
		$valid = True;
		$err = Array();

		if ($this->input->post("dateFrom") == False) {
			$valid = False;
			$err[] = "No date from supplied";
		}
		if ($this->input->post("dateTo") == False) {
			$valid = False;
			$err[] = "No date to supplied";
		}
		if ($this->input->post("interval") == False) {
			$valid = False;
			$err[] = "No interval to supplied";
		}

		if ($valid !== True) {
			$this->_message = implode("\r\n", $err);
		}

		return $valid;
	}

	/**
	 * @todo Implement function
	 */
	private function _validateAuth() {
		return true;
	}
}
