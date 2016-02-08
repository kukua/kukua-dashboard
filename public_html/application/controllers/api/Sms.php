<?php

class Sms extends MyController {

    public $sId;
    public $token;

    private $_number;
    private $_client;

    public function __construct() {
        parent::__construct();

        $this->sId     = TWILIO_SID;
        $this->token   = TWILIO_TOKEN;

        $this->_number = TWILIO_NUMBER;
        $this->_client = new Services_Twilio($this->sId, $this->token);
    }

    public function get() {
        $data = $this->input->post();
        if (isset($data["Body"])) {
            $wap = file_get_contents("http://wap.weather.fi/peek?param1=" . $data["Body"] . "&lang=en&format=text1");
        } else {
            $wap = "Please reply only a name i.e. 'Hilversum'";
        }
        $content = $wap;
        echo '<?xml version="1.0" encoding="UTF-8" ?>
        <Response>
            <Message>' . $content . '</Message>
        </Response>';
        die();
    }
}
