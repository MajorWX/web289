<?php

class User extends DatabaseObject {

  static protected $table_name = "users";
  static protected $db_columns = ['user_id', 'display_name', 'hashed_password', 'email', 'role'];

  public $user_id;
  public $display_name;
  public $email;
  public $role;
  protected $hashed_password;
  public $password;
  public $confirm_password;
  protected $password_required = true;

  public function __construct($args=[]){
    $this->display_name = $args['display_name'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->role = $args['role'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->confirm_password = $args['confirm_password'] ?? '';
  }


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

  protected function validate() {
    $this->errors = [];
  
    if(is_blank($this->email)) {
      $this->errors[] = "Email cannot be blank.";
    } elseif (!has_length($this->email, array('max' => 255))) {
      $this->errors[] = "Last name must be less than 255 characters.";
    } elseif (!has_valid_email_format($this->email)) {
      $this->errors[] = "Email must be a valid format.";
    }
  
    if(is_blank($this->display_name)) {
      $this->errors[] = "Display name cannot be blank.";
    } elseif (!has_length($this->display_name, array('min' => 8, 'max' => 30))) {
      $this->errors[] = "Display name must be between 8 and 30 characters.";
    } elseif (!has_unique_display_name($this->display_name, $this->user_id ?? 0)) {
      $this->errors[] = "Display name must be unique, try another.";
    }
  
    if($this->password_required) {
      if(is_blank($this->password)) {
        $this->errors[] = "Password cannot be blank.";
      } elseif (!has_length($this->password, array('min' => 8))) {
        $this->errors[] = "Password must contain 8 or more characters";
      } elseif (!preg_match('/[A-Z]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 uppercase letter";
      } elseif (!preg_match('/[a-z]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 lowercase letter";
      } elseif (!preg_match('/[0-9]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 number";
      } elseif (!preg_match('/[^A-Za-z0-9\s]/', $this->password)) {
        $this->errors[] = "Password must contain at least 1 symbol";
      }
    
      if(is_blank($this->confirm_password)) {
        $this->errors[] = "Confirm password cannot be blank.";
      } elseif ($this->password !== $this->confirm_password) {
        $this->errors[] = "Password and confirm password must match.";
      }

      


    }
    
    if($this->role !== "a" && $this->role !== "m" && $this->role !== "s"){
      $this->errors[] = "Invalid user role.";
    }
  
    return $this->errors;
  }

  static public function find_by_display_name($display_name){
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE display_name='" . self::$database->escape_string($display_name) . "'";
    $obj_array = static::find_by_sql($sql);
    if(!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }
}

?>