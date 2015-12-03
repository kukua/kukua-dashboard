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
        $api = new GrafanaApi();
        $api->call("/dashboards/db/kukuatz");

        //If the /graph/download returned no results
        //display the given values
        if ($this->session->postLocation !== Null) {
            $items = [
                "postLocation" => $this->session->postLocation,
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

        $user = GlobalHelper::getUser();
        $this->data["locations"]   = Graph::$stations[$user];
        $this->data["panelGraphs"] = $api->result();
        $this->data["graphUrl"]    = $this->_graphUrl();
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

        $nation = ($this->input->post("nation") != "") ? $this->input->post("nation") : null;

        $from   = $this->input->post("from") . " 00:00:00";
        $to     = $this->input->post("to") . " 23:59:59";
        $dateFrom = DateTime::createFromFormat("Y/m/d H:i:s", $from)->getTimestamp();
        $dateTo   = DateTime::createFromFormat("Y/m/d H:i:s", $to)->getTimestamp();

        try {
            $influx = new InfluxDbApi();
            $influx->buildQuery($nation, $dateFrom, $dateTo);
            $influx->call();
            $values = $influx->getOutput();
            GlobalHelper::outputCsv("export-" . $from . "-" . $to . ".csv", $values);
        } catch (Exception $e) {
            Notification::set(Graph::INFO, "The selected from-to dates gave zero results.");
            $sessionData = [
                "postLocation" => $this->input->post("nation"),
                "postDateFrom" => $this->input->post("from"),
                "postDateTo"   => $this->input->post("to"),
            ];
            $this->session->set_userdata($sessionData);
            redirect("/graph/index#refresh", "refresh");
        }
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
