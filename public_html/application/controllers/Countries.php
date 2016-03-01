<?php defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * @package Controllers
 * @since	29-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 * @copyright 2016 Kukua B.V.
 */
class Countries extends MyController {

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
	 * Display list of countries
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$this->allow("admin");
        $this->data["countries"] = (new Country())->load();
        $this->load->view("countries/index", $this->data);
	}

	/**
	 * Create a new country
	 *
	 * @access public
	 * @return void
	 */
	public function create() {
		$this->allow("admin");
        if ($this->input->post("name")) {
            $country = new Country();
            $country->populate($this->input->post());
            if ($country->save() !== false) {
                Notification::set(Countries::SUCCESS, "The country has been added");
            }
            redirect("/countries");
        }
        $this->load->view("countries/create", $this->data);
	}

	/**
	 * Display country details
	 *
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function read($id) {
		$this->allow("admin");
	}

	/**
	 * Update country details
	 * 
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function update($id) {
		$this->allow("admin");
	}

	/**
	 * Delete a country, and the connected stations
	 *
	 * @access public
	 * @param  int
	 * @return void
	 */
	public function delete($id) {
		$this->allow("admin");
        $countryId = is_numeric($id) ? $id : false;
        $country = (new Country())->findById($countryId);
        if ($country->getId() !== null) {
            $countryId = $country->getId();
            if ($country->delete()) {

                //users_countries where country_id = $countryId
                $userCountries = new UserCountry();
                $userCountries->deleteWhere("country_id", $countryId);

                //stations, remove where country_id = $countryId (recursive)
                $station = new Station();
                $station->deleteWhere("country_id", $countryId, true);

                Notification::set(Countries::SUCCESS, "The country and connected stations has been deleted");
            } else {
                Notification::set(Countries::WARNING, "The country could not be deleted. Please try again");
            }
        }
        redirect("/countries");
	}

	/**
	 * Display columns
	 *
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function display($id) {
		$this->allow("admin");
		$this->data["country"] = (new Country())->findById($id);
		$this->data["columns"] = (new CountryColumn())->findByCountryId($id);
		$this->load->view("countries/display", $this->data);
	}

	/**
	 * Add a column available for the country
	 *
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function add_column($id) {
		$this->allow("admin");
		if ($this->input->post()) {
			$country = (new Country())->findById($id);
			$visible = (bool) $this->input->post("visible");
			if ($country->getId() !== null) {
				$data = $this->input->post();
				$data["visible"] = (int) $visible; 
				$data["country_id"] = $id;
				$countryColumn = new CountryColumn();
				$countryColumn->populate($data);
				if ($countryColumn->save()) {
					Notification::set(Countries::SUCCESS, "The column has been added");
				} else {
					Notification::set(Countries::WARNING, "Something went wrong, please try agian");
				}
			}
		}
		redirect("/countries/display/" . $id);
	}

	/**
	 * Toggle column visibility
	 *
	 * @access public
	 * @param  int country column id
	 * @param  int 1/0
	 * @return void
	 */
	public function toggle_column($id, $value) {
		$this->allow("admin");
		$column = (new CountryColumn())->findById($id);
		if ($column->getId() !== null) {
			$column->setVisible($value);
			$column->save();
		}
		redirect("/countries/display/" . $column->getCountryId());
	}

	/**
	 * get available country columns
	 *
	 * @access public
	 * @param  int $countryId
	 * @return void
	 */
	public function getcolumns($countryId) {

		/* Find visible country columns */
		$availableColumns = (new CountryColumn())->findByCountryId($countryId, true);
		foreach($availableColumns as $key => $column) {
			$result[$key]["id"] = $column->getId();
			$result[$key]["name"] = $column->getName();
		}

		echo json_encode($result);
		exit;
	}
}
