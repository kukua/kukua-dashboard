<?php  if ( ! defined("BASEPATH")) exit("No direct script access allowed");

class MyController extends CI_Controller {

    const INFO      = "info";
    const WARNING   = "warning";
    const DANGER    = "danger";
    const SUCCESS   = "success";

    public $data;
    public $_user;

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Europe/Amsterdam");
        $this->setDefaultData();
        $this->setUser();
    }

    /**
     * Set user
     *
     * @access protected
     * @return void
     */
    protected function setUser() {
        $this->_user = false;
        $this->data["isAdmin"] = false;
        if ($this->ion_auth->logged_in()) {
            $this->_user = $this->ion_auth->user()->row();
            if ($this->ion_auth->in_group("admin")) {
                $this->data["isAdmin"] = true;
            }
        }
        $this->data["user"] = $this->_user;
    }

    /**
     * Set base url
     *
     * @access protected
     * @return void
     */
    protected function setDefaultData() {
        $this->data["baseUrl"] = base_url();
    }

    /**
     * Check if user has access
     *
     * @access protected
     * @param  String   $group
     * @param  Int      $user
     * @return void
     */
    protected function allow($group = null, $user_id = null) {
        if (!$this->ion_auth->logged_in()) {
            Notification::set(self::WARNING, "No access allowed");
            redirect("auth/login", "refresh");
        }

        //Only allow people with the given role, for their own pages (user/update/ etc)
        if ($group !== null && $user_id !== null) {
            if (!$this->_groupCheck($group)) {
                $this->_disallow();
            }
            if (!$this->_userCheck($user_id)) {
                $this->_disallow();
            }
        }

        //Allow everyone within the group
        if ($group !== null) {
            if ($this->_groupCheck($group) === false) {
                $this->_disallow();
            }
        }
    }

    private function _groupCheck($group) {
        return $this->ion_auth->in_group($group);
    }

    private function _userCheck($user_id) {

        //Allow admins everywhere
        if ($this->ion_auth->in_group("admin")) {
            return true;
        }

        return $this->ion_auth->user()->row()->id == $user_id;
    }

    private function _disallow() {
        Notification::set(self::WARNING, "You cannot access this part");
        redirect("/");
        return false;
    }
}
