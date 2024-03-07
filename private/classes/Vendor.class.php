<?php

class Vendor extends DatabaseObject {

  static protected $table_name = 'vendors';
  static protected $db_columns = ['vendor_id', 'vd_user_id', 'vendor_display_name', 'vendor_desc', 'contact_info', 'address', 'city', 'vd_state_id', 'is_pending'];

  public $vendor_id;
  public $vd_user_id;
  public $vendor_display_name;
  public $vendor_desc;
  public $contact_info;
  public $address;
  public $city;
  public $vd_state_id;
  public $is_pending;

  public $state;
  public $user;

  public $phone_numbers = [];
  public $vendor_inventory = [];
  public $listed_dates = [];

  // SQL FUNCTIONS ====================================================

  static public function list_all() {
    $sql = "SELECT vendor_id, vendor_display_name ";
    $sql .= "FROM " . static::$table_name . " ";
    $sql .= "WHERE is_pending = FALSE;";

    return static::find_by_sql($sql);
  }

  static public function find_by_id($vendor_id) {
    $sql = "SELECT vendor_id, vendor_display_name ";
    $sql .= "FROM " . static::$table_name . " ";
    $sql .= "WHERE vendor_id = " . $vendor_id . ";";

    return static::find_by_sql($sql)[0];
  }

  static public function find_by_user_id($user_id) {
    $sql = "SELECT vendor_id, vendor_display_name, is_pending ";
    $sql .= "FROM " . static::$table_name . " ";
    $sql .= "WHERE vd_user_id = " . $user_id . ";";
    
    return static::find_by_sql($sql);
  }

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

  public function populate_phones(){
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

  public function populate_dates(){
    $this->listed_dates = CalendarDate::find_by_vendor($this->vendor_id);
  }

  public function populate_inventory(){
    $this->vendor_inventory = VendorInventory::find_by_vendor($this->vendor_id);
  }

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


}

?>