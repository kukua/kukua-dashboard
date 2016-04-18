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
	 * graph/index
	 *
	 * @access public
	 * @return view
	 */
	public function index() {
		if ($this->session->postDateFrom !== null) {
			$items = [
				"postDateFrom" => $this->session->postDateFrom,
				"postDateTo"   => $this->session->postDateTo,
			];

			//Assign variables to the view
			foreach($items as $key => $value) {
				$this->data[$key] = $value;
			}

			//Remove session
			$this->session->unset_userdata(array_keys($items));
		}

		$this->data["regions"] = (new UserStations())->findRegionsByUserId($this->_user->id);
		$this->load->view("graph/index", $this->data);
	}

	/**
	 * @access public
	 * @return void
	 */
	public function download() {
		$data["region"] = $this->input->post("region");
		$data["type"] = "all";
		$data["dateFrom"] = $this->input->post("from");
		$data["dateTo"] = $this->input->post("to");
		$data["interval"] = $this->input->post("interval");
		$result = $this->_call($data);
		GlobalHelper::outputCsv("export-stations", $result);
		exit;
	}

	/**
	 * @access protected
	 * @return Curl::response
	 * @todo   fix security
	 * @fixme  Insecure
	 */
	protected function _call($data = Array()) {
		$curl = new \Curl\Curl();
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
		$curl->post("https://dashboard.kukua.cc/api/sensordata/get",
			$data
		);
		return $curl->response;
	}
}
