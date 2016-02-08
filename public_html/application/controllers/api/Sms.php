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
        echo '<?xml version="1.0" encoding="UTF-8" ?>
        <Response>
            <Message>' . print_r($this->input->post()) . '</Message>
        </Response>
        ';
        exit;
    }
}
