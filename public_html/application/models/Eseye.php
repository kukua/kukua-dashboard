<?php defined("BASEPATH") OR exit("No direct script access allowed");

use Curl\Curl;

class Eseye extends CI_Model {

    public $type;
    public $portfolioId;
    public $username;
    public $password;

    /**
     *
     */
    public function __construct() {
        $this->portfolioId = ESEYE_PORTFOLIOID;
        $this->username = ESEYE_USERNAME;
        $this->password = ESEYE_PASSWORD;

        $this->url = "https://tigrillostaging.eseye.com/Japi/Tigrollo";
        if (ENVIRONMENT === "production") {
            $this->url = "https://siam.eseye.com/Japi/Tigrillo";
        }
    }

    /**
     *
     */
    public function getSims() {
        $curl = new Curl();
        try {
            $curl->post($this->url . "/ping");
            GlobalHelper::debug($curl->response);
        } catch (Exception $e) {
            return Array();
        }
    }
}
