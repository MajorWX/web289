<?php

class User extends DatabaseObject {

  static protected $table_name = "users";
  static protected $db_columns = ['user_id', 'display_name', 'hashed_password', 'email', 'role'];

  /**
   * This user's id, as it appears in the users table.
   */
  public $user_id;

  /**
   * This user's display name.
   */
  public $display_name;

  /**
   * This user's email.
   */
  public $email;

  /**
   * A character describing this user's role: s=Super Admin, a=Admin, m=User.
   */
  public $role;

  /**
   * A hashed version of this user's password.
   */
  protected $hashed_password;

  /**
   * This user's password as provided by the password field in a form.
   */
  public $password;

  /**
   * This user's password as provided by the confirm password field in a form, used to match with the password field to prevent mistyping.
   */
  public $confirm_password;

  /**
   * A bool that prevents a user from being stored to the database if it does not have a valid un-hashed password.
   */
  protected $password_required = true;

  public function __construct($args=[]){
    $this->display_name = $args['display_name'] ?? '';
    $this->email = $args['email'] ?? '';
    $this->role = $args['role'] ?? '';
    $this->password = $args['password'] ?? '';
    $this->confirm_password = $args['confirm_password'] ?? '';
  }

  /**
   * Takes the un-hashed password attribute of this user object, hashes it, and stores the hashed password.
   */
  public function set_hashed_password() {
    $this->hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
  }

  /**
   * Checks if a given password matches this user object's hashed password.
   * 
   * @param string $password the password to check
   * 
   * @return bool if the given password matches the hashed password
   */
  public function verify_password($password) {
    return password_verify($password, $this->hashed_password);
  }

  /**
   * Determines if this user already exists in the users table and then stores it in a new or existing row. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function save() {
    // A new record will not have an ID yet
    if(isset($this->user_id)) {
      return $this->update();
    } else {
      return $this->create();
    }
  }

  /**
   * Creates a new row in the users table based on this user. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  protected function create() {
    $this->set_hashed_password();

    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $sql = "INSERT INTO " . static::$table_name . " (";
    $sql .= join(', ', array_keys($attributes));
    $sql .= ") VALUES ('";
    $sql .= join("', '", array_values($attributes));
    $sql .= "')";
    $result = self::$database->query($sql);
    if($result) {
      $this->user_id = self::$database->insert_id;
    }
    return $result;
  }

  /**
   * Modifies an existing row in the users table based on this user. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  protected function update() {
    if($this->password != '') {
      $this->set_hashed_password();
      // validate password
    } else {
      $this->password_required = false;
    }
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $attribute_pairs = [];
    foreach($attributes as $key => $value) {
      $attribute_pairs[] = "{$key}='{$value}'";
    }

    $sql = "UPDATE " . static::$table_name . " SET ";
    $sql .= join(', ', $attribute_pairs);
    $sql .= " WHERE user_id='" . self::$database->escape_string($this->user_id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }

  /**
   * Removes a row from the users table based on this user's user_id. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function delete() {
    $sql = "DELETE FROM " . static::$table_name . " ";
    $sql .= "WHERE user_id='" . self::$database->escape_string($this->user_id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;

    // After deleting, the instance of the object will still
    // exist, even though the database record does not.
    // This can be useful, as in:
    //   echo $user->first_name . " was deleted.";
    // but, for example, we can't call $user->update() after
    // calling $user->delete().
  }

  /**
   * Makes sure that this user has a valid email, a valid and unique display name, a valid role, and a valid password if required.
   * 
   * @return array all errors that were discovered during validation
   */
  protected function validate() {
    $this->errors = [];
  
    if(is_blank($this->email)) {
      $this->errors[] = "Email cannot be blank.";
    } elseif (!has_length($this->email, array('max' => 255))) {
      $this->errors[] = "Email must be less than 255 characters.";
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

  /**
   * Queries the user table and finds the user object with a given display name. 1 Query
   * 
   * @param string $display_name the display name to search for
   * 
   * @return User|bool the user found by the query, if it exists
   */
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
  
  /**
   * Get this user's role and prints it as full words rather than just a character.
   * 
   * @return string the role as full
   */
  public function role_to_string() {
    switch ($this->role) {
      case 's':
        return 'Super Admin';
        break;
      case 'a':
        return 'Admin';
        break;
      case 'm':
        return 'User';
        break;
      default:
        return 'ERROR: INVALID ROLE CHARACTER';
    }
  }
} // End User class

?>
