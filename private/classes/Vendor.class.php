<?php

class Vendor extends DatabaseObject {

  static protected $table_name = 'vendors';
  static protected $db_columns = ['vendor_id', 'vd_user_id', 'vendor_display_name', 'vendor_desc', 'contact_info', 'address', 'city', 'vd_state_id'];

  public $vendor_id;
  public $vd_user_id;
  public $vendor_display_name;
  public $vendor_desc;
  public $contact_info;
  public $address;
  public $city;
  public $vd_state_id;

  public $state;
  public $user;

  public $phone_numbers = [];
  public $vendor_inventory = [];
  public $listed_dates = [];

  // SQL FUNCTIONS ====================================================

  static public function list_all() {
    $sql = "SELECT vendor_id, vendor_display_name ";
    $sql .= "FROM " . static::$table_name . " ";

    return static::find_by_sql($sql);
  }

  static public function find_by_id($vendor_id) {
    
  }



}

?>