<?php defined("BASEPATH") OR exit("No direct script access allowed");

use \Curl\Curl;

class Statistics extends MyController {

	public function __construct() {
		parent::__construct();
		$this->allow("manager");
	}

	public function index() {
		$stations = (new Station())->findByUserId($this->_user->id);
		$simCards = [];
		foreach($stations as $station) {
			$eseye = new Eseye();
			$simCards[] = $eseye->getSim($station);
		}

		$dateFrom = (new \DateTime())->modify('-1 week');
		$dateTo = new \DateTime();

		$this->data["simcards"] = $simCards;
		$this->data["dates"] = ['from' => $dateFrom, 'to' => $dateTo];

		$this->load->view("statistics/index", $this->data);
	}
}
