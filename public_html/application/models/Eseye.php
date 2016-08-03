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
		$result = new StdClass();
		$result->LastRadiusStop = "-";
		$result->LastRadiusBytes = "";

		$statusBg = "";
		$statusText = "-";

		if ($station->getSimId()) {
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

				if (isset($response->info)) {
					$result = $response->info;

					if (!isset($result->LastRadiusStop)) {
						$result->LastRadiusStop = "0000-00-00 00:00:00";
					}
					if (!isset($result->LastRadiusBytes)) {
						$result->LastRadiusBytes = "0";
					}

					$tsDate = (new DateTime())->createFromFormat("Y-m-d H:i:s", $result->LastRadiusStop, (new DateTimeZone("UTC")));
					$statusBg   = $this->_getDifference($result->LastRadiusStop);

					// TODO(mauvm): Quick fix for not displaying Airtel SIMs.
					if ($statusBg === 'red') {
						$statusBg = '';
						$statusText = '-';
					} else {
						$statusText = $tsDate->format("Y-m-d H:i:s") . " | " . $result->LastRadiusBytes;
					}
				}
			} catch (Exception $e) {
				throw $e;
			}
		}

		/* Battery voltage */
		$batteryBg = "";
		$batteryVoltage = (new Source())->getBatteryLevel($station->getDeviceId());

		// TODO(mauvm): Remove this temporary fix after battery levels have been sanitized.
		if ($batteryVoltage > 10000) {
			$batteryVoltage /= 10;
		} else if ($batteryVoltage < 1000) {
			$batteryVoltage *= 10;
		}

		switch($batteryVoltage) {
			case ($batteryVoltage >= 4000):
				$batteryBg = "green";
				break;
			case ($batteryVoltage >= 3500 && $batteryVoltage < 4000):
				$batteryBg = "orange";
				break;
			default:
				$batteryBg = "red";
				break;
		}

		$timestamp = (new Source())->getLatestTimestamp($station->getDeviceId());
		$tsBg = $this->_getDifference($timestamp);

		/* Board temperature */
		$boardTemp = (new Source())->getMaxBoardTemp($station->getDeviceId());
		$boardTempColor = "";
		if ($boardTemp >= 40) {
			$boardTempColor = "red";
		} elseif ($boardTemp >= 35) {
			$boardTempColor = "orange";
		} elseif ($boardTemp >= 1) {
			$boardTempColor = "green";
		} else {
			$boardTemp = "-";
		}

		$boardHumid = (new Source())->getMaxHumidity($station->getDeviceId());
		$boardHumidColor = "";
		if ($boardHumid >= 50) {
			$boardHumidColor = "green";
		} elseif ($boardHumid >= 30) {
			$boardHumidColor = "orange";
		} elseif ($boardHumid >= 1) {
			$boardHumidColor = "red";
		} else {
			$boardHumid = "-";
		}

		$lastOpened = (new Source())->getLastOpenedDate($station->getDeviceId());
		$lastOpenedColor = "";
		if ($lastOpened) {
			$lastOpenedColor = "red";
		} else {
			$lastOpened = "-";
		}

		$sigQual = (new Source())->getMinSigQual($station->getDeviceId());
		$sigQualColor = "";
		if ($sigQual >= 50) {
			$sigQualColor = "red";
		} elseif ($sigQual >= 1) {
			$sigQualColor = "green";
		} else {
			$sigQual = "-";
		}

		$sigQualTime = (new Source())->getMaxSigQualMinTime($station->getDeviceId());
		$sigQualTimeColor = "";
		if ($sigQualTime >= "10") {
			$sigQualTimeColor = "red";
		} elseif ($sigQualTime >= 1) {
			$sigQualTimeColor = "green";
		} else {
			$sigQualTime = "-";
		}

		$result->name   = $station->getName();
		$result->regionId = $station->getRegionId();
		$result->ICCID  = $station->getSimId();
		$result->status = $statusText;
		$result->statusColor = $statusBg;
		$result->voltage = $batteryVoltage;
		$result->voltageColor = $batteryBg;
		$result->timestampColor = $tsBg;
		$result->timestamp = $timestamp;
		$result->boardTemp = $boardTemp;
		$result->boardTempColor = $boardTempColor;
		$result->boardHumid = $boardHumid;
		$result->boardHumidColor = $boardHumidColor;
		$result->lastOpened = $lastOpened;
		$result->lastOpenedColor = $lastOpenedColor;
		$result->sigQual = $sigQual;
		$result->sigQualColor = $sigQualColor;
		$result->sigQualTime = $sigQualTime;
		$result->sigQualTimeColor = $sigQualTimeColor;
		$result->link = $station->getLink();
		return $result;
	}

	public function _getDifference($date) {
		$tsDate = (new DateTime())->createFromFormat("Y-m-d H:i:s", $date, (new DateTimeZone("UTC")));
		$nowDate = new DateTime("now", (new DateTimeZone("UTC")));
		$difference = abs($nowDate->getTimestamp() - $tsDate->getTimestamp()) / 60 / 60;

		$color = 'red';
		switch ($difference) {
			case ($difference <= 1):
				$color = "green";
				break;
			case ($difference > 1 && $difference < 24):
				$color = "blue";
				break;
			case ($difference >= 24 && $difference < 48):
				$color = "yellow";
				break;
			case ($difference >= 48 && $difference < 96):
				$color = "orange";
				break;
			default:
				$color = "red";
				break;
		}
		return $color;
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
