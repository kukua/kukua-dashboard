<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MyController {

    public $validPassword     = '$2y$12$V520ITFwsANfsXiSwa9FF.hI.CrVNgqyLVtd2amuwlDPeQ/bbc5l.';

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        redirect("/auth/login", "refresh");
    }

    public function login() {
        if ($this->input->post()) {
            if ($this->_authenticate() === True) {
                redirect("/index", "refresh");
            }
        }

        $this->load->view("auth/login", $this->data);
    }

    public function logout() {
        unset($_SESSION["user"]);
        session_destroy();
        redirect("/auth/login");
    }

    /**
     * Quick auth
     */
    protected function _authenticate() {
        if (!$this->_validate()) {
            return false;
        }

        $user = $this->input->post("username");

        if (in_array($user, GlobalHelper::$validUsers)) {
            $pass = $this->input->post("password");
            if (password_verify($pass, $this->validPassword)) {
                $this->session->set_userdata("user", $user);
                return true;
            }
        }

        Notification::set(Auth::DANGER, "You have entered a wrong username or password.");
        return false;
    }

    protected function _validate() {
        $this->form_validation->set_rules("username", "Username", "required");
        $this->form_validation->set_rules("password", "Password", "required");
        return $this->form_validation->run() !== false;
    }
}
