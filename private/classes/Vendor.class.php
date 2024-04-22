<?php

class Vendor extends DatabaseObject {

  static protected $table_name = 'vendors';
  static protected $db_columns = ['vendor_id', 'vd_user_id', 'vendor_display_name', 'vendor_desc', 'contact_info', 'address', 'city', 'vd_state_id', 'is_pending'];

  /**
   * This vendor object's unique vendor_id as it appears in the vendors table.
   */
  public $vendor_id;
  /**
   * The user_id associated with this vendor as it appears in the users table.
   */
  public $vd_user_id;
  /**
   * The vendor's company name.
   */
  public $vendor_display_name;
  /**
   * The description paragraph set by the vendor.
   */
  public $vendor_desc;
  /**
   * DEPRECIATED| The paragraph formatting for the vendor's contact info
   */
  public $contact_info;
  /**
   * The vendor's stated street address.
   */
  public $address;
  /**
   * The vendor's stated city address.
   */
  public $city;
  /**
   * The state_id of the state in this vendor's address as it appears in the states table.
   */
  public $vd_state_id;
  /**
   * A bool showing if this vendor is pending admin review and approval.
   */
  public $is_pending;

  /**
   * The string version of the vendor's state.
   */
  public $state;
  /**
   * UNUSED
   */
  public $user;

  /**
   * An associative array of all this vendor's phone numbers, see the populate_phones() function.
   * A list of phones with [phone_id]['phone_number'] and [phone_id]['phone_type']
   * Phone types are 'home', 'mobile', and 'work'
   */
  public $phone_numbers = [];
  
  /**
   * An associative array of all vendor phones added in the last edit.
   * A list of phones with [phone_id]['phone_number'] and [phone_id]['phone_type']
   * Phone types are 'home', 'mobile', and 'work'
   */
  public $new_phone_numbers = [];

  /**
   * A list of this vendor's VendorInventory objects, product listings associated with this vendor.
   */
  public $vendor_inventory = [];
  /**
   * A list of this vendor's CalendarDate objects, dates this vendor has marked as attending.
   */
  public $listed_dates = [];

  public function __construct($args=[]){
    $this->vendor_display_name = $args['vendor_display_name'] ?? '';
    $this->vendor_desc = $args['vendor_desc'] ?? '';
    $this->address = $args['address'] ?? '';
    $this->city = $args['city'] ?? '';
    $this->vd_state_id = $args['vd_state_id'] ?? '';

    $phone_number_array = $args['phone_numbers'] ?? [];
    if(count($phone_number_array) > 0) {
      foreach($phone_number_array as $phone_id => $phone_attributes) {
        $this->phone_numbers[$phone_id]['phone_number'] = preg_replace('~\D~', "" , $phone_attributes['phone_number']);
        echo "Set a phone number: " . $this->phone_numbers[$phone_id]['phone_number'];

        $this->phone_numbers[$phone_id]['phone_type'] = $phone_attributes['phone_type'];
        echo "Set a phone type: " . $this->phone_numbers[$phone_id]['phone_type'];
      }
    }

    $new_phone_number_array = $args['new_phone_numbers'] ?? [];
    if(count($new_phone_number_array) > 0) {
      foreach($phone_number_array as $phone_id => $phone_attributes) {
        $this->new_phone_numbers[$phone_id]['phone_number'] = preg_replace('~\D~', "" , $phone_attributes['phone_number']);
        $this->new_phone_numbers[$phone_id]['phone_type'] = $phone_attributes['phone_type'];
      }
    }
  }

  // SQL FUNCTIONS ====================================================

  /**
   * Queries the database and finds all vendors that are not listed as pending. 1 Query
   * 
   * @return Vendor[]|false the vendors found by the search, if they exist
   */
  static public function list_all() {
    $sql = "SELECT vendor_id, vendor_display_name ";
    $sql .= "FROM " . static::$table_name . " ";
    $sql .= "WHERE is_pending = FALSE;";

    $result = static::find_by_sql($sql);
    if($result){
      return $result;
    } else {
      return false;
    }
  }

  /**
   * Queries the database and finds all vendors that are listed as pending. 1 Query
   * 
   * @return Vendor[]|false the vendors found by the search, if they exist
   */
  static public function find_all_pending() {
    $sql = "SELECT vendor_id, vendor_display_name ";
    $sql .= "FROM " . static::$table_name . " ";
    $sql .= "WHERE is_pending = TRUE;";

    $result = static::find_by_sql($sql);
    if($result){
      return $result;
    } else {
      return false;
    }
  }

