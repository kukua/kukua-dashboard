<?php defined("BASEPATH") OR exit("No direct script access allowed");

use \Curl\Curl;

class Graph extends MyController {

    const GRAPH_HISTORY  = "history";
    const GRAPH_FORECAST = "forecast";
    const GRAPH_FORECAST_T = "forecast_t";
    const GRAPH_DOWNLOAD = "download";

    public static $stations = [
        "tanzania" => [
            //"mwangoi"       => "sivad_ndogo_a5e4d2c1",
            //"mavumo"        => "sivad_ndogo_a687dcd8",
            "migambo"       => "sivad_ndogo_a468d67c",
            "mshizii"       => "sivad_ndogo_9f113b00",
            "baga"          => "sivad_ndogo_890d85ba",
            "makuyuni"      => "sivad_ndogo_1e2e607e",
            "rauya"         => "sivad_ndogo_9f696fb0",
            "mandakamnono"  => "sivad_ndogo_841d300b",
            "sanyo"         => "sivad_ndogo_7aa19521",
        ],
        "nigeria" => [
            "ibadan"        => "sivad_ndogo_fab23419",
        ]
    ];

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
     * Start requesting graph data
     *
     * @access public
     * @param  String $type
     * @param  String $graph
     * @return view
     */
    public function get($type = Graph::GRAPH_HISTORY, $graph = null) {
        $dates    = $this->_handlePostDates();
        $interval = $this->_handleInterval();
        $params = [
            "from"   => null,
            "where"  => $dates,
            "group"  => $interval,
            "order"  => " ORDER BY time ASC"
        ];

        switch($type) {
            default:
            case Graph::GRAPH_HISTORY:
                $params["select"] = $this->_handleHistorySelect($graph);
                $main = InfluxDb::getHistory($params);
                break;
            case Graph::GRAPH_FORECAST:
                $params["select"] = $this->_handleForecastSelect($graph);
                $main = InfluxDb::getForecast($params);
                break;
            case Graph::GRAPH_FORECAST_T:
                $params["where"] = "";
                $params["select"] = $this->_handleForecastSelect($graph);
                $main = InfluxDb::getForecast($params);
                break;
            case Graph::GRAPH_DOWNLOAD:
                $params["select"] = $this->_handleDownloadSelect();
                $main = InfluxDb::getDownload($params);
                $opts = [
                    "q" => $main->getQuery(),
                    "db" => $main->getDb(),
                ];
                $request = $this->_curl($main, $opts, true);
                $responses = $request->results;
                if (isset($responses[0]->series) !== false) {
                    $response = $this->_manipulate($responses[0]->series);
                    GlobalHelper::outputCsv("export-" . $dates["dateFrom"] . "-" . $dates["dateTo"] . ".csv", $response, true);
                } else {
                    echo "Sorry, no results";
                }
                exit;
                break;
        }

        $opts = [
            "q" => $main->getQuery(),
            "db" => $main->getDb(),
        ];
        $request = $this->_curl($main, $opts, true);
        $responses = $request->results;
        if (isset($responses[0]->series) !== false) {
            $response = $this->_manipulate($responses[0]->series);
            echo json_encode($response);
        } else {
            echo json_encode(Array());
        }
        exit;
    }

    /**
     * @access protected
     * @param  mixed $class
     * @return Array $result
     */
    protected function _curl($class, $opts = Array(), $headers = False) {
        $curl = new Curl();
        if ($headers !== false) {
            $curl->setHeader("Authorization", "Basic " . $class->getToken());
        }

        $result = $curl->get($class->getUrl(), $opts);
        return $result;
    }

    /**
     * Check if there are posted dates, else use default
     *
     * @access protected
     * @return Array
     */
    protected function _handlePostDates() {
        $from = DateTime::createFromFormat("Y/m/d H:i:s", GlobalHelper::getDefaultDate("P1D", true))->getTimestamp();
        $to   = DateTime::createFromFormat("Y/m/d H:i:s", GlobalHelper::getDefaultDate("P1D"))->getTimestamp();

        if ($this->input->post("from") || $this->input->post("to")) {
            $from = (int) $this->input->post("from");
            $to   = (int) $this->input->post("to");
        }
        return ["dateFrom" => $from, "dateTo" => $to];
    }

    /**
     * Check if there is a posted interval, else use default
     *
     * @access protected
     * @return Array
     */
    protected function _handleInterval() {
        $interval = "1h";
        if ($this->input->post("interval")) {
            $interval = $this->input->post("interval");
        }
        return $interval;
    }

    /**
     * Select (history)
     *
     * @access protected
     * @param  mixed $graph
     * @return Array
     */
    protected function _handleHistorySelect($graph = null) {
        $select = null;
        if ($graph !== null) {
            if ($graph == "temp") {
                $select["mean(temp)"] = "Temperature";
            }
            if ($graph == "rain") {
                $select["sum(rainTicks)"] = "Rain";
            }
            if ($graph == "hum") {
                $select["mean(hum)"] = "Humidity";
            }
            if ($graph == "presBMP") {
                $select["mean(presBMP)"] = "PresBMP";
            }
        }
        return $select;
    }

    /**
     * @access protected
     * @param  mixed $graph
     * @return null
     */
    protected function _handleForecastSelect($graph = null) {
        $select = null;
        if ($graph !== null) {
            if ($graph == "temp") {
                $select["temp"] = "Temperature";
            }
            if ($graph == "rain") {
                $select["precip"] = "Rain";
            }
            if ($graph == "hum") {
                $select["humid"] = "Humidity";
            }
            if ($graph == "presBMP") {
                $select["presBMP"] = "PresBMP";
            }

            //Ten days forecast
            if ($graph == "temp_ten") {
                $select["tempLow"] = "Low";
                $select["tempHigh"] = "High";
            }
            if ($graph == "rain_ten") {
                $select["precip"] = "Rainfall";
            }
        }
        return $select;
    }

    /**
     * Select (download)
     *
     * @access protected
     * @param  mixed $graph
     * @return Array
     */
    protected function _handleDownloadSelect() {
        $select = [
            "sum(rainTicks)" => "RainTicks",
            "mean(windTicks)" => "WindTicks",
            "mean(windGustTicks)" => "WindGustTicks",
            "mean(windDir)" => "WindDir",
            "mean(windGustDir)" => "WindGustDir",
            "mean(temp)" => "Temperature",
            "mean(hum)" => "Humidity",
            "mean(presBMP)" => "PresBMP"
        ];
        return $select;
    }

    /**
     * Manipulate forecast time display for highcharts
     *
     * @access protected
     * @return Array
     */
    protected function _manipulate($data) {
        if (count($data)) {
            foreach($data as $station => $values) {
                if (count($values->values)) {
                    foreach($values->values as $key => $points) {
                        $points[0] = str_replace("Z", "", $points[0]);
                        $points[0] = str_replace("T", " ", $points[0]);
                        $new = DateTime::createFromFormat("Y-m-d H:i:s", $points[0]);

                        //multiply by 1000 (milliseconds)
                        $data[$station]->values[$key][0] = $new->getTimestamp() * 1000;
                    }
                }
            }
        }
        return $data;
    }
}
