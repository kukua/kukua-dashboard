<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Graph extends MyController {

    public static $stations = [
        "tanzania" => [
            'mwangoi'       => 'sivad_ndogo_a5e4d2c1',
            //'mavumo'        => 'sivad_ndogo_a687dcd8',
            'migambo'       => 'sivad_ndogo_a468d67c',
            'mshizii'       => 'sivad_ndogo_9f113b00',
            'baga'          => 'sivad_ndogo_890d85ba',
            'makuyuni'      => 'sivad_ndogo_1e2e607e',
            'rauya'         => 'sivad_ndogo_9f696fb0',
            'mandakamnono'  => 'sivad_ndogo_841d300b',
            'sanyo'         => 'sivad_ndogo_7aa19521',
        ],
        "nigeria" => [
            'ibadan'        => 'sivad_ndogo_fab23419',
        ]
    ];

    public function __construct() {
        parent::__construct();
        $this->allow('members');
    }

    /**
     * graph/index
     *
     * @access public
     * @return view
     */
    public function index() {
        if ($this->session->postDateFrom !== null) {
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

        $dateFrom   = (int) $this->input->post("from");
        $dateTo     = (int) $this->input->post("to");

        try {
            $influx = new InfluxDbApi();
            $influx->buildQuery(null, null, $dateFrom, $dateTo, $this->input->post("interval"));
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

        //default week
        $fromDate = DateTime::createFromFormat("Y/m/d", GlobalHelper::getDefaultDate("P1D"))->getTimestamp();
        $toDate   = DateTime::createFromFormat("Y/m/d", GlobalHelper::getDefaultDate("P1D"))->getTimestamp();

        //posted week
        if ($this->input->post("from") || $this->input->post("to")) {
            $fromDate = (int) $this->input->post("from");
            $toDate   = (int) $this->input->post("to");
        }

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
        $influx->buildQuery($type, $nation, $fromDate, $toDate, $submit);
        $influx->call();
        $values = $influx->getOutput();
        echo $values;
        exit;
    }
}
