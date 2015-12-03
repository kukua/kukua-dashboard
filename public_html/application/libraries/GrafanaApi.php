<?php

class GrafanaApi {

    public $token = "eyJrIjoiTWFST1puSGNZRmFuVmZiaVl4NVVuOHhHTjVPa0JxWXUiLCJuIjoiYXBpIiwiaWQiOjF9";
    public $response;

    public function call($call) {
        $headers = array(
            "Content-Type:application/json",
            "Authorization: Bearer " . $this->token,
            "Accept: application/json"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://dashboard.kukua.cc:9000/api" . $call);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        curl_close($ch);
        $this->response = json_decode($output);
    }

    public function result() {
        $id = Array();
        foreach($this->response->dashboard->rows as $key => $row) {
            foreach($row->panels as $panel) {
                $id[$panel->id] = $panel->title;
            }
        }
        return $id;
    }
}
