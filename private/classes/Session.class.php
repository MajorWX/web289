<?php

class Session {
  private $user_id;
  public $display_name;
  private $last_login;
  private $role;


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
      $this->role = $_SESSION['$role'] = $user->role;
    }
    return true;
  }

  public function is_logged_in() {
    return isset($this->user_id) && $this->last_login_is_recent();
  }

  public function is_admin_logged_in() {
    return $this->is_logged_in() && ($this->role == 'a' || $this->role == 's');
  }


  public function logout() {
    unset($_SESSION['user_id']);
    unset($_SESSION['display_name']);
    unset($_SESSION['last_login']);
    unset($this->user_id);
    return true;
  }

  private function check_stored_login() {
    if(isset($_SESSION['user_id'])) {
      $this->user_id = $_SESSION['user_id'];
      $this->display_name = $_SESSION['display_name'];
      $this->last_login = $_SESSION['last_login'];
      $this->role = $_SESSION['$role'];
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