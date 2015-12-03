<?php  if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class MyController extends CI_Controller {

    const INFO      = "info";
    const WARNING   = "warning";
    const DANGER    = "danger";
    const SUCCESS   = "success";

    public $data;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Europe/Amsterdam");
        $this->setDefaultData();
    }

    protected function setDefaultData() {
        $this->data["baseUrl"] = "127.0.0.1";
    }
}