  /**
   * Queries the database and finds the vendor with the given vendor_id. 1 Query
   * 
   * @param int $vendor_id the vendor_id to search the database for
   * 
   * @return Vendor|false the vendor found by the search, if it exists
   */
  static public function find_by_id($vendor_id) {
    // $sql = "SELECT vendor_id, vendor_display_name ";
    $sql = "SELECT * ";
    $sql .= "FROM " . static::$table_name . " ";
    $sql .= "WHERE vendor_id = " . $vendor_id . ";";

    $result = static::find_by_sql($sql);
    if($result){
      return $result[0];
    } else {
      return false;
    }
    
  }

  /**
   * Queries the database and finds the vendor associated with a given user_id. 1 Query
   * 
   * @param int $user_id the user_id to search the database for
   * 
   * @return Vendor|false the vendor found by the search, if it exists
   */
  static public function find_by_user_id($user_id) {
    $sql = "SELECT vendor_id, vendor_display_name, is_pending ";
    $sql .= "FROM " . static::$table_name . " ";
    $sql .= "WHERE vd_user_id = " . $user_id . ";";
    
    $result = static::find_by_sql($sql);

    if($result){
      return $result[0];
    } else {
      return false;
    }
  }

  /**
   * Gets all the US states from the states table and returns them in an associative array. 1 Query
   * 
   * @return string[] an associative array of states, Keys: state_ids. Values: state_names
   */
  static public function get_state_array(){
    $sql = "SELECT * ";
    $sql .= "FROM states;";

    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed.");
    }

