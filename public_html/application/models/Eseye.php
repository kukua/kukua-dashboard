<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Models
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
use Curl\Curl;

class Eseye extends CI_Model {

	protected $_type;
	protected $_portfolioId;
	protected $_username;
	protected $_password;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->_portfolioId = ESEYE_PORTFOLIOID;
		$this->_username = ESEYE_USERNAME;
		$this->_password = ESEYE_PASSWORD;

		$this->_url = "https://siam.eseye.com/Japi/Tigrillo";
		if (ENVIRONMENT === "development") {
			$this->_url = "https://tigrillostaging.eseye.com/Japi/Tigrillo";
		}
	}

	/**
	 * Get single SIM card details
	 *
	 * @access public
	 * @throws Exception
	 * @return StdClass
	 */
	public function getSim($station) {
		$curl = new Curl();
		$curl->setHeader("Content-type", "application/json");
		$curl->post($this->_url . "/getCookieName");

		$cookieName = $curl->response;
		$cookieValue = $this->_login_eseye();

		try {
			$curl->setHeader("Content-type", "application/json");
			$curl->setCookie($cookieName, $cookieValue);
			$curl->post($this->_url . "/getSIMLastActivity", [
				"ICCID" => $station->getSimId()
			]);

			$response = $curl->response;
			$result = $response->info;

			$difference = 96;
			if (!empty($result->LastRadiusStop)) {
				$now = new DateTime("now", (new DateTimeZone("UTC")));
				$date = DateTime::createFromFormat("Y-m-d H:i:s", $result->LastRadiusStop);
				$difference = abs($now->getTimestamp() - $date->getTimestamp()) / 60 / 60;
			}

			switch ($difference) {
				case ($difference <= 1):
					$status = "green";
					break;
				case ($difference > 1 && $difference < 24):
					$status = "blue";
					break;
				case ($difference >= 24 && $difference < 48):
					$status = "yellow";
					break;
				case ($difference >= 48 && $difference < 96):
					$status = "orange";
					break;
				default:
					$status = "red";
					break;
			}

			$result->name   = $station->getName();
			$result->ICCID  = $station->getSimId();
			$result->status = $status;
			return $result;
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Login eseye, save token in session
	 *
	 * @access protected
	 * @throws Exception
	 * @return string
	 */
	protected function _login_eseye() {
		if (!$this->session->userdata("eseye/login")) {
			$curl = new Curl;
			$curl->setHeader("Content-type", "application/json");
			try {
				$curl->post($this->_url. "/login", [
					'username' => $this->_username,
					'password' => $this->_password,
					'portfolioId' => $this->_portfolioId
				]);

				$cookie = isset($curl->response->cookie) ? $curl->response->cookie : false;
				#set login in session
				$this->session->set_userdata("eseye/login", $cookie);
			} catch (Exception $e) {
				throw $e;
			}
		}
		return $this->session->userdata("eseye/login");
	}
}
