<?php

class DatabaseObject {

  /**
   * The MySQLi connection to the database, as set in initialize.php
   * @var mysqli
   */
  static protected $database;

  /**
   * The name of the table directly associated with this object.
   */
  static protected $table_name = "";

  /**
   * A list of strings for each of the columns in the associated table.
   */
  static protected $db_columns = [];

  /**
   * A list of strings that is filled when an instance of this object is validated.
   */
  public $errors = [];

  /**
   * REFACTOR THIS IN CHILD CLASSES! The SQL table field for id as it appears in tables. Must be replaced with the specific id field of each class.
   */
  public $id;


  /**
   * Sets the database that all child classes of DatabaseObject reference. 
   * 
   * @param mysqli $database The MySQLi connection
   */
  static public function set_database($database) {
    self::$database = $database;
  }

  /**
   * Queries the database and returns a list of object instances based on a given SQL statement. Can only search this class's designated table. 1 Query.
   * 
   * @param string $sql the SQL statement to query the database.
   * 
   * @return static[] a list of instances of the class that called this function
   */
  static public function find_by_sql($sql) {
    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed.");
    }

    // results into objects
    $object_array = [];
    while($record = $result->fetch_assoc()) {
      $object_array[] = static::instantiate($record);
    }

    $result->free();

    return $object_array;
  }

  /**
   * Queries the database and returns a list of all objects within this class's designated table. Can only search this class's designated table. 1 Query.
   * 
   * @return static[] a list of all instances of the class that called this function
   */
  static public function find_all() {
    $sql = "SELECT * FROM " . static::$table_name;
    return static::find_by_sql($sql);
  }

  /**
   * Queries the database and returns a single instantiated object with a given id (the first column in the class's db_columns attribute). Can only search this class's designated table. 1 Query.
   * 
   * @param int $id the unique identifier only associated with one object in this class's table
   * 
   * @return static|bool the object found by this search, if it exists
   */
  static public function find_by_id($id) {
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE " . static::$db_columns[0] . "='" . self::$database->escape_string($id) . "'";
    $obj_array = static::find_by_sql($sql);
    if(!empty($obj_array)) {
      return array_shift($obj_array);
    } else {
      return false;
    }
  }

  /**
   * Reads a row of results returned by MySQLi->fetch_assoc() and turns it into an instance of an object of this class for every column in the table that has the same name as this class's attributes.
   * 
   * @param mixed $record a row of results from MySQLi->fetch_assoc()
   * 
   * @return static an instance of the object found in that row 
   */
  static protected function instantiate($record) {
    $object = new static;
    // Could manually assign values to properties
    // but automatically assignment is easier and re-usable
    foreach($record as $property => $value) {
      if(property_exists($object, $property)) {
        $object->$property = $value;
      }
    }
    return $object;
  }

  /**
   * REFACTOR THIS IN CHILD CLASSES! Makes sure this instance is valid for being stored in the database.
   * 
   * @return array returns a list of errors as strings. 
   */
  protected function validate() {
    $this->errors = [];

    // Add custom validations

    return $this->errors;
  }

  /**
   * REFACTOR THIS IN CHILD CLASSES! Creates a new row in the database based on this object. 1 Query
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
    $sql .= "')";
    $result = self::$database->query($sql);
    if($result) {
      $this->id = self::$database->insert_id;
    }
    return $result;
  }

  /**
   * REFACTOR THIS IN CHILD CLASSES! Modifies an existing row in the database based on this object. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  protected function update() {
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $attribute_pairs = [];
    foreach($attributes as $key => $value) {
      $attribute_pairs[] = "{$key}='{$value}'";
    }

    $sql = "UPDATE " . static::$table_name . " SET ";
    $sql .= join(', ', $attribute_pairs);
    $sql .= " WHERE id='" . self::$database->escape_string($this->id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }

  /**
   * REFACTOR THIS IN CHILD CLASSES! Determines if this object already exists in the database and then stores it in a new or existing row. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function save() {
    // A new record will not have an ID yet
    if(isset($this->id)) {
      return $this->update();
    } else {
      return $this->create();
    }
  }

  /**
   * Takes in an array of arguments and updates this object's properties with the provided arguments.
   * 
   * @param array $args an associative array of arguments, with keys equal to the object's property names and values equal to the new values for the object
   */
  public function merge_attributes($args=[]) {
    foreach($args as $key => $value) {
      if(property_exists($this, $key) && !is_null($value)) {
        $this->$key = $value;
      }
    }
  }

  /**
   * Gets all of this object's properties' values that are associated with fields in the table.
   * 
   * @return array an array of all of this object's properties' values
   */
  public function attributes() {
    $attributes = [];
    foreach(static::$db_columns as $column) {
      if($column == 'id') { continue; }
      $attributes[$column] = $this->$column;
    }
    return $attributes;
  }

  /**
   * Sanitizes this object's properties' values for storage into SQL.
   * 
   * @return array an array of all of this object's properties' values
   */
  protected function sanitized_attributes() {
    $sanitized = [];
    foreach($this->attributes() as $key => $value) {
      $sanitized[$key] = self::$database->escape_string($value);
    }
    return $sanitized;
  }

  /**
   * REFACTOR THIS IN CHILD CLASSES! Removes a row from this class's table based on this object's id. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function delete() {
    $sql = "DELETE FROM " . static::$table_name . " ";
    $sql .= "WHERE id='" . self::$database->escape_string($this->id) . "' ";
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

}

?>
