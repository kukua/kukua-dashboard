<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Source extends CI_Model {

	protected $_region;
	protected $_type;
	protected $_dateFrom;
	protected $_dateTo;
	protected $_interval;
	protected $_range;

	/**
	 * Class constructor
	 * If params given, populate object
	 *
	 * @fix me
	 * @fixme  authentication (JWT?)
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
	 * @return string
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
	 * @param string
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setWeatherType($type) {
		$this->_type = $type;
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getWeatherType() {
		return $this->_type;
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

	/**
	 *
	 */
	public function setRange($range) {
		if (!is_string($range)) {
			throw new InvalidArgumentException("Param supplied not a string");
		}

		$this->_range = $range;
	}

	/**
	 *
	 */
	public function getRange() {
		return $this->_range;
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
		if (isset($data["type"])) {
			$this->setWeatherType($data["type"]);
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
		if (isset($data["range"])) {
			$this->setRange($data["range"]);
		}

		return $this;
	}

	/**
	 * Gather data from DB
	 *
	 * @access public
	 * @param  User
	 * @return Array
	 */
	public function gather($user = null) {
		require_once(APPPATH . "models/Sources/Measurements.php");

		$result = [];
		$object = new Measurements();
		$measurements = $object->get($this, $user);
		if (isset($measurements[0])) {
			$result = $measurements;
		}
		return $result;
	}

	/**
	 * @access public
	 * @param  User
	 * @return Array
	 */
	public function gatherForecast($user = null) {
		require_once(APPPATH . "models/Sources/Foreca.php");

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
}
