<?php  if ( ! defined("BASEPATH")) exit("No direct script access allowed");

use Mailgun\Mailgun;

class Email {

    private $key;
    private $domain;

    protected $_to;
    protected $_from;
    protected $_subject;
    protected $_content;

    public function __construct() {
        $this->key      = MAILGUN_KEY;      //defined in .environment
        $this->domain   = MAILGUN_DOMAIN;   //defined in .environment
    }

    /**
     * Set receiver
     *
     * @access public
     * @param  string $to
     * @return void
     */
    public function setTo($to) {
        $this->_to = $to;
    }

    /**
     * Get receiver
     *
     * @access public
     * @return string $this->_to
     */
    public function getTo() {
        return $this->_to;
    }

    /**
     * Set sender
     *
     * @access public
     * @param  string $from
     * @return void
     */
    public function setFrom($from) {
        $this->_from = $from;
    }

    /**
     * Get sender
     *
     * @access public
     * @return string $this->_from
     */
    public function getFrom() {
        return $this->_from;
    }

    /**
     * Set subject
     *
     * @access public
     * @param  string $subject
     * @return void
     */
    public function setSubject($subject) {
        $this->_subject = $subject;
    }

    /**
     * Get subject
     *
     * @access public
     * @return string $this->_subject
     */
    public function getSubject() {
        return $this->_subject;
    }

    /**
     * Set content
     *
     * @access public
     * @param  string $content
     * @return void
     */
    public function setContent($content) {
        $this->_content = $content;
    }

    /**
     * Get content
     *
     * @access public
     * @return string $this->_content
     */
    public function getContent() {
        return $this->_content;
    }

    /**
     * @access public
     * @param  Array $data
     * @return Email
     */
    public function populate($data) {
        if (!is_array($data) || empty($data)) {
            throw new Exception("Data is empty");
        }

        if (isset($data["to"])) {
            $this->_to = $data["to"];
        }
        if (isset($data["from"])) {
            $this->_from = $data["from"];
        }
        if (isset($data["subject"])) {
            $this->_subject = $data["subject"];
        }
        if (isset($data["content"])) {
            $this->_content = $data["content"];
        }

        return $this;
    }

    /**
     * Send the email via MailGun
     *
     * @access public
     * @return void
     */
    public function send() {
        $client = new Mailgun($this->key);
        $data = [
            "from"    => $this->_from,
            "to"      => $this->_to,
            "subject" => $this->_subject,
            "html"    => $this->_content
        ];
        if (ENVIRONMENT === "production") {
            $data["bcc"] = "1870363@bcc.hubspot.com";
        }
        $result = $client->sendMessage($this->domain, $data);
    }
}
