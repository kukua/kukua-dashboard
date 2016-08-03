<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Source extends CI_Model {

	protected $_region;
	protected $_station;
	protected $_measurement;
	protected $_dateFrom;
	protected $_dateTo;
	protected $_interval;
	protected $_multiple;
	protected $_download;

	/**
	 * Class constructor
	 * If params given, populate object
	 *
	 * @fix me
	 * @fixme  authentication
	 * @fix me
	 */
	public function __construct($request = null) {
		parent::__construct();

		if (is_array($request)) {
			$this->populate($request);
		}
	}

	/**
	 * @access public
	 * @param  string $region
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setRegion($region) {
		if (!is_numeric($region)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}
		$this->_region = $region;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getRegion() {
		return $this->_region;
	}

	/**
	 * @access public
	 * @param  int $station
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setStation($station) {
		if (!is_string($station)) {
			throw new InvalidArgumentException("Invalid param supplied");
		}
		$this->_station = $station;
	}

	/**
	 * @access public
	 * @return int
	 */
	public function getStation() {
		return $this->_station;
	}

	/**
	 * @access public
	 * @param string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setMeasurement($measurement) {
		$this->_measurement = $measurement;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getMeasurement() {
		return $this->_measurement;
	}

	/**
	 * Set date, if string create datetime object
	 * multiple formats (timestamp | yyyy-mm-dd HH:ii)
	 *
	 * @access public
	 * @param string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setDateFrom($date) {
		if (!is_string($date)) {
			throw new InvalidArgumentException("Param supplied not a string");
		}

		$this->_dateFrom = $date;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getDateFrom() {
		return $this->_dateFrom;
	}

	/**
	 * Set date, if string create datetime object
	 * multiple formats (timestamp | yyyy-mm-dd HH:ii)
	 *
	 * @access public
	 * @param string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setDateTo($date) {
		if (!is_string($date)) {
			throw new InvalidArgumentException("Param supplied not a string");
		}

		$this->_dateTo = $date;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getDateTo() {
		return $this->_dateTo;
	}

	/**
	 * One of 5m, 1h, 12h 24h
	 *
	 * @access public
	 * @param string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setInterval($interval) {
		if (!is_string($interval)) {
			throw new InvalidArgumentException("Param supplied not a string");
		}

		$this->_interval = $interval;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getInterval() {
		return $this->_interval;
	}

	public function setMultiple($multiple) {
		$this->_multiple = $multiple;
	}

	public function getMultiple() {
		return $this->_multiple;
	}

	public function setDownload($download) {
		$this->_download = $download;
	}

	public function getDownload() {
		return $this->_download;
	}

	/**
	 * Populate object
	 *
	 * @access public
	 * @param  Array $data
	 * @return Source
	 */
	public function populate($data) {
		if (!is_array($data)) {
			throw new InvalidArgumentException("No valid param given");
		}

		if (isset($data["region"])) {
			$this->setRegion($data["region"]);
		}
		if (isset($data["station"])) {
			$this->setStation($data["station"]);
		}
		if (isset($data["measurement"])) {
			$this->setMeasurement($data["measurement"]);
		}
		if (isset($data["dateFrom"])) {
			$this->setDateFrom($data["dateFrom"]);
		}
		if (isset($data["dateTo"])) {
			$this->setDateTo($data["dateTo"]);
		}
		if (isset($data["interval"])) {
			$this->setInterval($data["interval"]);
		}
		if (isset($data["multiple"])) {
			$this->setMultiple($data["multiple"]);
		}
		if (isset($data["download"])) {
			$this->setDownload($data["download"]);
		}
		return $this;
	}

	public function get($user = null) {
		return ($this->getMultiple() == true) ? $this->getMultipleStations($user) : $this->getSingleStation($user);
	}

	/**
	 * Gather data from DB for multiple stations
	 *
	 * @access public
	 * @param  User
	 * @return Array
	 */
	public function getMultipleStations($user = null) {
		$result = [];
		$measurements = (new Measurements())->get($this, $user);
		if (isset($measurements[0])) {
			$result = $measurements;
		}
		return $result;
	}

	/**
	 * Gather data from DB for a single station
	 *
	 * @access public
	 * @param  User
	 * @return Array
	 */
	public function getSingleStation($user = null) {
		return (new Measurements())->get($this, $user);
	}

	/**
	 * @access public
	 * @param  User
	 * @return Array
	 */
	public function gatherForecast($user = null) {
		$result = [];
		switch($this->getRegion()) {
		case '1':
			$stations = ['hourly_02339354'];
			break;
		case '2':
			$stations = ['hourly_100156918'];
			break;
		default:
			$stations = [];
			break;
		}

		$object = new Foreca();
		$objResult = $object->get($this, $stations);
		if (isset($objResult[0])) {
			$result = $objResult;
		}
		return $result;
	}

	/**
	 * Get latest battery level
	 *
	 * @access public
	 * @param  DeviceId
	 * @return int
	 */
	public function getBatteryLevel($deviceId) {
		$object = new Measurements();
		$query = "SELECT * FROM `" . $deviceId . "` ORDER BY timestamp DESC LIMIT 1";
		$value = $object->single($query);

		if (isset($value["battery"])) {
			return $value["battery"];
		}
		else {
			return 0;
		}
	}

	/**
	 * Get latest Timestamp
	 *
	 * @access public
	 * @param  DeviceId
	 * @return int
	 */
	public function getLatestTimestamp($deviceId) {
		$today = (new DateTime(null, new DateTimeZone('UTC')));
		$object = new Measurements();
		$query = "SELECT timestamp FROM `" . $deviceId . "` WHERE timestamp <= FROM_UNIXTIME(" . (int) $today->getTimestamp() . ") ORDER BY timestamp DESC LIMIT 1";
		$value = $object->single($query);

		return (isset($value["timestamp"])) ? $value['timestamp'] : '0000-00-00 00:00:00';
	}

	public function getExceedBoardTempDate($deviceId) {
		// Returns datetime if board has been overheated in last 7 days
		$today = (new DateTime());
		$before = (new DateTime())->modify('-7 days');

		$object = new Measurements();
		$query = "SELECT timestamp FROM `" . $deviceId . "` WHERE UNIX_TIMESTAMP(timestamp) BETWEEN " . $before->getTimestamp() . " AND " . $today->getTimestamp() . " AND bmpTemp >= 45 ORDER BY timestamp DESC LIMIT 1";
		$value = $object->single($query);
		if (isset($value["bmpTemp"])) {
			return $value["bmpTemp"];
		}

		return "";
	}

	public function getMaxHumidity($deviceId) {
		$today = (new DateTime());
		$before = (new DateTime())->modify('-7 days');

		$object = new Measurements();
		$query = "SELECT humid FROM `" . $deviceId . "` WHERE UNIX_TIMESTAMP(`timestamp`) BETWEEN " . $before->getTimestamp() . " AND " . $today->getTimestamp() . " ORDER BY `humid` DESC LIMIT 1";
		$value = $object->single($query);
		if (isset($value["humid"])) {
			return $value["humid"];
		}
		return "";
	}

	public function getLastOpenedDate ($deviceId) {
		// Returns datetime if board has been opened in the last 7 days
		$today = (new DateTime());
		$before = (new DateTime())->modify('-7 days');

		$object = new Measurements();
		$query = "SELECT timestamp FROM `" . $deviceId . "` WHERE UNIX_TIMESTAMP(timestamp) BETWEEN " . $before->getTimestamp() . " AND " . $today->getTimestamp() . " AND lightSensMax > 100 ORDER BY timestamp DESC LIMIT 1";
		$value = $object->single($query);
		if (isset($value["timestamp"])) {
			return $value["timestamp"];
		}
		return "";
	}

	public function getMaxSigQualMinTime($deviceId) {
		$today = (new DateTime());
		$before = (new DateTime())->modify('-7 days');

		$object = new Measurements();
		$query = "SELECT sigQualMinTime FROM `" . $deviceId . "` WHERE UNIX_TIMESTAMP(`timestamp`) BETWEEN " . $before->getTimestamp() . " AND " . $today->getTimestamp() . " ORDER BY `sigQualMinTime` DESC LIMIT 1";
		$value = $object->single($query);
		if (isset($value["sigQualMinTime"])) {
			return $value["sigQualMinTime"];
		}
		return "";
	}

	public function getMinSigQual($deviceId) {
		$today = (new DateTime());
		$before = (new DateTime())->modify('-7 days');

		$object = new Measurements();
		$query = "SELECT * FROM `" . $deviceId . "` WHERE UNIX_TIMESTAMP(`timestamp`) BETWEEN " . $before->getTimestamp() . " AND " . $today->getTimestamp() . " ORDER BY `sigQual` ASC LIMIT 1";
		$value = $object->single($query);
		if (isset($value["sigQual"])) {
			return $value["sigQual"];
		}
		return "";
	}
}