    // Storing Results
    $state_array = [];
    // Reading each row
    while($row = $result->fetch_assoc()) {

    $state_id = '';
    $state_name = '';

      // Reading each cell
      foreach($row as $property => $value) {
        if($property === 'state_id') {
          $state_id = $value;
        } elseif($property === 'state_name') {
          $state_name = $value;
        }
      } // End foreach for cells
      $state_array[$state_id] = $state_name;
    } // End while for rows
    return $state_array;
  }

  /**
   * Populates this vendor's state attribute with a state_name. 1 Query
   */
  public function populate_state(){
    $sql = "SELECT state_name ";
    $sql .= "FROM states ";
    $sql .= "WHERE state_id = " . $this->vd_state_id . ";";

    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed.");
    }

    $this->state = $result->fetch_row()[0];

    $result->free();
  }

  /**
   * Populates this vendor's phone_numbers attribute as an associative array [phone_id]['phone_number'] and [phone_id]['phone_type']. 1 Query
   */
  public function populate_phones() {
    $sql = "SELECT phone_id, phone_number, phone_type ";
    $sql .= "FROM phone_numbers ";
    $sql .= "WHERE ph_vendor_id =" . $this->vendor_id . ";";

    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed.");
    }

    // Storing Results

    // Reading each row
    while($row = $result->fetch_assoc()) {

      $phone_id = '';
      $phone_number = '';
      $phone_type = '';

      // Reading each cell
      foreach($row as $property => $value) {
        if($property === 'phone_id') {
          $phone_id = $value;
        } elseif($property === 'phone_number') {
          $phone_number = $value;
        }elseif($property === 'phone_type') {
          $phone_type = $value;
        }
      } // End foreach for cells

      $this->phone_numbers[$phone_id]['phone_number'] = $phone_number;
      $this->phone_numbers[$phone_id]['phone_type'] = $phone_type;
    } // End while for rows
  } // End populate_phones()

  /**
   * Queries the database to find all CalendarDate listings this vendor is attending to populate this Vendor object's listed_dates attribute. 1 Query
   */
  public function populate_dates(){
    $this->listed_dates = CalendarDate::find_by_vendor($this->vendor_id);
  }

  /**
   * Populates this vendor's vendor_inventory attribute by querying the database for all VendorInventory listings associated with this vendor. N+1 Queries
   */
  public function populate_inventory(){
    $this->vendor_inventory = VendorInventory::find_by_vendor($this->vendor_id);
  }

  /**
   * Creates a Vendor object by querying the database for a given vendor_id and populates as many of its fields as possible. N+4 Queries
   */
  static public function populate_full($vendor_id) {
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE vendor_id = " . $vendor_id . ";";

    $vendor_object = static::find_by_sql($sql)[0];

    $vendor_object->populate_state();
    // $vendor_object->populate_user();
    $vendor_object->populate_phones();
    $vendor_object->populate_inventory();
    $vendor_object->populate_dates();

    return $vendor_object;
  }

  /**
   * Checks if this vendor is attending a given date. 1 Query
   * 
   * @param CalendarDate $given_date the date to check attendance for
   * 
   * @return bool whether this vendor is attending
   */
  public function is_coming_on_date($given_date){

    if(empty($this->listed_dates)){
      $this->populate_dates();
    }

    $listed_date_ids = [];
    foreach($this->listed_dates as $date){
      $listed_date_ids[] = $date->calendar_id;
    }

    return in_array($given_date->calendar_id, $listed_date_ids);
  }

  /**
   * Saves this vendor object's information to the vendors table. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function save() {
    // A new record will not have an ID yet
    if(isset($this->vendor_id)) {
      return $this->update();
    } else {
      return $this->create();
    }
  }

  /**
   * Creates a new vendor in the the vendors table. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  protected function create() {
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $sql = "INSERT INTO " . static::$table_name . " (";
    $sql .= join(', ', array_keys($attributes));
    $sql .= ") VALUES ('";
    $sql .= join("', '", array_values($attributes));
    $sql .= "');";
    $result = self::$database->query($sql);
    if($result) {
      $this->vendor_id = self::$database->insert_id;
    }
    return $result;
  }

  /**
   * Updates an existing vendor in the vendors table. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  protected function update() {
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $this->update_phones();

    $attributes = $this->sanitized_attributes();
    $attribute_pairs = [];
    foreach($attributes as $key => $value) {
      $attribute_pairs[] = "{$key}='{$value}'";
    }

    $sql = "UPDATE " . static::$table_name . " SET ";
    $sql .= join(', ', $attribute_pairs);
    $sql .= " WHERE vendor_id='" . self::$database->escape_string($this->vendor_id) . "' ";
    $sql .= "LIMIT 1;";
    $result = self::$database->query($sql);
    return $result;
  }

  /**
   * Updates the phones table to include all phones in this vendor's phone_numbers and new_phone_numbers attributes. N Queries
   */
  protected function update_phones() {
    // Editing existing phone numbers if any exist
    if(count($this->phone_numbers) > 0) {
      foreach($this->phone_numbers as $phone_id => $phone_attributes) {
        $sql = "UPDATE phone_numbers SET ";
        $sql .= "phone_number='" . preg_replace('~\D~', "" , $phone_attributes['phone_number']) . "', ";
        $sql .= "phone_type='" . $phone_attributes['phone_type'] . "' ";
        $sql .= "WHERE phone_id='" . $phone_id . "' ";
        $sql .= "LIMIT 1;";

        $result = self::$database->query($sql);
        if(!$result){
          exit("Database query failed.");
        }
      }
    }

    // Adding new phone numbers
    if(count($this->new_phone_numbers) > 0) {
      foreach($this->new_phone_numbers as $phone_attributes) {
        $sql = "INSERT INTO phone_numbers (ph_vendor_id, phone_number, phone_type) ";
        $sql .= "VALUES ('";
        $sql .= $this->vendor_id . "', '";
        $sql .= preg_replace('~\D~', "" , $phone_attributes['phone_number']) . "', '";
        $sql .= $phone_attributes['phone_type'] . "');";

        $result = self::$database->query($sql);
        if(!$result){
          exit("Database query failed.");
        }
      }
    }
  }

  /**
   * 
   */
  public function delete() {
    $sql = "DELETE FROM " . static::$table_name . " ";
    $sql .= "WHERE vendor_id='" . self::$database->escape_string($this->vendor_id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }



  // RENDERING FUNCTIONS ================================================

  /**
   * Formats a 10 digit string of numbers into a phone number. i.e '(123) 456-7890'
   * 
   * @param string the 10 digit string of numbers to convert
   * 
   * @return string the formatted phone number
   */
  static public function phone_to_string($phone_number){
    return "(" . substr($phone_number, 0, 3) . ") " .
    substr($phone_number, 3, 3) . "-" .
    substr($phone_number, 6, 4);
  }


}

?>
