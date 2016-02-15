<?php

class Cronjobs extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            throw new Exception("Only accessable through CLI");
        }
    }

    /**
     * Cronjob that sends SMS Text messages with forecast info
     *
     * @access public
     * @return void
     */
    public function smsForecast() {
        $sId     = TWILIO_SID;
        $token   = TWILIO_TOKEN;
        $twilio  = new Services_Twilio($sId, $token);

        $smsClients = new SmsClient();
        $clients = $smsClients->load();
        $number = "+447400200078";

        foreach($clients as $client) {
            $content = file_get_contents("http://wap.weather.fi/peek?param1=" . $client->getLocation() . "&lang=en&format=text1");
            $twilio->account->messages->sendMessage(
                $number,
                $client->getNumber(),
                $content
            );
        }
    }
}
