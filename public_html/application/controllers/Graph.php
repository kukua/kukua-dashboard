<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Graph extends MyController {

    public static $stations = [
        "kukuatz" => [
            'mwangoi'       => 'sivad_ndogo_a5e4d2c1',
            'mavumo'        => 'sivad_ndogo_a687dcd8',
            'migambo'       => 'sivad_ndogo_a468d67c',
            'mshizii'       => 'sivad_ndogo_9f113b00',
            'baga'          => 'sivad_ndogo_890d85ba',
            'makuyuni'      => 'sivad_ndogo_1e2e607e',
            'rauya'         => 'sivad_ndogo_9f696fb0',
            'mandakamnono'  => 'sivad_ndogo_841d300b',
            'sanyo'         => 'sivad_ndogo_7aa19521',
        ],
        "kukuang" => [
            'ibadan'        => 'sivad_ndogo_fab23419',
        ]
    ];

    public function __construct() {
        parent::__construct();
        GlobalHelper::requireLogin();
    }

    /**
     * graph/index
     *
     * @access public
     * @return view
     */
    public function index() {
        if ($this->session->postDateFrom !== Null) {
            $items = [
                "postDateFrom" => $this->session->postDateFrom,
                "postDateTo"   => $this->session->postDateTo,
            ];

            //Assign variables to the view
            foreach($items as $key => $value) {
                $this->data[$key] = $value;
            }

            //Remove session
            $this->session->unset_userdata(array_keys($items));
        }

        $this->data["panelGraphs"] = [];
        $this->load->view("graph/index", $this->data);
    }

    /**
     * @access public
     * @return view
     */
    public function download() {
        if (!$this->input->post()) {
            redirect("/graph/index");
        }

        $from   = $this->input->post("from") . " 00:00:00";
        $to     = $this->input->post("to") . " 23:59:59";
        $dateFrom = DateTime::createFromFormat("Y/m/d H:i:s", $from)->getTimestamp();
        $dateTo   = DateTime::createFromFormat("Y/m/d H:i:s", $to)->getTimestamp();

        try {
            $influx = new InfluxDbApi();
            $influx->buildQuery(null, null, $dateFrom, $dateTo, $this->input->post("submit"));
            $influx->call();
            $values = $influx->getOutput();
            GlobalHelper::outputCsv("export-" . $from . "-" . $to . ".csv", json_decode($values, true));
        } catch (Exception $e) {
            Notification::set(Graph::INFO, "The selected from-to dates gave zero results.");
            $sessionData = [
                "postDateFrom" => $this->input->post("from"),
                "postDateTo"   => $this->input->post("to"),
            ];
            $this->session->set_userdata($sessionData);
            redirect("/graph/index", "refresh");
        }
    }

    //Test function for new graph
    public function build($graph = null, $interval= "5m") {
        $type = null;
        $nation = null;
        $submit = $interval;

        $from = GlobalHelper::getDefaultDate("P8D");
        $to   = GlobalHelper::getDefaultDate("P1D");

        if ($this->input->post("from") || $this->input->post("to")) {
            $from = $this->input->post("from");
            $to   = $this->input->post("to");
        }
        $fromDate = DateTime::createFromFormat("Y/m/d", $from);
        $toDate   = DateTime::createFromFormat("Y/m/d", $to);

        //Temporary
        if ($graph !== null) {
            if ($graph == "temp") {
                $type["temp"] = "Temperature";
            }
            if ($graph == "rain") {
                $type["rainTicks"] = "Rain";
            }
        }

        $influx = new InfluxDbApi();
        $influx->buildQuery($type, $nation, $fromDate->getTimestamp(), $toDate->getTimestamp(), $submit);
        $influx->call();
        $values = $influx->getOutput();
        echo $values;
        exit;
    }

    protected function _graphUrl() {
        $server = "http://dashboard.kukua.cc";
        $from = GlobalHelper::getDefaultDate("P8D");
        $from = str_replace("/","",$from);
        $to   = GlobalHelper::getDefaultDate("P1D");
        $to   = str_replace("/","",$to);
        return $server . ":9000/dashboard-solo/db/" . GlobalHelper::getUser() . "?panelId=4&fullscreen&from=" . $from . "&to=" . $to . "&theme=light";
    }
}
