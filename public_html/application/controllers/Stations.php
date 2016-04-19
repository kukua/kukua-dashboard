<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Controllers
 * @since	29-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 * @copyright 2016 Kukua B.V.
 */
class Stations extends MyController {

	/**
	 * Class constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->allow("admin");
	}

	/**
	 * List stations per country
	 *
	 * @access public
	 * @param  int $id (country id)
	 * @return void
	 */
	public function index() {
		$stations = (new Station())->load();
		$this->data["stations"] = $stations;
		$this->load->view("stations/index", $this->data);
	}

	/**
	 * Create a station within a country
	 *
	 * @access public
	 * @param  int $id (country id)
	 * @return void
	 */
	public function create() {
		$station = new Station();
		if ($this->input->post("device_id")) {
			$station->populate($this->input->post());
			if ($station->save()) {
				Notification::set(Stations::SUCCESS, "The station has been added");
				redirect("/stations/");
			}
		}
		$this->data["regions"] = (new Region())->load();
		$this->data["station"] = $station;
		$this->load->view("stations/create", $this->data);
	}

	/**
	 * Update station
	 *
	 * @access public
	 * @param  int $id (station id)
	 * @return void
	 */
	public function update($id) {
		$station = (new Station())->findById($id);
		if ($this->input->post("device_id")) {
			$station->populate($this->input->post());
			if ($station->save()) {
				Notification::set(Stations::SUCCESS, "The station has been updated");
				redirect("/stations/");
			}
		}
		$this->data["regions"] = (new Region())->load();
		$this->data["station"] = $station;
		$this->load->view("stations/update", $this->data);
	}

	/**
	 * Delete station
	 *
	 * @access public
	 * @param  int $id (station id)
	 * @return void
	 */
	public function delete($id) {
		$station = (new Station())->findById($id);
		if ($station->getId() !== null) {
			$stationId = $station->getId();
			if ($station->delete($station->getId())) {

				/* Also delete combination of users to the station */
				(new UserStations())->deleteByStationId($stationId);

				Notification::set(Stations::SUCCESS, "The station has been deleted");
				redirect("/stations/");
			}
		}
		Notification::set(Stations::WARNING, "The station could not be deleted. Please try again");
		redirect("/stations/index");
	}

	/**
	 * Disable a station
	 *
	 * @access public
	 * @param  int $id (station id)
	 * @return void
	 */
	public function disable($id) {
		$station = (new Station())->findById($id);
		if ($station->getId() !== null) {
			$station->setActive(0);
			if ($station->save()) {
				Notification::set(Stations::SUCCESS, "The station is de-activated");
			} else {
				Notification::set(Stations::WARNING, "Something went wrong. Please try agian");
			}
		}
		redirect("/stations/");
	}

	/**
	 * Enable a station
	 *
	 * @access public
	 * @param  int $id (station id)
	 * @return void
	 */
	public function enable($id) {
		$station = (new Station())->findById($id);
		if ($station->getId() !== null) {
			$station->setActive(1);
			if ($station->save() !== false) {
				Notification::set(Stations::SUCCESS, "The station is activated");
			} else {
				Notification::set(Stations::WARNING, "Something went wrong. Please try agian");
			}
		}
		redirect("/stations/");
	}
}
