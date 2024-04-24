<?php

class Session
{
  /**
   * The id of the logged in user, as it appears in the users table.
   */
  private $user_id;

  /**
   * The display name of the logged in user.
   */
  public $display_name;

  /**
   * The Unix Timestamp of when this session was last logged in.
   */
  private $last_login;

  /**
   * A character based on the logged in user's role: s=Super Admin, a=Admin, m=User.
   */
  private $role;


  /**
   * The id of the vendor associated with the logged in user, as it appears in the vendors table.
   */
  public $active_vendor_id;

  /**
   * The vendor display name of the vendor associated with the logged in user.
   */
  public $active_vendor_name;

  /**
   * A bit corresponding to a bool on whether the vendor associated with the logged in user has a pending application.
   */
  public $is_pending;


  public const MAX_LOGIN_AGE = 60 * 60 * 24; // 1 day

  public function __construct() {
    session_start();
    $this->check_stored_login();
  }

  /**
   * Sets the session data based on a given user logging in, changing the view of the website.
   * 
   * @param User $user the user object to be logged in.
   */
  public function login($user) {
    if ($user) {

      session_regenerate_id();
      $_SESSION['user_id'] = $user->user_id;
      $this->user_id = $user->user_id;

      $this->display_name = $_SESSION['display_name'] = $user->display_name;
      $this->last_login = $_SESSION['last_login'] = time();
      $this->role = $_SESSION['role'] = $user->role;

      $active_vendor = Vendor::find_by_user_id($user->user_id);
      if ($active_vendor) {
        $this->active_vendor_id = $_SESSION['active_vendor_id'] = $active_vendor->vendor_id;
        $this->active_vendor_name = $_SESSION['active_vendor_name'] = $active_vendor->vendor_display_name;
        $this->is_pending = $_SESSION['is_pending'] = $active_vendor->is_pending;
      } else {
        $this->active_vendor_id = $_SESSION['active_vendor_id'] = null;
        $this->active_vendor_name = $_SESSION['active_vendor_name'] = null;
        $this->is_pending = $_SESSION['is_pending'] = null;
      }
    }
    return true;
  }

  /**
   * Tests if the current session is a logged in user who logged in recently.
   * 
   * @return bool whether the session is a logged in user
   */
  public function is_logged_in() {
    return isset($this->user_id) && $this->last_login_is_recent();
  }

  /**
   * Tests if the current session is a logged in admin or super admin.
   * 
   * @return bool whether the session is a logged in admin
   */
  public function is_admin_logged_in() {
    return $this->is_logged_in() && ($this->role == 'a' || $this->role == 's');
  }

  /**
   * Tests if the current session is a logged in super admin.
   * 
   * @return bool whether the session is a logged in super admin
   */
  public function is_super_admin_logged_in() {
    return $this->is_logged_in() && ($this->role == 's');
  }

  /**
   * Tests if the current session has an associated vendor.
   * 
   * @return bool whether the session has an associated vendor
   */
  public function has_vendor() {
    return isset($this->active_vendor_id);
  }

  /**
   * Clears the session data, changing the view of the website.
   */
  public function logout() {
    unset($_SESSION['user_id']);
    unset($_SESSION['display_name']);
    unset($_SESSION['last_login']);
    unset($_SESSION['role']);
    unset($_SESSION['active_vendor_id']);
    unset($_SESSION['active_vendor_name']);
    unset($_SESSION['is_pending']);
    unset($this->user_id);
    return true;
  }

  /**
   * Stores all of the session data to this session object.
   */
  private function check_stored_login() {
    if (isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->display_name = $_SESSION['display_name'];
      $this->last_login = $_SESSION['last_login'];
      $this->role = $_SESSION['role'];

      $this->active_vendor_id = $_SESSION['active_vendor_id'];
      $this->active_vendor_name = $_SESSION['active_vendor_name'];
      $this->is_pending = $_SESSION['is_pending'];
    }
  }

  /**
   * Checks if the session was logged in recently.
   * 
   * @return bool whether this session was logged in recently
   */
  private function last_login_is_recent() {
    if (!isset($this->last_login)) {
      return false;
    } else if (($this->last_login + self::MAX_LOGIN_AGE) < time()) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Gets the user_id associated with this session.
   * 
   * @return int this session's user id
   */
  public function get_user_id() {
    return $this->user_id;
  }

  /**
   * Clears the session data pertaining to this user having an associated vendor or vendor application.
   */
  public function no_application() {
    $this->active_vendor_id = $_SESSION['active_vendor_id'] = null;
    $this->active_vendor_name = $_SESSION['active_vendor_name'] = null;
    $this->is_pending = $_SESSION['is_pending'] = null;
  }

  /**
   * Sets and gets messages to and from the session data.
   * 
   * @param string $msg the message to be stored
   * 
   * @return string the message that was stored
   */
  public function message($msg = "") {
    if (!empty($msg)) {
      // This is a set message
      $_SESSION['message'] = $msg;
      return true;
    } else {
      // This is a get message
      return $_SESSION['message'] ?? '';
    }
  }

  /**
   * Clears the current message from the session data.
   */
  public function clear_message() {
    unset($_SESSION['message']);
  }
}
