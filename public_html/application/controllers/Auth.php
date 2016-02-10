<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Mailgun\Mailgun;

class Auth extends MyController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Index action, redirects to login
     */
    public function index() {
        redirect("auth/login", "refresh");
    }

    /**
     * @access public
     * @return void
     */
    public function login() {
        if ($this->input->post()) {
            if ($this->_authenticate() === True) {
                redirect("index", "refresh");
            }
        }

        $this->load->view("auth/login", $this->data);
    }

    /**
     * Log out
     *
     * @access public
     * @return void
     */
    public function logout() {
        $this->ion_auth->logout();
        redirect("auth/login");
    }

    /**
     * @access public
     * @return void
     */
    public function activate($code = null) {
        $this->ion_auth->logout();
        if ($code === null) {
            Notification::set(Auth::DANGER, "It appears that you can't activate");
            redirect("auth/login", "refresh");
        }

        $user = $this->ion_auth->where("activation_code", $code)->users()->row();
        if (!$user) {
            //Invalid code
            Notification::set(Auth::DANGER, "This invitation is no longer active");
            redirect("auth/login", 'refresh');
        }

        //validate & update user with postdata
        if ($this->input->post()) {
            $this->form_validation->set_rules("first_name", "First name", "required");
            $this->form_validation->set_rules("last_name", "Last name", "required");
            $this->form_validation->set_rules('password', 'Password', 'required|matches[password_confirm]');
            $this->form_validation->set_rules('password_confirm', 'Repeat password', 'required');
            if ($this->form_validation->run() !== false) {
                $userData = [
                    "first_name"        => $this->input->post("first_name"),
                    "last_name"         => $this->input->post("last_name"),
                    "password"          => $this->input->post("password"),
                    "activation_code"   => "",
                    "active"            => 1
                ];

                if ($this->ion_auth->update($user->id, $userData) === true) {
                    Notification::set(Auth::SUCCESS, "Thank you for registering with us. You can now login");
                    redirect("auth/login", "refresh");
                }
                Notification::set(Auth::ERROR, "Updating the user failed, please consult Kukua.");
            } else {
                Notification::set(Auth::WARNING, validation_errors());
            }
        }

        $this->data["user"] = $user;
        $this->load->view("auth/activate", $this->data);
    }

    /**
     * @access public
     * @return void
     */
    public function forgot_password() {
        $this->form_validation->set_rules('identity', 'E-mail address', 'required|valid_email');
        if ($this->form_validation->run() !== false) {
            $identity = $this->ion_auth->where("email", $this->input->post('identity'))->users()->row();
            if (empty($identity)) {
                Notification::set(Auth::DANGER, "Something went terribly wrong");
                redirect("auth/login");
            }

            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});
            if (!$forgotten) {
                Notification::set(Auth::DANGER, $this->ion_auth->errors());
                redirect("auth/forgot_password", "refresh");
            }

            $this->_send_reset_password_email($identity, $forgotten["forgotten_password_code"]);
            Notification::set(Auth::SUCCESS, "Your have received a e-mail with further instructions");
            redirect("auth/login", "refresh");
        }
        $this->load->view("auth/forgot_password", $this->data);
    }

    /**
     * @access public
     * @param  mixed $code
     * @return void
     */
    public function reset_password($code = null) {
        if ($code === null) {
            redirect("auth/login");
        }

        $user = $this->ion_auth->forgotten_password_check($code);
        if (!$user) {
            //Invalid code
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("auth/forgot_password", 'refresh');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('new', 'New password', 'required|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', 'Repeat new password', 'required');
            if ($this->form_validation->run() !== true) {
                Notification::set(Auth::DANGER, "The passwords did not match");
                redirect("auth/reset_password/" . $code, "refresh");
            }

            if ($this->_valid_csrf_nonce() === false || $user->id != $this->input->post('user_id')) {
                //Get out of here.
                Notification::set(Auth::DANGER, "csrf, is false.");
                $this->ion_auth->clear_forgotten_password_code($code);
                redirect("auth/login", "refresh");
            }

            $identity = $user->{$this->config->item("identity", "ion_auth")};
            $change   = $this->ion_auth->reset_password($identity, $this->input->post("new"));
            if (!$change) {
                Notification::set(Auth::DANGER, "error, you made.");
                redirect("auth/reset_password/" . $code, "refresh");
            }

            //Everything worked out perfectly
            Notification::set(Auth::SUCCESS, "Your password has been resetted. You can now use that to login.");
            redirect("auth/login", 'refresh');
        }
        $this->data['user_id'] = $user->id;
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['code'] = $code;
        $this->load->view("auth/reset_password", $this->data);
    }

    /**
     * Authenticate
     *
     * @access protected
     * @return boolean
     */
    protected function _authenticate() {
        if (!$this->_validate()) {
            return false;
        }

        $remember = false;
        $user = $this->input->post("identity");
        $pass = $this->input->post("password");

        if ($this->ion_auth->login($user, $pass, $remember)) {
            return true;
        }

        //Check if the error returned contained a not_active string
        if (stristr($this->ion_auth->errors(), "not_active") == false) {
            Notification::set(Auth::DANGER, "You have entered a wrong username or password.");
        } else {
            Notification::set(Auth::DANGER, "Your account has been deactivated. Contact ollie@kukua.cc");
        }
            
        return false;
    }

    /**
     * Validate
     *
     * @access protected
     * @return boolean
     */
    protected function _validate() {
        $this->form_validation->set_rules("identity", "E-mail address", "required");
        $this->form_validation->set_rules("password", "Password", "required");
        return $this->form_validation->run() !== false;
    }

    /**
     * @access protected
     * @return Array
     */
    protected function _get_csrf_nonce() {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);
        return [
            "name" => $key,
            "value" => $value
        ];
    }

    /**
     * @access protected
     * @return boolean
     */
    protected function _valid_csrf_nonce() {
        if ($this->input->post($this->session->flashdata('csrfkey')) !== false &&
            $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sends email to user
     *
     * @access protected
     * @param  $user
     * @return void
     */
    protected function _send_reset_password_email($user, $code) {
        $data["base"] = base_url();
        $data["user"] = $user;
        $data["code"] = $code;

        $content = $this->load->view("auth/email/forgot_password", $data, true);
        $lib = new Email();
        $lib->setFrom("Kukua B.V. <info@kukua.cc>");
        $lib->setTo($user->first_name . " " . $user->last_name . " <" . $user->email . ">");
        $lib->setSubject("Password reset");
        $lib->setContent($content);
        $lib->send();
        return true;
    }
}
