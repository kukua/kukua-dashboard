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
        parent::__construct();

        $this->portfolioId = ESEYE_PORTFOLIOID;
        $this->username = ESEYE_USERNAME;
        $this->password = ESEYE_PASSWORD;

        $this->url = "https://siam.eseye.com/Japi/Tigrillo";
        if (ENVIRONMENT === "development") {
            $this->url = "https://tigrillostaging.eseye.com/Japi/Tigrillo";
        }
    }

    /**
     *
     */
    public function getSims() {
        $curl = new Curl();
        $curl->setHeader("Content-type", "application/json");
        $curl->post($this->url . "/getCookieName");

        $cookieName = $curl->response;
        $cookieValue = $this->_login_eseye();

        try {
            $curl->setCookie($cookieName, $cookieValue);
            $curl->post($this->url . "/getSIMs", [
                'sortOrder' => "I",
                'startRec' => 0,
                'numRecs' => 0,
            ]);
            $simcards = isset($curl->response->sims) ? $curl->response->sims : Array();

            $result = Array();
            foreach($simcards as $sim) {
                $result[] = $this->getSim($sim, $cookieName, $cookieValue);
            }

            usort($result, function($a, $b) {
                if ($a->LastRadiusStop == $b->LastRadiusStop) {
                    return 0;
                }
                return ($a->LastRadiusStop < $b->LastRadiusStop) ? -1 : 1;
            });

            return $result;

        } catch (Exception $e) {
            throw $e;
            exit;
        }
    }

    public function getSim($sim, $cookieName, $cookieValue) {
        $curl = new Curl();
        $curl->setHeader("Content-type", "application/json");
        try {
            $curl->setCookie($cookieName, $cookieValue);
            $curl->post($this->url . "/getSIMLastActivity", [
                "ICCID" => $sim->ICCID
            ]);
            $response = $curl->response;
            $sim->LastRadiusStop  = $response->info->LastRadiusStop;
            $sim->LastRadiusBytes = $response->info->LastRadiusBytes;

            $difference = 96;
            if (!empty($sim->LastRadiusStop)) {
                $date = DateTime::createFromFormat("Y-m-d H:i:s", $sim->LastRadiusStop);
                $difference = ($date->getTimestamp() / 3600);
            }

            $status = "red"; 
            if ($difference <= 1)
                $status = "green";

            if ($difference > 1 && $difference < 24)
                $status = "blue";

            if ($difference >= 24 && $difference < 48)
                $status = "yellow";

            if ($difference >= 48 && $difference < 96)
                $status = "orange";

            $sim->Status = $status;
            return $sim;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     */
    public function _login_eseye() {
        if (!$this->session->userdata("eseye/login")) {
            $curl = new Curl;
            $curl->setHeader("Content-type", "application/json");
            try {
                $curl->post($this->url. "/login", [
                    'username' => $this->username,
                    'password' => $this->password,
                    'portfolioId' => $this->portfolioId
                ]);

                $cookie = isset($curl->response->cookie) ? $curl->response->cookie : false;
                #set login in session
                $this->session->set_userdata("eseye/login", $cookie);
            } catch (Exception $e) {
                throw $e;
            }
        }
        return $this->session->userdata("eseye/login");
    }
}
