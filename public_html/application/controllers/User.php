<?php  if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class User extends MyController {

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->allow("members");
    }

    /**
     * Display all users
     *
     * @access public
     * @return void
     */
    public function index() {
        $this->allow("admin");
        $users = $this->ion_auth->users()->result();
        foreach($users as $user) {
            //Check if user is admin, and set extra value
            $user->isAdmin = $this->ion_auth->in_group(1,$user->id);
        }

        $this->data["users"] = $users;
        $this->load->view("user/index", $this->data);
    }

    /**
     * Display current user - NYI
     *
     * @access public
     * @return void
     */
    public function read($id = null) {
        $this->allow("members", $id);
        $this->data["user"] = $this->ion_auth->user($id)->row();
        $this->load->view("user/read", $this->data);
    }

    /**
     * Create a user - NYI
     *
     * @access public
     * @return void
     */
    public function create() {
        $this->allow("admin");
        $this->load->view("user/create", $this->data);
    }

    /**
     * Updates a (or current) user
     *
     * @access public
     * @param  int $id
     * @return void
     */
    public function update($id = null) {
        $this->allow("members", $id);

        if ($this->input->post("first_name")) {
            $this->form_validation->set_rules("first_name", "First name", "required");
            $this->form_validation->set_rules("last_name", "Last name", "required");
            $postPw = $this->input->post("new") ? true : false;
            if ($postPw) {
                $this->form_validation->set_rules('new', 'Password', 'required|matches[new_confirm]');
                $this->form_validation->set_rules('new_confirm', 'Password confirmation', 'required');
            }

            if ($this->form_validation->run() !== false) {
                $userData["first_name"] = $this->input->post("first_name");
                $userData["last_name"] = $this->input->post("last_name");
                if ($postPw) {
                    $userData["password"] = $this->input->post("new");
                }

                if ($this->ion_auth->update($id, $userData) === true) {
                    Notification::set(User::SUCCESS, "Your profile has been updated");
                } else {
                    Notification::set(User::WARNING, "Your profile could not be saved");
                }
            } else {
                Notification::set(User::WARNING, validation_errors());
            }
        }

        $this->data["user"] = $this->ion_auth->user($id)->row();
        $this->load->view("user/update", $this->data);
    }

    /**
     * Invite a user to the dashboard
     *
     * @access public
     * @return void
     */
    public function invite() {
        $this->allow("admin");
        if ($this->input->post("email")) {
            $username = "";
            $password = "";
            $email    = $this->input->post("email");
            $data     = [
                "country"    => $this->input->post("country"),
                "first_name" => $this->input->post("first_name"),
                "last_name"  => $this->input->post("last_name")
            ];

            // Check if e-mail already exists
            if ($this->ion_auth->email_check($email) === true) {
                Notification::set(User::WARNING, "This e-mail address is already registered");
                redirect("user/invite", "refresh");
            }

            $user = $this->ion_auth->register($username, $password, $email, $data);
            if ($user == false) {
                Notification::set(User::DANGER, "Something went wrong. Please try again.");
                redirect("user/invite", "refresh");
            }

            if ($this->_send_user_invitation($user["id"])) {
                Notification::set(User::SUCCESS, "The user has been invited");
                redirect("user/invite", "refresh");
            }
        }

        $this->load->view("user/invite", $this->data);
    }

    /**
     * Deletes a user
     *
     * @access public
     * @param  int $id
     * @return void
     */
    public function delete($id) {
        $this->allow("admin");
        $user = $this->ion_auth->user($id)->row();

        //Check if you are trying to remove urself
        if ($id == $this->ion_auth->user()->row()->id) {
            Notification::set(User::DANGER, "You can't remove urself");
            redirect("user", "refresh");
        }

        if ($this->ion_auth->delete_user($id)) {
            Notification::set(User::SUCCESS, "The user has been removed");
            redirect("user", "refresh");
        }

        Notification::set(User::WARNING, "Something went wrong, please try agian");
        redirect("user", "refresh");
    }

    /**
     * Revoke access to a group for one user
     *
     * @access public
     * @param  $group_id
     * @param  $user_id
     * @return void
     */
    public function revoke($group_id, $user_id) {
        $this->allow("admin");
        $user = $this->ion_auth->user($user_id)->row();
        $group = $this->ion_auth->group($group_id)->row();

        //A user can't revoke his own access
        if ($user->id == $this->ion_auth->user()->row()->id) {
            Notification::set(User::WARNING, "You can't revoke ur own access");
            redirect("user", "refresh");
        }

        if ($group !== null && $user !== null) {
            if ($this->ion_auth->remove_from_group($group_id, $user_id)) {
                Notification::set(User::SUCCESS, $user->username . " removed from group " . $group->name);
            } else {
                Notification::set(User::WARNING, "Could not remove " . $user->username . " from " . $group->name);
            }
        } else {
            Notification::set(User::DANGER, "Could not remove " . $user->username . " from " . $group->name);
        }

        redirect("user", "refresh");
    }

    /**
     * Grant a user access to a group
     *
     * @access public
     * @param  int $group_id
     * @param  int $user_id
     * @return void
     */
    public function grant($group_id, $user_id) {
        $this->allow("admin");
        $user = $this->ion_auth->user($user_id)->row();
        $group = $this->ion_auth->group($group_id)->row();

        if ($group !== null && $user !== null) {
            if ($this->ion_auth->add_to_group($group_id, $user_id)) {
                Notification::set(User::SUCCESS, $user->username . " granted access to " . $group->name);
            } else {
                Notification::set(User::WARNING, "Could not grant " . $user->username . " access to " . $group->name);
            }
        } else {
            Notification::set(User::DANGER, "Could not grant " . $user->username . " access to " . $group->name);
        }

        redirect("user", "refresh");
    }

    /**
     * Send the email
     *
     * @access private
     * @param int $user_id
     * @return boolean
     */
    private function _send_user_invitation($user_id) {
        $user = $this->ion_auth->where("id", $user_id)->users()->row();
        $data["user"] = $user;
        $data["base"] = base_url();

        $content = $this->load->view("auth/email/activate", $data, true);

        $lib = new Email();
        $lib->setFrom("Ollie Smeenk <ollie@kukua.cc>");
        $lib->setTo($user->first_name . " " . $user->last_name . " <" . $user->email . ">");
        $lib->setSubject("Invitation to the new Kukua Dashboard");
        $lib->setContent($content);
        $lib->send();
        return true;
    }
}