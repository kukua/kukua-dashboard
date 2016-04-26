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
			$user->isManager = $this->ion_auth->in_group(3, $user->id);
		}

		$this->data["users"] = $users;
		$this->load->view("user/index", $this->data);
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

		$isAdmin = $this->ion_auth->in_group("admin");
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

				if ($isAdmin) {

					/* User station management */
					$postUserStations = $this->input->post('userStations');
					$dbUserStations = new UserStations();
					$dbUserStations->saveBatch($id, $postUserStations);

					/* User group management */
					$postGroups = $this->input->post('user_groups');
					$dbUserGroups = new UserGroup();
					$dbUserGroups->saveBatch($id, $postGroups);
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

		$this->_userStations($id);
		$this->_groups($id);

		$currentUser = $this->ion_auth->user($id)->row();
		$this->data["member"] = $currentUser;

		$this->load->view("user/update", $this->data);
	}

	/**
	 * Disable access for user in dashboard
	 *
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function disable($id) {
		$this->allow("admin");
		$user = $this->ion_auth->user($id)->row();

		if ($id == $this->ion_auth->user()->row()->id) {
			Notification::set(User::DANGER, "You can't lock yourself out!");
			redirect("user", "refresh");
		}

		if ($this->ion_auth->deactivate($id)) {
			Notification::set(User::SUCCESS, "The user has been deactivated");
			redirect("user", "refresh");
		}

		Notification::set(User::WARNING, "Something went wrong, please try agian");
		redirect("user", "refresh");
	}

	/**
	 * Enable access for user in dashboard
	 *
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function enable($id) {
		$this->allow("admin");
		$user = $this->ion_auth->user($id)->row();

		if ($id == $this->ion_auth->user()->row()->id) {
			Notification::set(User::DANGER, "You can't enable yourself");
			redirect("user", "refresh");
		}

		if ($this->ion_auth->activate($id)) {
			Notification::set(User::SUCCESS, "The user has been activated");
			redirect("user", "refresh");
		}

		Notification::set(User::WARNING, "Something went wrong, please try agian");
		redirect("user", "refresh");
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
			$email	  = $this->input->post("email");
			$data	  = [
				"first_name" => $this->input->post("first_name"),
				"last_name"  => $this->input->post("last_name")
			];

			if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				Notification::set(User::WARNING, "This is not a valid e-mail address");
				redirect("user/invite", "refresh");
			}

			// Check if e-mail already exists
			if ($this->ion_auth->email_check($email) === true) {
				Notification::set(User::WARNING, "This e-mail address is already registered");
				redirect("user/invite", "refresh");
			}

			$data["activation_code"] = bin2hex(openssl_random_pseudo_bytes(8));
			$user = $this->ion_auth->register($username, $password, $email, $data);
			if ($user == false) {
				Notification::set(User::DANGER, "Something went wrong. Please try again.");
				redirect("user/invite", "refresh");
			}

			$postUserStations = $this->input->post('userStations');
			$dbUserStations = new UserStations();
			$dbUserStations->saveBatch($user, $postUserStations);

			if ($this->_send_user_invitation($user)) {
				Notification::set(User::SUCCESS, "The user has been invited");
				redirect("user/invite", "refresh");
			}
		}

		$this->_userStations();
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
	 * Resend invitation e-mail
	 *
	 * @access public
	 * @param  int $id
	 * @return void
	 */
	public function resendInvite($id) {
		if ($this->_send_user_invitation($id)) {
			Notification::set(User::SUCCESS, "The e-mail has been send");
		}
		redirect("/user");
	}

	/**
	 * Build view data for user stations
	 *
	 * @access private
	 * @return void
	 */
	private function _userStations($userId = null) {
		$userStations = [];
		if (!is_null($userId)) {
			$userStations = (new UserStations())->findStationsByUserId($userId);
		}

		if ($this->ion_auth->in_group("admin") === true) {
			$stations = (new Station())->load();
			$regions  = (new Region())->load();

			$regionStations = [];
			foreach ($stations as $station) {
				foreach ($regions as $region) {
					if ($station->getRegionId() == $region->getId()) {
						$regionStations[$region->getId()]["region"] = $region;
						$regionStations[$region->getId()]["stations"][] = $station;
					}
				}
			}

			$this->data["regions"] = $regionStations;
		}
		$this->data["userStations"] = $userStations;
	}

	/**
	 * Build view data for user groups
	 *
	 * @access private
	 * @param  int | null $userId
	 * @return void
	 */
	private function _groups($userId = null) {
		$this->data["groups"]		= null;
		$this->data["usergroups"]	= null;

		/* If the current logged in user is admin */
		if ($this->ion_auth->in_group('admin')) {
			$this->data["groups"]		= $this->ion_auth->groups()->result();
			$this->data["usergroups"]	= $this->ion_auth->get_users_groups($userId)->result();
		}
	}

	/**
	 * Send the email
	 *
	 * @access private
	 * @param int $user_id
	 * @return boolean
	 */
	private function _send_user_invitation($userId) {
		$user = $this->ion_auth->users()->user($userId)->row();
		$data["user"] = $user;
		$data["base"] = base_url();

		$content = $this->load->view("auth/email/activate", $data, true);

		$lib = new Email();
		$lib->setFrom("Kukua B.V. <info@kukua.cc>");
		$lib->setTo($user->first_name . " " . $user->last_name . " <" . $user->email . ">");
		$lib->setSubject("Welcome to the Kukua Dashboard");
		$lib->setContent($content);
		$lib->send();
		return true;
	}
}
