<?php defined("BASEPATH") OR exit("No direct script access allowed");

use \Curl\Curl;

class Sim extends MyController {

    public function __construct() {
        parent::__construct();
        $this->allow("admin");
    }

	public function index() {

		$stations = (new Station())->load();
		$simCards = [];
		foreach($stations as $station) {
			if ($station->getSimId()) {
				$eseye = new Eseye();
				$simCards[] = $eseye->getSim($station);
			}
		}

        $this->data["simcards"] = $simCards;
        $this->load->view("sim/index", $this->data);
    }
}
