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
        echo json_encode($stations);
        exit;
    }

    /**
     *
     */
    public function disable($id) {
        $station = (new Stations())->findById($id);
        if ($station->id !== null) {
            $station->active = 0;
            if ($station->save()) {
                Notification::set(Locations::SUCCESS, "The station is de-activated");
            } else {
                Notification::set(Locations::WARNING, "Something went wrong. Please try agian");
            }
        }
        redirect("/locations");
    }

    /**
     *
     */
    public function enable($id) {
        $station = (new Stations())->findById($id);
        if ($station->id !== null) {
            $station->active = 1;
            if ($station->save() !== false) {
                Notification::set(Locations::SUCCESS, "The station is activated");
            } else {
                Notification::set(Locations::WARNING, "Something went wrong. Please try agian");
            }
        }
        redirect("/locations");
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
    public function delete_country() {
        $countryId = is_numeric($this->input->post("country_id")) ? $this->input->post("country_id") : false;
        $country = (new Countries())->findById($countryId);
        if ($country->id !== null) {
            $countryId = $country->id;
            if ($country->delete()) {

                //users_countries
                $userCountries = new UserCountries();
                $userCountries->deleteWhere("country_id", $countryId);

                //stations, remove where country_id = id
                $stations = new Stations();
                $stations->deleteWhere("country_id", $countryId);

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
        if ($station->id !== null) {
            if ($station->delete($station->id)) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     */
    public function add_station() {
        $countryId = is_numeric($this->input->post("country_id")) ? $this->input->post("country_id") : false;
        $country = (new Countries())->findById($countryId);
        if ($country->id !== null) {
            $this->data["country"] = $country;
            if ($this->input->post("name")) {
                $station = new Stations();
                $station->populate($this->input->post());
                if ($station->save()) {
                    Notification::set(Locations::SUCCESS, "The station has been added to " . $country->name);
                    redirect("/locations");
                }
            }
            $this->load->view("locations/add_station", $this->data);
        } else {
            Notification::set(Locations::DANGER, "Invalid input");
            redirect("/locations");
        }
    }
}
