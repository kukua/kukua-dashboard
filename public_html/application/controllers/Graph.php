<?php defined("BASEPATH") OR exit("No direct script access allowed");

class Graph extends MyController {

	const GRAPH_HISTORY  = "history";
	const GRAPH_FORECAST = "forecast";
	const GRAPH_FORECAST_T = "forecast_t";
	const GRAPH_DOWNLOAD = "download";

	/**
	 * Class constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->allow("members");
	}

	/**
	 * Weather graph
	 *
	 * @access public
	 * @return view
	 */
	public function index() {
		$this->data["regions"] = (new UserStations())->findRegionsByUserId($this->_user->id);
		$this->data["measurements"] = (new Measurements())->_default_columns;
		$this->load->view("graph/index", $this->data);
	}

	/**
	 * Misc. sensors
	 *
	 * @access public
	 * @return void
	 */
	public function sensors() {
		$this->allow('manager');
		$this->data["stations"] = (new UserStations())->findStationsByUserId($this->_user->id);
		$this->load->view("graph/sensors", $this->data);
	}

	/**
	 * Download SensorData
	 *
	 * @access public
	 * @return void
	 */
	public function download() {
		if ($this->input->post()) {

			$data["dateFrom"]	= $this->input->post("from");
			$data["dateTo"]		= $this->input->post("to");
			$data["interval"]	= $this->input->post("interval");
			$data["download"]	= true;

			if ($this->input->post("region")) {
				$data["region"]		= $this->input->post("region");
				$data["station"]	= null;
				$data["measurement"]= null;
				$data["multiple"]	= true;
			}

			if ($this->input->post("station")) {
				$data["region"]		= null;
				$data["station"]	= $this->input->post("station");
				$data["measurement"]= $this->input->post("measurement");
				$data["multiple"]	= false;
			}

			$result = (new Source($data))->get($this->_user);
			if (is_array($result)) {
				GlobalHelper::outputCsv("export-stations", $result, $data["station"]);
				exit;
			} else {
				redirect("/graph");
			}
		} else {
			redirect("/graph");
		}
	}

	/**
	 * @access public
	 * @param  int $stationId
	 * @return void
	 */
	public function getStationMeasurements($stationId) {
		if (!is_numeric($stationId)) {
			http_response_code(400);
			exit;
		}

		$measurement = new StationMeasurement();
		$measurements = $measurement->findByStationId($stationId);

		$result = [];
		foreach($measurements as $m) {
			$result[] = $m->toArray();
		}

		echo json_encode($result);
		exit;
	}
}
