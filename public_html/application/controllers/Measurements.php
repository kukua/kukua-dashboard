<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Controllers
 * @since	26-04-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 * @copyright 2016 Kukua B.V.
 */
class Measurements extends MyController {

	/**
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->allow('manager');
	}

	/**
	 * @access public
	 * @param  int $stationId
	 * @return void
	 */
	public function index($stationId) {
		if (!is_numeric($stationId)) {
			Notification::set(Stations::WARNING, "No access allowed");
			redirect("/graph");
		}

		$this->data["stationId"] = $stationId;
		$this->data["measurements"] = (new StationMeasurement())->findByStationId($stationId);
		$this->load->view("measurements/index", $this->data);
	}

	/**
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function create($stationId) {
		if (!is_numeric($stationId)) {
			Notification::set(Measurements::WARNING, "No access allowed");
			redirect("/graph");
		}

		$measurement = new StationMeasurement();
		if ($this->input->post('column')) {
			$measurement->populate($this->input->post());
			if ($measurement->save()) {
				Notification::set(Measurements::SUCCESS, "The measurement has been added");
				redirect("/measurements/index/" . $stationId);
			}
		}

		$this->data["stationId"] = $stationId;
		$this->data["measurement"] = $measurement;
		$this->load->view("measurements/create", $this->data);
	}

	/**
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function update($id) {
		if (!is_numeric($id)) {
			Notification::set(Measurements::WARNING, "No access allowed");
			redirect("/graph");
		}
		$measurement = (new StationMeasurement())->findById($id);

		if ($this->input->post('column')) {
			$measurement->populate($this->input->post());
			if ($measurement->save()) {
				Notification::set(Measurements::SUCCESS, "The measurement has been updated");
				redirect("/measurements/index/" . $measurement->getStationId());
			}
		}
		$this->data["stationId"] = $measurement->getStationId();
		$this->data["measurement"] = $measurement;
		$this->load->view("measurements/update", $this->data);
	}

	/**
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function delete($id) {
		if (!is_numeric($id)) {
			Notification::set(Measurements::WARNING, "No access allowed");
			redirect("/graph");
		}

	}
}
