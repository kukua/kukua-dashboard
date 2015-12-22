<?php

class Migrate extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->config->set_item("sess_use_database", False);
        $this->load->driver("session");
        $this->input->is_cli_request() == False ? show_404() : "";
    }

    public function generate($name = "") {
        $help = "\r\n" . "Usage: $ php index.php cli migrate generate <name>" . "\r\n";
        if (empty($name)) {
            $this->output->append_output($help);
            return;
        }

        // sanitize name
        $name = preg_replace('/[^a-z0-9_]/i', '', str_replace(" ", "", trim($name)));
        $filename = date('YmdHis', time())."_".$name.".php";

        $template = "<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_".$name." extends CI_Migration {

    public function up() {

    }

    public function down() {

    }
}";
        // create migration file
        $fp = fopen(APPPATH.'migrations/'.$filename, 'w');
        fwrite($fp, $template);
        fclose($fp);

        // output
        print "\nMigration created: " .$filename.
              "\n\n\tvim ".APPPATH."migrations/".$filename."\n\n";
    }

    /**
     * Version
     *
     * @access  public
     * @param   datetime $version
     * @return  void
     */
    public function version($version = '') {
        $help = "
Usage:  \t$ php index.php cli migrate version version-timestamp
Example:\t$ php index.php cli migrate version 20131101000000\r\n";

        if (empty($version)) {
            $this->output->append_output($help);
            return;
        }
        if ($version === 'latest') {
            $this->latest();
            return;
        }
        $this->load->library("migration");
        if (!$this->migration->version($version)){
            $this->output->append_output($this->migration->error_string());
        }
    }

    /**
     * Execute latest migration
     *
     * @access  public
     * @return  void
     */
    public function latest(){
        $this->load->library("migration");
        if (!$this->migration->latest()) {
            $this->output->append_output($this->migration->error_string());
        }
    }

    public function reset() {
        $this->load->library("migration");
        if (!$this->migration->version("000000000000")) {
            $this->output->append_output($this->migration->error_string());
        }
    }

}
