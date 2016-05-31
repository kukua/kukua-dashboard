<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Smsclients extends MyController {

    /**
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->allow("admin");
    }

    /**
     * Displays all sms clients
     *
     * @access public
     * @return void
     */
    public function index() {
        $client = new Smsclient();
        $this->data["clients"] = $client->load();
        $this->load->view("smsclients/index", $this->data);
    }

    /**
     * Create a new sms client
     *
     * @access public
     * @return void
     */
    public function create() {
        if ($this->input->post()) {
            $client = new Smsclient();
            $client->populate($this->input->post());
            if ($client->save() !== false) {
				Notification::set(Smsclients::SUCCESS, "Succesfully added the client");
				redirect("/smsclients");
            } else {
                Notification::set(Smsclients::DANGER, "Something went wrong");
            }
        }
        $this->load->view("smsclients/create", $this->data);
    }

	/**
	 * Update existing client
	 *
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function update($id) {
		$client = new Smsclient();
		$client->findById($id);
		if ($this->input->post()) {
			$client->populate($this->input->post());
            if ($client->save() !== false) {
                Notification::set(Smsclients::SUCCESS, "Succesfully updated the client");
				redirect("/smsclients");
            } else {
                Notification::set(Smsclients::DANGER, "Something went wrong");
            }
		}

		$this->data["client"] = $client;
		$this->load->view("smsclients/update", $this->data);
	}

    /**
     * Delete a sms client
     *
     * @access public
     * @param  int $id
     * @return void
     */
    public function delete($id) {
        $client = new Smsclient();
        if ($client->delete($id)) {
            Notification::set(Smsclients::SUCCESS, "Succesfully deleted the client");
        } else {
            Notification::set(Smsclients::DANGER, "Something went wrong");
        }
        redirect(base_url() . "smsclients/index");
    }
}
