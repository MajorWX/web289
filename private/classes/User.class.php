<?php

class User extends DatabaseObject {

  static protected $table_name = "users";
  static protected $db_columns = ['user_id', 'displayName', 'hashed_password', 'email', 'role'];

  public $user_id;
  public $displayName;
  public $email;
  public $role;
  protected $hashed_password;
  public $password;
  public $confirm_password;
  protected $password_required = true;

  public function set_hashed_password() {
    $this->hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
  }

  public function verify_password($password) {
    return password_verify($password, $this->hashed_password);
  }

  protected function create() {
    $this->set_hashed_password();
    return parent::create();
  }

  protected function update() {
    if($this->password != '') {
      $this->set_hashed_password();
      // validate password
    } else {
      $this->password_required = false;
    }
    return parent::update();
  }

  // protected function validate() {}

  // static public function find_by_displayName($displayName){}
}

?>