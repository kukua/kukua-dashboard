<?php

class Locations extends MyController {

    public function __construct() {
        parent::__construct();
        $this->allow("admin");
    }

    /**
     *
     */
    public function index() {
        $this->data["countries"] = (new Countries())->load();
        $this->load->view("locations/index", $this->data);
    }

    /**
     *
     */
    public function get() {
        $countryId = $this->input->post("country");
		$stations = (new Stations())->findByCountryId($countryId, true);
		$result = [];
		foreach($stations as $station) {
			$result[] =$station->toArray();
		}
		echo json_encode($result);
        exit;
    }

    /**
     *
     */
    public function disable_station($id) {
        $station = (new Stations())->findById($id);
        if ($station->getId() !== null) {
            $station->setActive(0);
            if ($station->save()) {
                Notification::set(Locations::SUCCESS, "The station is de-activated");
            } else {
                Notification::set(Locations::WARNING, "Something went wrong. Please try agian");
            }
        }
        redirect("/locations/view_country/{$station->getCountryId()}");
    }

    /**
     *
     */
    public function enable_station($id) {
        $station = (new Stations())->findById($id);
        if ($station->getId() !== null) {
            $station->setActive(1);
            if ($station->save() !== false) {
                Notification::set(Locations::SUCCESS, "The station is activated");
            } else {
                Notification::set(Locations::WARNING, "Something went wrong. Please try agian");
            }
        }
        redirect("/locations/view_country/{$station->getCountryId()}");
    }

    /**
     *
     */
    public function add_country() {
        if ($this->input->post("name")) {
            $country = new Countries();
            $country->populate($this->input->post());
            if ($country->save() !== false) {
                Notification::set(Locations::SUCCESS, "The country has been added");
            }
            redirect("/locations");
        }
        $this->load->view("locations/add_country", $this->data);
    }

    /**
     *
     */
    public function delete_country($id) {
        $countryId = is_numeric($id) ? $id : false;
        $country = (new Countries())->findById($countryId);
        if ($country->getId() !== null) {
            $countryId = $country->getId();
            if ($country->delete()) {

                //users_countries where country_id = $countryId
                $userCountries = new UserCountries();
                $userCountries->deleteWhere("country_id", $countryId);

                //stations, remove where country_id = $countryId (recursive)
                $stations = new Stations();
                $stations->deleteWhere("country_id", $countryId, true);

                Notification::set(Locations::SUCCESS, "The country has been deleted");
            } else {
                Notification::set(Locations::WARNING, "The country could not be deleted. Please try again");
            }
        }
        redirect("/locations");
    }

    /**
     *
     */
    public function delete_station($id) {
        $station = (new Stations())->findById($id);
        if ($station->getId() !== null) {
            if ($station->delete($station->getId())) {
                Notification::set(Locations::SUCCESS, "The station has been deleted");
				redirect("/locations/view_country/{$station->getCountryId()}");
            }
        }
		Notification::set(Locations::WARNING, "The station could not be deleted. Please try again");
        redirect("/locations/view_country/{$station->getCountryId()}");
    }

    /**
     *
     */
    public function add_station($id) {
        $countryId = is_numeric($id) ? $id : false;
		$country = (new Countries())->findById($countryId);
        if ($country->getId() !== null) {
            $this->data["country"] = $country;
            if ($this->input->post("name")) {
                $station = new Stations();
				$station->populate($this->input->post());
				$station->setCountryId($country->getId());
                if ($station->save()) {
                    Notification::set(Locations::SUCCESS, "The station has been added to " . $country->getName());
                    redirect("/locations");
                }
            }
            $this->load->view("locations/add_station", $this->data);
        } else {
            Notification::set(Locations::DANGER, "Invalid input");
            redirect("/locations/view_country/{$country->getId()}");
        }
    }

	/**
	 * View stations for a country
	 *
	 *	@access public
	 *	@return void
	 */
	public function view_country($id) {
		$countryId = is_numeric($id) ? $id : false;
		if ($countryId === false) {
            Notification::set(Locations::DANGER, "Invalid input");
            redirect("/locations");
		}

		$country = (new Countries())->findById($countryId);
		if ($country->getId() === null) {
            Notification::set(Locations::DANGER, "Invalid input");
            redirect("/locations");
		}

		$stations = (new Stations())->findByCountryId($id, true);
		$this->data["country"] = $country;
		$this->data["stations"] = $stations;
		$this->load->view("locations/view_country", $this->data);
	}

	/**
	 * Edit station columns
	 *
	 * @access public
	 * @return void
	 */
    public function edit_station($id) {
        $station = (new Stations())->findById($id);
        $columns = (new StationColumns())->findByStationId($id);

        $this->data["weatherTypes"] = GlobalHelper::allWeathertypes();
        $this->data["station"] = $station;
        $this->data["columns"] = $columns;
        $this->load->view("locations/edit_station", $this->data);
    }

    /**
     * Add key / value to station column
     * 
     * @access public
     * @return void
     */
    public function add_station_column($id) {
        if ($this->input->post()) {
            $column = new StationColumns();
            $column->populate($this->input->post());
            $column->setStationId($id);
            $column->save();
        }
        redirect("/locations/edit_station/" . $id);
    }

    /**
     * @access public
     * @return void
     */
    public function delete_station_column($id, $stationId) {
        if ((new StationColumns())->delete($id)) {
            Notification::set(Locations::SUCCESS, "Column deleted");
        } else {
            Notification::set(Locations::DANGER, "Something went wrong");
        }
        redirect("/locations/edit_station/" . $stationId);
    }
}
