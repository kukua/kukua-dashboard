<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @subpackage Sources
 * @since	11-04-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Measurements extends Source {
	private $_db_host;
	private $_db_user;
	private $_db_pass;
	private $_db_name;
	private $_db;

	public $_default_columns;

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		//connect mysql and set buffer to _db
		$this->_db_host = MEASUREMENTS_HOST;
		$this->_db_user = MEASUREMENTS_USER;
		$this->_db_pass = MEASUREMENTS_PASS;
		$this->_db_name = "measurements";
		$this->_db = new MySQLi($this->_db_host, $this->_db_user, $this->_db_pass, $this->_db_name);
		if ($this->_db->connect_errno) {
			throw new Exception("No connection to measurements database");
			exit;
		}

		$this->_default_columns = [
			'Temperature' => [
				'calc' => 'AVG',
				'name' => 'temp'
			],
			'Rainfall' => [
				'calc' => 'SUM',
				'name' => 'rain'
			],
			'Humidity' => [
				'calc' => 'AVG',
				'name' => 'humid'
			],
			'WindSpeed' => [
				'calc' => 'AVG',
				'name' => 'windSpeed'
			]
		];
	}

	/**
	 * Build the query and return the output
	 *
	 * @access public
	 * @param  Source
	 * @return Array
	 */
	public function get($source, $user = null) {
		if ($source->getStation() != "") {
			return $this->getSensors($source, $user);
		} else {
			return $this->getDefault($source, $user);
		}
	}

	/**
	 *
	 */
	public function getDefault($source, $user = null) {
		$select = $this->buildSelect($source->getWeatherType(), $source->getInterval());
		$where	= $this->buildWhere($source->getDateFrom(), $source->getDateTo());
		$group	= $this->buildGroup($source->getInterval());
		$sort	= $this->buildSort();

		$stations = $this->_getStations($source, $user);

		$data = [];
		foreach($stations as $key => $station) {
			$from  = $this->buildFrom($station->getDeviceId());
			$query = $select . $from . $where . $group . $sort;
			log_message("ERROR", $query);
			$dbResult = $this->_db->query($query);

			if ($source->getWeatherType() == "all") {
				$columns = $this->_default_columns;
			} else {
				$columns[] = $this->_default_columns[$source->getWeatherType()];
			}

			$data[$key]["name"] = $station->getName();
			$data[$key]["data"] = $this->_processQuery($dbResult, $columns);
		}

		return $data;
	}

	/**
	 * Get sensor data
	 *
	 * @access public
	 * @return Array
	 */
	public function getSensors($source, $user = null) {
		$where	= $this->buildWhere($source->getDateFrom(), $source->getDateTo());
		$group	= $this->buildGroup($source->getInterval());
		$sort	= $this->buildSort();

		$stations = $this->_getStations($source, $user);
		foreach($stations as $i => $station) {

			/* If download */
			if ($source->getWeatherType() == "all") {
				$sensorData = (new StationMeasurements())->findByStationId($station->getId());
				foreach($sensorData as $sensor) {
					$columns = $this->_default_columns;
					$columns[$sensor->getName()]["name"] = $sensor->getColumn();
					$columns[$sensor->getName()]["calc"] = "AVG";
				}
			} else {
				$sensors = (new StationMeasurement())->findById($source->getWeatherType());
				$columns[$sensors->getName()]["name"] = $sensors->getColumn();
				$columns[$sensors->getName()]["calc"] = "AVG";
			}

			$select = $this->buildSelect($columns, $source->getInterval());
			$from  = $this->buildFrom($station->getDeviceId());

			$query = $select . $from . $where . $group . $sort;
			log_message("ERROR", $query);
			$dbResult = $this->_db->query($query);

			$data[$i]["name"] = $station->getName();
			$data[$i]["data"] = $this->_processQuery($dbResult, $columns);
		}
		return $data;
	}

	/**
	 * @access protected
	 * @param  int   $i		iterator
	 * @param  mixed $dbQuery
	 * @param  array $columns
	 * @return Array
	 */
	protected function _processQuery($dbResult, $columns) {
		$iterator = 0;
		$data = [];
		if ($dbResult) {
			while($rows = $dbResult->fetch_assoc()) {
				$data[$iterator][] = (int) $rows["timestamp"];
				foreach($columns as $column) {
					$data[$iterator][] = (float) $rows[$column["name"]];
				}
				$iterator++;
			}
		}
		return $data;
	}

	/**
	 * Return stations based on request
	 *
	 * @access protected
	 * @param  Source $source
	 * @return Array
	 */
	protected function _getStations($source, $user = null) {

		/** If station is supplied **/
		if ($source->getStation() !== null) {

			/* If multiple stations requested */
			if (is_array($source->getStation())) {
				foreach($source->getStations() as $station) {
					$stations[] = (new Station())->findById($source->getStation());
				}

			/* If single station requested */
			} else {
				$stations[] = (new Station())->findById($source->getStation());
			}

		} else {

			/** If user AND region is set */
			if ($source->getRegion() !== null && $user) {
				$stations = (new Station())->findByRegionIdAndUserId($source->getRegion(), $user->id);
			}

			/** If user is set */
			if ($source->getRegion() == null && $user) {
				$stations = (new UserStations())->findStationsByUserId($user->id);
			}

			/** If region is set */
			if ($source->getRegion() !== null && !$user) {
				$stations = (new Station())->findByRegionId($source->getRegion());
			}
		}

		return $stations;
	}

	/**
	 * Build the select part of the query
	 *
	 * @access public
	 * @param  mixed  $param
	 * @return string select query part
	 */
	public function buildSelect($weatherType, $interval) {

		/* Query all params */
		switch($weatherType) {
			case is_array($weatherType):
				$columns = $weatherType;
				break;

			case "all":
				$columns = $this->_default_columns;
				break;

			default:
				$keys = array_keys($this->_default_columns);
				foreach($keys as $name) {
					if ($weatherType == $name) {
						$columns[] = $this->_default_columns[$name];
						break;
					}
				}
				break;
		}

		$div = $this->getInterval($interval);
		$select  = "SELECT ";
		$select .= " (UNIX_TIMESTAMP(timestamp) - mod(UNIX_TIMESTAMP(timestamp)," . $div . ")) * 1000 as timestamp,";
		foreach($columns as $column) {
			$select .= $column['calc'] . "(" . $column['name'] . ") AS " . $column['name'] . ",";
		}

		return trim($select, ",");
	}

	/**
	 * Build the from part of the query
	 *
	 * @access public
	 * @param  int $deviceId
	 * @return string from query part
	 */
	public function buildFrom($deviceId) {
		return " FROM `" . $deviceId . "`";
	}

	/**
	 * Build the where part of the query
	 *
	 * @access public
	 * @param  string timestamp start date
	 * @param  string timestamp end date
	 * @param  string extra
	 * @return string where query part
	 */
	public function buildWhere($from, $to, $extra = null) {
		$where = " WHERE timestamp > FROM_UNIXTIME(" . $from . ") AND timestamp < FROM_UNIXTIME(" . $to . ") ";
		if ($extra !== null) {
			$where .= $extra;
		}
		return $where;
	}

	/**
	 * Build the group part of the query
	 *
	 * @access public
	 * @param  string interval
	 * @return string group query part
	 */
	public function buildGroup($interval) {
		$div = $this->getInterval($interval);
		$query = " GROUP BY UNIX_TIMESTAMP(timestamp) - UNIX_TIMESTAMP(timestamp) % " . $div;
		return $query;
	}

	/**
	 * Build the sort query
	 *
	 * @access public
	 * @return string
	 */
	public function buildSort() {
		$query = " ORDER BY timestamp ASC";
		return $query;
	}

	/**
	 * Define interval
	 *
	 * @access public
	 * @param  string $interval (5m, 1h, etc)
	 * @return int
	 */
	public function getInterval($interval) {
		switch($interval) {
			case '5m':
				$div = 60 * 5;
				break;
			case '12h':
				$div = 60 * 60 * 12;
				break;
			case '24h':
				$div = 60 * 60 * 24;
				break;
			default:
			case '1h':
				$div = 60 * 60;
				break;
		}
		return $div;
	}
}
