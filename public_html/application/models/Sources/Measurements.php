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
				'name' => 'temp',
				'where' => 'temp < 300',
			],
			'Rainfall' => [
				'calc' => 'SUM',
				'name' => 'rain',
				'where' => 'rain < 1000',
			],
			'Pressure' => [
				'calc' => 'AVG',
				'name' => 'pressure'
			],
			'Humidity' => [
				'calc' => 'AVG',
				'name' => 'humid',
				'where' => 'humid <= 100',
			],
			'WindSpeed' => [
				'calc' => 'AVG',
				'name' => 'windSpeed',
				'where' => 'windSpeed < 3000',
			],
			'WindDir' => [
				'calc' => 'AVG',
				'name' => 'windDir',
				'where' => 'windDir < 360 AND windDir >= 0',
			],
			'SolarRad' => [
				'calc' => 'AVG',
				'name' => 'solarIrrad'
			],
			'Battery' => [
				'calc' => 'AVG',
				'name' => 'battery'
			],
		];
	}

	/**
	 * Build the query and return the output
	 *
	 * @access public
	 * @param  Source
	 * @return Array
	 */
	public function get($source, $user = false) {
		if ($source->getStation() != null) {
			$station = $this->_getStations($source, $user);
			$columns = $this->_defineColumns($source);

			return [$this->getMeasurement($source, $user, $station, $columns)];
		} else {
			return $this->getMeasurements($source, $user);
		}
	}

	/**
	 * @access public
	 * @return void
	 */
	public function getMeasurements($source, $user = false) {
		$data = [];

		$stations = $this->_getStations($source, $user);
		foreach ($stations as $i => $station) {
			$columns = $this->_defineColumns($source);
			$result = $this->getMeasurement($source, $user, $station, $columns);

			if (!is_null($result)) {
				$data[] = $result;
			}
		}

		return $data;
	}

	/**
	 * @access public
	 * @return Array
	 */
	public function getMeasurement($source, $user = false, $station = null, $columns = null) {
		if ($station == null) {
			$station = $this->_getStations($source, $user);
		}

		$dbResult = $this->_buildQuery($source, $station, $columns);
		if ($dbResult !== false) {
			/* Output returns array key's numbered*/
			return $this->_output($dbResult, $station, $columns, $source->getDownload());
		}

		return null;
	}

	/**
	 *
	 */
	protected function _defineColumns($source) {
		$return = [];

		if ($source->getMeasurement()) {

			//Try to loop over default columns
			foreach($this->_default_columns as $column) {
				if ($column['name'] == $source->getMeasurement()) {
					$return[$source->getMeasurement()] = $column;
				}
			}

			if (empty($return)) {
				$return = [
					$source->getMeasurement() => [
						'name' => $source->getMeasurement(),
						'calc' => 'AVG'
					]
				];
			}
		} else {
			$return = $this->_default_columns;
		}

		return $return;
	}

	/**
	 * @access protected
	 * @return void
	 */
	protected function _buildQuery($source, $station, $columns) {
		$select = $this->buildSelect($columns, $source->getInterval(), $station->getDeviceId());
		$from	= $this->buildFrom($station->getDeviceId());
		$extra	= $this->buildExtraWhere($columns);
		$where	= $this->buildWhere($source->getDateFrom(), $source->getDateTo(), $extra);

		$group	= $this->buildGroup($source->getInterval());
		$sort	= $this->buildSort();

		$query = $select . $from . $where . $group . $sort;

		print_r($query);
		die();
		log_message("ERROR", $query);

		return $this->_db->query($query);
	}

	/**
	 * @access protected
	 * @return void
	 */
	protected function _output($dbResult, $station, $column, $download) {
		if ($download == true) {
			$result = $dbResult->fetch_all(MYSQLI_ASSOC);
			$return['station'] = $station;
			$return['header'] = array_keys($column);

			foreach ($result as $key => $values) {
				if (isset($values['timestamp'])) {
					$date = new DateTime();
					$date->setTimestamp( ($values['timestamp'] / 1000) );
					$convertedData['timestamp'] = $date->format('Y-m-d H:i:s');
				}

				foreach ($column as $key => $val) {
					if (isset($values[$val["name"]])) {
						$convertedData[$key] = (float) round($values[$val["name"]], 2);
					}
				}
				$return['data'][] = $convertedData;
			}

		} else {
			$return["name"] = $station->getName();
			foreach ($dbResult->fetch_all(MYSQLI_NUM) as $i => $res) {
				if (isset($res[1])) {
					$return['data'][$i][0] = (int) $res[0];
					$return['data'][$i][1] = (float) round($res[1], 2);
				}
			}
		}

		return $return;
	}

	/**
	 * Build the select part of the query
	 *
	 * @access public
	 * @param  mixed  $param
	 * @return string select query part
	 */
	public function buildSelect($columns, $interval, $deviceId) {
		$div = $this->getInterval($interval);
		$select  = "SELECT ";
		$select .= " (UNIX_TIMESTAMP(timestamp) - mod(UNIX_TIMESTAMP(timestamp)," . $div . ")) * 1000 as timestamp,";
		foreach($columns as $column) {

			/* skip SolarIrrad for classic devices */
			if (substr($deviceId, 0, 8) == "4a000000" && $column["name"] == "solarIrrad") {
				continue;
			}

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
	 * Build the extra conditionals for the where part of the query
	 *
	 * @access public
	 * @param  array $columns
	 * @return string where query part
	 */
	public function buildExtraWhere($columns) {
		$extra = '';

		foreach ($columns as & $column) {
			if (empty($column['where'])) continue;

			$extra .= ' AND (' . $column['where'] . ')';
		}

		return $extra;
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

	/**
	 *
	 * HEADS UP
	 * If the param type = "all" the keys in the returning array are
	 * filled with column names from the database.
	 * This occurs when downloading data for a station.
	 *
	 * For type = "all" the timestamp is converted to date / time
	 *
	 * @example Download
	 *			$data[0]['timestamp'] = '';
	 *			$data[1]['temp'] = '';
	 *
	 * @example Chart
	 *			$data[0][0] = ''; (timestamp)
	 *			$data[0][1] = ''; (value, i.e. temp)
	 *
	 * @access protected
	 * @param  int	 $i		iterator
	 * @param  mixed $dbQuery
	 * @param  array $columns
	 * @return Array
	 */
	protected function _processQuery($dbResult, $columns, $type = "") {
		$iterator = 0;
		$data = [];
		if ($dbResult) {
			while($rows = $dbResult->fetch_assoc()) {

				/* Convert timestamp to human readable DateTime for downloads */
				if ($type == "all") {
					$date = new DateTime();
					$date->setTimestamp( ($rows["timestamp"] / 1000) );
					$data[$iterator]['timestamp'] = $date->format("Y-m-d H:i:s");
				} else {
					$data[$iterator][] = (int) $rows["timestamp"];
				}

				foreach($columns as $column) {
					if (isset($rows[$column["name"]])) {

						/* Convert number to type for downloads */
						if ($type == "all") {
							$data[$iterator][$column["name"]] = (float) round($rows[$column["name"]], 2);
						} else {
							$data[$iterator][] = (float) round($rows[$column["name"]], 2);
						}
					}
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
	 * @param  StdClass $user
	 * @return Array
	 */
	protected function _getStations($source, $user = false) {
		if ($source->getMultiple() === true) {
			if ($user !== false) {

				#Find by region and user id
				$stations = (new Station())->findByRegionIdAndUserId($source->getRegion(), $user->id);
			} else {

				#Find by region id
				$stations = (new Station())->findByRegionId($source->getRegion());
			}
		} else {

			#Specific station
			$stations = (new Station())->findById($source->getStation());
		}

		return $stations;
	}

	/**
	 * Single query
	 *
	 * @access public
	 * @param  String $query
	 * @return Array
	 */
	public function single($query) {
		$dbResult = $this->_db->query($query);
		if ($dbResult) {
			return $dbResult->fetch_assoc();
		}
		return $dbResult;
	}
}
