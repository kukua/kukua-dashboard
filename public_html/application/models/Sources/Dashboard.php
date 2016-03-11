<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Models
 * @subpackage Sources
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Dashboard extends Source {

	private $_token;
	private $_url;
	private $_port;
	private $_db;
	private $_suffix;

	protected $_select;
	protected $_from;
	protected $_where;
	protected $_group;
	protected $_order;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->_token  = "cm9vdDo2NjhiYjg1NDVjYmM2MTE1ZWY0Yjk1OTYz";
		$this->_url = "http://d.kukua.cc";
		$this->_port = ":9003";
		$this->_db = "data";
		$this->_suffix = "/query";
	}

	/**
	 * @access public
	 * @param  Source
	 * @return Array
	 */
	public function get($source) {
		$stations = (new Station())->findByCountryId($source->getCountry());
		$dates["dateFrom"] = $source->getDateFrom();
		$dates["dateTo"]   = $source->getDateTo();

		$query = [];
		$query["where"] = $this->getWhere($dates);
		$query["group"] = $this->getGroup($source->getInterval());

		$result = [];

		if (strtolower($source->getWeatherType()) != "all") {
			foreach($stations as $key => $station) {

				$column = (new StationColumn())->find($station->getId(), $source->getWeatherType());
				if ($column !== false) {
					$build = $this->_build($query, $station, $column);
					if ($build !== null) {
						$result[] = $build;
					}
				}
			}
		} else {
			foreach($stations as $station) {
				$columns = (new StationColumn())->findByStationId($station->getId());

				#temp hack to add a where clause
				if (strpos($station->getName(), ";") !== false) {
					$query["where"] = $this->andWhere($query["where"], $station);
					if ($station->getStationId() == "Foreca") {
						$query["group"] = $this->getGroup("1h");
					}
				}

				$query["from"]	 = $this->getFrom($station->getStationId());
				$query["select"] = "SELECT ";
				foreach($columns as $column) {
					$query["select"] = $this->addSelect($query["select"], $column);
				}

				$query["select"] = rtrim($query["select"], ",");
				if (!empty($query["select"])) {
					$q = $query["select"] . " " . $query["from"] . " " . $query["where"] . " " . $query["group"];
					log_message('error', $q);
					$values = $this->_parse($q);
					if (count($values) > 0) {
						$result[] = $values;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Building the query
	 *
	 * @access protected
	 * @param  string $query
	 * @param  Station $station
	 * @param  string $column
	 * @return string json
	 */
	protected function _build($query, $station, $column) {
		/* temp hack to add a where clause */
		if (strpos($station->getName(), ";") !== false) {
			$query["where"] = $this->andWhere($query["where"], $station);
		}

		/* temp hack to check if forecast (only 1h grouping available) */
		if ($station->getStationId() == "Foreca") {
			$query["group"] = $this->getGroup("1h");
		}

		$query["select"] = $this->getSelect($column);
		$query["from"]	 = $this->getFrom($station->getStationId());
		if (!empty($query["select"]) && !empty($query["from"])) {
			$q = $query["select"] . " " . $query["from"] . " " . $query["where"] . " " . $query["group"];
			log_message('error', $q);
			$values = $this->_parse($q);
			if (count($values) >= 1) {
				return $values;
			}
		}
	}

	/**
	 * Get select (query)
	 *
	 * @access public
	 * @param  string $column
	 * @return string
	 */
	public function getSelect($column) {
		$prefix = "mean(";
		$countryColumn = (new CountryColumn())->findById($column->getCountryColumnId());
		if ($countryColumn->getName() == "Rainfall") {
			$prefix = "sum(";
		}

		return "SELECT " . $prefix . $column->getValue() . ") as " . $countryColumn->getName();
	}

	/**
	 * Add to select (query)
	 *
	 * @access public
	 * @param  string $select
	 * @param  string $column
	 * @return string
	 */
	public function addSelect($select, $column) {
		$prefix = "mean(";
		$countryColumn = (new CountryColumn())->findById($column->getCountryColumnId());
		if ($countryColumn->getName() == "Rainfall") {
			$prefix = "sum(";
		}
		$select .= " " . $prefix . $column->getValue() . ") as " . $countryColumn->getName() . ",";
		return $select;
	}

	/**
	 * Get from clause (query)
	 *
	 * @access public
	 * @return string
	 */
	public function getFrom($stationId) {
		return " FROM " . $stationId;
	}

	/**
	 * Get where clause (query)
	 *
	 * @access public
	 * @return string
	 */
	public function getWhere($where) {
		if (isset($where["dateFrom"]) && isset($where["dateTo"])) {
			$query = "time > " . $where["dateFrom"] . "s AND time < " . $where["dateTo"] . "s";
		}
		return "WHERE " . $query;
	}

	/**
	 * Get group clause (query)
	 *
	 * @access public
	 * @return string
	 */
	public function getGroup($interval) {
		return "GROUP BY time(" . $interval . ")";
	}

	/**
	 * @access	public
	 * @param	string $where
	 * @param	Station
	 * @return	string
	 *
	 * @example data;deviceId=abc
	 * @example foreca;id=abc;column=value
	 */
	public function andWhere($where, $station) {
		$extra = explode(";", $station->getName());
		foreach($extra as $k => $keyval) {
			if ($k == 0) continue;

			$res = explode("=", $keyval);
			$where .= " AND " . $res[0] . " = '" . $res[1] . "'";
		}

		if ($extra[0] == "forecast") {
			$where .= " AND time > now()";
		}
		return $where;
	}

	/**
	 * Query the database and manipulate data
	 *
	 * @access protected
	 * @param  string $q
	 * @return Array
	 */
	protected function _parse($q) {
		$opts = [
			"q" => $q,
			"db" => $this->_db,
		];
		$request = $this->_curl($opts, true);
		if ($request != false || $request != "" || $request != null) {
			$responses = $request->results;
			if (isset($responses[0]->series) !== false) {
				$response = $this->_manipulate($responses[0]->series);
				return $response[0];
			}
		}
		return [];
	}

	/**
	 * Curl request
	 *
	 * @access protected
	 * @return Array
	 */
	protected function _curl($opts = [], $headers = False) {
		$curl = new \Curl\Curl();
		if ($headers !== false) {
			$curl->setHeader("Authorization", "Basic " . $this->_token);
		}

		$url = $this->_url . $this->_port . $this->_suffix;
		$result = $curl->get($url, $opts);
		return $result;
	}

	/**
	 * Manipulating data to get a format we can work with
	 *
	 * @access protected
	 * @param  Array $data
	 * @return Array
	 */
	protected function _manipulate($data) {
		if (count($data)) {
			foreach($data as $station => $values) {
				if (count($values->values)) {

					/* Set correct name */
					$niceName = (new Station())->findByStationId($values->name)->getName();
					if (strpos($niceName, ";") !== false) {
						$extra = explode(";", $niceName);
						$values->name = ucfirst($extra[0]);
					} else {
						$values->name = ucfirst($niceName);
					}

					/* Set correct date */
					foreach($values->values as $key => $points) {
						$points[0] = str_replace("Z", "", $points[0]);
						$points[0] = str_replace("T", " ", $points[0]);
						$new = DateTime::createFromFormat("Y-m-d H:i:s", $points[0]);

						if ($new !== false) {
							/* multiply by 1000 (milliseconds) */
							$data[$station]->values[$key][0] = $new->getTimestamp() * 1000;
						}
					}
				}
			}
		}
		return $data;
	}
}
