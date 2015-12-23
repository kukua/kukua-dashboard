<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends MyController {

    public function __construct() {
        parent::__construct();
        $this->allow("members");
    }

    /**
     * @access public
     * @return void
     */
    public function index() {
        $this->allow("admin");
        $feedback = new Feedback_model();
        $this->data["feedback"] = $feedback->load();
        $this->load->view("feedback/index", $this->data);
    }

    /**
     * @access public
     * @return JSON
     */
    public function create() {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(["success" => false]);
            exit;
        }

        if ($this->input->post("feedback")) {
            if (!$this->_validate()) {
                echo json_encode(["success" => false, "message" => validation_errors()]);
                exit;
            }

            $feedback = new Feedback_model();
            $feedback->populate($this->input->post());
            $feedback->user_id = $this->_user->id;
            if ($feedback->save() !== false) {
                echo json_encode(["success" => true]);
                exit;
            }
        }
    }

    /**
     * @access protected
     * @return boolean
     */
    public function complete($id) {
        $this->allow("admin");
        $feedback = new Feedback_model();
        $obj = $feedback->findById($id);
        if ($obj) {
            $obj->completed = 1;
            $feedback->populate((array) $obj);
            if ($feedback->save() !== false) {
                redirect("/feedback");
            }
        }

        Notification::set(Feedback::DANGER, "NO!");
        redirect("/feedback");
    }

    /**
     * @access protected
     * @return boolean
     */
    public function uncomplete($id) {
        $this->allow("admin");
        $feedback = new Feedback_model();
        $obj = $feedback->findById($id);
        if ($obj) {
            $obj->completed = 0;
            $feedback->populate((array) $obj);
            if ($feedback->save() !== false) {
                redirect("/feedback");
            }
        }

        Notification::set(Feedback::DANGER, "NO!");
        redirect("/feedback");
    }

    /**
     * @access protected
     * @return boolean
     */
    public function delete($id) {
        $this->allow("admin");
        $feedback = new Feedback_model();
        $obj = $feedback->findById($id);
        if ($obj) {
            if ($feedback->delete($id)) {
                redirect("/feedback");
            }
        }
        Notification::set(Feedback::DANGER, "NEIN NEIN NEIN.");
        redirect("/feedback");
    }

    /**
     * @access protected
     * @return boolean
     */
    protected function _validate() {
        $this->form_validation->set_rules("feedback", "Feedback", "required");
        $this->form_validation->set_rules("email", "E-mail", "valid_email");
        return $this->form_validation->run() !== false;
    }
}
