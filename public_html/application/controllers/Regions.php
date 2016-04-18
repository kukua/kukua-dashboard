<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package Controllers
 * @since	22-02-2016
 * @version 1.0
 * @author	Siebren Kranenburg <siebren@kukua.cc>
 */
class Regions extends MyController {

	public function __construct() {
		parent::__construct();
		$this->allow('admin');
	}

	public function index() {
		$regions = (new Region())->load();
		$this->data["regions"] = $regions;
		$this->load->view("regions/index.tpl", $this->data);
	}

	public function create() {
		$region = new Region();
		if ($this->input->post("name")) {
			$region->populate($this->input->post());
			if ($region->save()) {
				Notification::set(Regions::SUCCESS, "The region has been added");
				redirect("/regions/");
			}
		}
		$this->data["region"] = $region;
		$this->load->view("regions/create.tpl", $this->data);
	}

	public function update($id) {
		$region = (new Region())->findById($id);
		if ($this->input->post('name')) {
			$region->populate($this->input->post());
			if ($region->save()) {
				Notification::set(Regions::SUCCESS, "The region has been added");
				redirect("/regions/");
			}
		}
		$this->data["region"] = $region;
		$this->load->view("regions/update.tpl", $this->data);
	}

	public function delete($id) {

	}
}
