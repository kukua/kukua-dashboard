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
	public function index($id) {
		$countryId = is_numeric($id) ? $id : false;
		if ($countryId === false) {
			Notification::set(Stations::DANGER, "Invalid input");
			redirect("/countries");
		}

		$country = (new Country())->findById($countryId);
		if ($country->getId() === null) {
			Notification::set(Stations::DANGER, "Invalid input");
			redirect("/countries");
		}

		$stations = (new Station())->findByCountryId($id, true);
		$this->data["country"] = $country;
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
	public function create($id) {
		$countryId = is_numeric($id) ? $id : false;
		$country = (new Country())->findById($countryId);
		if ($country->getId() !== null) {
			$this->data["country"] = $country;
			if ($this->input->post("name")) {
				$station = new Station();
				$station->populate($this->input->post());
				$station->setCountryId($country->getId());
				if ($station->save()) {
					Notification::set(Stations::SUCCESS, "The station has been added to " . $country->getName());
					redirect("/stations/" . $country->getId());
				}
			}
			$this->load->view("stations/create", $this->data);
		} else {
			Notification::set(Stations::DANGER, "Invalid input");
			redirect("/stations/" . $country->getId());
		}
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
		$availableColumns = (new CountryColumn())->findByCountryId($station->getCountryId());
		$columns = (new StationColumn())->findByStationId($id);

		$this->data["station"] = $station;
		$this->data["availableColumns"] = $availableColumns;
		$this->data["columns"] = $columns;
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
			if ($station->delete($station->getId())) {
				Notification::set(Stations::SUCCESS, "The station has been deleted");
				redirect("/stations/" . $station->getCountryId());
			}
		}
		Notification::set(Stations::WARNING, "The station could not be deleted. Please try again");
		redirect("/stations/index/" . $station->getCountryId());
	}

	/**
	 * Copies parameters from posted station
	 *
	 * @access public
	 * @param  station id (copying to..)
	 * @return void
	 */
	public function copyParams($copyTo, $countryId) {
		$copyTo   = is_numeric($copyTo) ? $copyTo : false;
		$copyFrom = is_numeric($this->input->post("copyFrom")) ? $this->input->post("copyFrom") : false;

		if ($copyFrom !== false && $copyTo !== false) {

			//Delete current params
			$column = (new StationColumn())->deleteByStationId($copyTo);

			//get params from copyFrom
			$columns = (new StationColumn())->findByStationId($copyFrom);
			foreach($columns as $column) {
				$column->unsetId();
				$column->setStationId($copyTo);
				$column->save();
			}
			Notification::set(Stations::SUCCESS, "The stations have been copied!");
			redirect("/stations/index/" . $countryId);
		}
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
		redirect("/stations/" . $station->getCountryId());
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
		redirect("/stations/" . $station->getCountryId());
	}

	/**
	 * Add key / value to station column
	 * 
	 * @access public
	 * @param  int $id (station id)
	 * @return void
	 */
	public function add_station_column($id) {
		if ($this->input->post()) {
			$column = new StationColumn();
			$column->populate($this->input->post());
			$column->setStationId($id);
			$column->save();
		}
		redirect("/stations/update/" . $id);
	}

	/**
	 * @access public
	 * @param  int $id (country id)
	 * @param  int $stationId (station id)
	 * @return void
	 */
	public function delete_station_column($id, $stationId) {
		if ((new StationColumn())->delete($id)) {
			Notification::set(Stations::SUCCESS, "Column deleted");
		} else {
			Notification::set(Stations::DANGER, "Something went wrong");
		}
		redirect("/stations/update/" . $stationId);
	}
}
