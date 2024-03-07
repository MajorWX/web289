<?php

class Session {
  private $user_id;
  public $display_name;
  private $last_login;
  private $role;
  public $active_vendor_id;
  public $active_vendor_name;
  public $is_pending;


  public const MAX_LOGIN_AGE = 60*60*24; // 1 day

  public function __construct() {
    session_start();
    $this->check_stored_login();
  }
  public function login($user) {
    if($user){

      session_regenerate_id();
      $_SESSION['user_id'] = $user->user_id;
      $this->user_id = $user->user_id;

      $this->display_name = $_SESSION['display_name'] = $user->display_name;
      $this->last_login = $_SESSION['last_login'] = time();
      $this->role = $_SESSION['role'] = $user->role;

      $active_vendor = Vendor::find_by_user_id($user->user_id)[0];
      $this->active_vendor_id = $_SESSION['active_vendor_id'] = $active_vendor->vendor_id;
      $this->active_vendor_name = $_SESSION['active_vendor_name'] = $active_vendor->vendor_display_name;
      $this->is_pending = $_SESSION['is_pending'] = $active_vendor->is_pending;
    }
    return true;
  }

  public function is_logged_in() {
    return isset($this->user_id) && $this->last_login_is_recent();
  }

  public function is_admin_logged_in() {
    return $this->is_logged_in() && ($this->role == 'a' || $this->role == 's');
  }

  public function has_vendor() {
    return isset($this->active_vendor_id);
  }

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

  private function check_stored_login() {
    if(isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->display_name = $_SESSION['display_name'];
      $this->last_login = $_SESSION['last_login'];
      $this->role = $_SESSION['role'];

      $this->active_vendor_id = $_SESSION['active_vendor_id'];
      $this->active_vendor_name = $_SESSION['active_vendor_name'];
      $this->is_pending = $_SESSION['is_pending'];
    }
  }

  private function last_login_is_recent() {
    if(!isset($this->last_login)) {
      return false;
    } else if(($this->last_login + self::MAX_LOGIN_AGE) < time()) {
      return false;
    } else {
      return true;
    }
  } 

  public function message($msg="") {
    if(!empty($msg)) {
      // This is a set message
      $_SESSION['message'] = $msg;
      return true;
    } else {
      // This is a get message
      return $_SESSION['message'] ?? '';
    }
  }

  public function clear_message() {
    unset($_SESSION['message']);
  }
}


?>