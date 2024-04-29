<?php

class CalendarDate extends DatabaseObject {

  static protected $table_name = 'calendar';
  static protected $db_columns = ['calendar_id', 'date'];

  static public $market_hours = ['Monday' => '9am-7pm',
  'Tuesday' => '9am-7pm',
  'Wednesday' => '9am-7pm',
  'Thursday' => '9am-7pm',
  'Friday' => '9am-9pm',
  'Saturday' => '9am-9pm',
  'Sunday' => '12pm-9pm'];

  /**
   * The CalendarDate's id value within the calendar table.
   */
  public $calendar_id;

  /**
   * The CalendarDate's date, usually stored in SQL datetime, i.e '2024-4-7'.
   */
  public $date;

  /**
   * A an associative array of vendors from the vendors table that have a calendar_listing match with this CalendarDate. Keys: vendor_ids. Values: vendor_display_names.
   */
  public $listed_vendors = [];

  /**
   * The maximum number of vendors to show in a calendar cell before instead linking them in a popup
   */
  static public $max_vendors_per_calendar_list = 3;



  // DATE FUNCTIONS ================================================

  /**
   * Returns a date formatted to SQL datetime, i.e '2024-4-7'.
   * 
   * @param string $date Unformatted date, Unix timestamp
   * 
   * @return string Formatted date
   */
  static public function to_sql_datetime($date){
    date_default_timezone_set('America/New_York');
    return date("Y-m-d", $date);
  }

  /**
   * Returns the current month as a string, i.e 'April'
   * 
   * @return string The name of the current month
   */
  static public function current_month_name(){
    date_default_timezone_set('America/New_York');
    return date('F');
  }

  /**
   * Returns the next month as a string, i.e 'April'
   * 
   * @return string The name of the next month
   */
  static public function next_month_name(){
    date_default_timezone_set('America/New_York');
    return date('F', CalendarDate::month_first_day(CalendarDate::current_month_number()+1));
  }

  /**
   * Returns the current month as an integer out of 12, i.e April would give '4'
   * 
   * @return int The number of the current month
   */
  static public function current_month_number(){
    date_default_timezone_set('America/New_York');
    return date('m');
  }

  /**
   * Returns the current year as a four digit integer, i.e '2024'.
   * 
   * @return int The current year
   */
  static public function current_year(){
    date_default_timezone_set('America/New_York');
    return date('Y');
  }


  /**
   * Returns this CalendarDate object's date as part of a grammatical sentence, i.e: 'Wednesday, March 27th'.
   * 
   * @return string This CalendarDate object's date as part of a sentence
   */
  public function print_date() {
    date_default_timezone_set('America/New_York');
    $explodedDate = explode('-', $this->date);

    $year = $explodedDate[0];
    $month = $explodedDate[1];
    $day = $explodedDate[2];

    return date('l, F jS', mktime(0, 0, 0, $month, $day, $year));
  }

  /**
   * Returns the provided date as part of a grammatical sentence, i.e: 'Wednesday, March 27th'.
   * 
   * @param string $date the date to print
   * 
   * @return string This date as part of a sentence
   */
  static public function print_date_from_string($date) {
    date_default_timezone_set('America/New_York');
    $explodedDate = explode('-', $date);

    $year = $explodedDate[0];
    $month = $explodedDate[1];
    $day = $explodedDate[2];

    return date('l, F jS', mktime(0, 0, 0, $month, $day, $year));
  }

  /**
   * Get the market hour schedule for this CalendarDate object.
   * 
   * @return string the market hours
   */
  public function print_market_hours() {
    date_default_timezone_set('America/New_York');
    $explodedDate = explode('-', $this->date);

    $year = $explodedDate[0];
    $month = $explodedDate[1];
    $day = $explodedDate[2];

    return static::$market_hours[date('l', mktime(0, 0, 0, $month, $day, $year))];
  }

  /**
   * Returns the first day of a given month, as a Unix timestamp.
   * 
   * @param int $monthNumber The number between 1 and 12 that corresponds to the desired month. Defaults to the current month.
   * @param int $year The four digit year number. Defaults to the current year.
   * 
   * @return int The Unix timestamp of the first day of the month
   */
  static public function month_first_day($monthNumber = NULL, $year = NULL){
    date_default_timezone_set('America/New_York');
    // Setting default values for $monthNumber and $year
    $monthNumber = ($monthNumber) ? $monthNumber : static::current_month_number();
    $year = ($year) ? $year : static::current_year();

    $firstDay = mktime(0, 0 , 0, $monthNumber, 1, $year);
    return $firstDay;
  }

  /**
   * Returns the day of the week, 1 = Monday, 7 = Sunday, that a month starts on.
   * 
   * @param int $monthNumber The number between 1 and 12 that corresponds to the desired month. Defaults to the current month.
   * @param int $year The four digit year number. Defaults to the current year.
   * 
   * @return int The first day of the month as a dat of the week integer
   */
  static public function month_starting_day_number($monthNumber = NULL, $year = NULL){
    date_default_timezone_set('America/New_York');
    // Setting default values for $monthNumber and $year
    $monthNumber = ($monthNumber) ? $monthNumber : static::current_month_number();
    $year = ($year) ? $year : static::current_year();

    $firstDay = static::month_first_day($monthNumber, $year);
    $dayOfTheWeekNumber = date('N', $firstDay);
    return $dayOfTheWeekNumber;
  }

  /**
   * Returns the number of total days in a given month.
   * 
   * @param int $monthNumber The number between 1 and 12 that corresponds to the desired month. Defaults to the current month.
   * @param int $year The four digit year number. Defaults to the current year.
   * 
   * @return int The total number of days in a month
   */
  static public function days_in_month($monthNumber = NULL, $year = NULL){
    date_default_timezone_set('America/New_York');
    // Setting default values for $monthNumber and $year
    $monthNumber = ($monthNumber) ? $monthNumber : static::current_month_number();
    $year = ($year) ? $year : static::current_year(); 
    
    $firstDay = static::month_first_day($monthNumber, $year);
    $daysInMonth = date('t', $firstDay);
    return $daysInMonth;
  }

  /**
   * Returns the last day of this month, as a Unix timestamp.
   * 
   * @return int The Unix timestamp of the last day of this month
   */
  static public function last_day_in_this_month() {
    date_default_timezone_set('America/New_York');
    // Setting values for $monthNumber and $year
    $monthNumber = static::current_month_number();
    $year = static::current_year(); 

    return  mktime(0, 0, 0, $monthNumber, CalendarDate::days_in_month(), $year);
  }

  /**
   * Checks if a given CalendarDate object corresponds to today.
   * 
   * @return bool if the CalendarDate object is today
   */
  public function is_today() {
    date_default_timezone_set('America/New_York');
    $explodedDate = explode('-', $this->date);

    $year = $explodedDate[0];
    $month = $explodedDate[1];
    if($explodedDate[2] < 10){
      $explodedDate[2] = substr($explodedDate[2], 1);
    }
    $day = $explodedDate[2];

    $explodedToday = explode('-', date("Y-m-d"));

    $todayYear = $explodedToday[0];
    $todayMonth = $explodedToday[1];
    if($explodedToday[2] < 10){
      $explodedToday[2] = substr($explodedToday[2], 1);
    }
    $todayDay = $explodedToday[2];

    return ($year == $todayYear && $month == $todayMonth && $day == $todayDay);
  }

  /**
   * Checks if a given CalendarDate object corresponds to this month.
   * 
   * @return bool if the CalendarDate object is today
   */
  public function is_current_month() {
    date_default_timezone_set('America/New_York');
    $explodedDate = explode('-', $this->date);
    $year = $explodedDate[0];
    $month = $explodedDate[1];

    return (($month == CalendarDate::current_month_number()) && ($year == CalendarDate::current_year()));
  }

  // SQL FUNCTIONS =====================================================

  /**
   * Queries the database for a list of CalendarDate objects WITH populated vendor lists.
   * 
   * @param string $sql The sql query ("SELECT properties ", "FROM tables ", "WHERE conditions ")
   * 
   * @return CalendarDate[] The list of CalendarDate objects WITH populated listed_vendor attributes
   */
  static public function find_listing_by_sql($sql) {
    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed. " . $sql);
    }

    // results into objects, CalendarDate objects WITH populated listed_vendors
    $object_array = [];
    $existing_ids = [];

    // Reading each row
    while($row = $result->fetch_assoc()) {
      // Make a new CalendarDate object
      $object = new static;

      $vendor_id = '';
      $vendor_display_name = '';
      // Reading each cell
      foreach($row as $property => $value) {
        if(property_exists($object, $property)) {
          $object->$property = $value;
        } elseif($property === 'vendor_display_name') {
          $vendor_display_name = $value;
        } elseif($property === 'vendor_id'){
          $vendor_id = $value;
        }
      } // End foreach for cells

      // if there's already a CalendarDate object with that calendar_id
      if(in_array($object->calendar_id, $existing_ids)) {
        // Add to that CalendarDate object's listed_vendors instead of making a new object
        $object_array[$object->calendar_id]->listed_vendors[$vendor_id] = $vendor_display_name;
      } else {
        // Add the id to the list of existing ids
        $existing_ids[] = $object->calendar_id;
        // Add the vendor display name to this object
        $object->listed_vendors[$vendor_id] = $vendor_display_name;
        // Add the object to the object array at the key of its calendar_id
        $object_array[$object->calendar_id] = $object;
      }
    } // End while for rows

    $result->free();

    return $object_array;
  } // End find_listing_by_sql()

  /**
   * Queries the database for a list of CalendarDate objects WITHOUT populated vendor lists.
   * 
   * @param string $sql The sql query ("SELECT properties ", "FROM tables ", "WHERE conditions ")
   * 
   * @return CalendarDate[] The list of CalendarDate objects
   */
  static public function find_date_by_sql($sql) {
    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed. " . $sql);
    }

    // results into objects, CalendarDate objects WITHOUT populated listed_vendors
    $object_array = [];

    // Reading each row
    while($row = $result->fetch_assoc()) {
      // Make a new CalendarDate object
      $object = new static;

      // Reading each cell
      foreach($row as $property => $value) {
        if(property_exists($object, $property)) {
          $object->$property = $value;
        }
      } // End foreach for cells

    // Add the object to the object array at the key of its calendar_id
    $object_array[$object->calendar_id] = $object;
    } // End while for rows

    $result->free();

    return $object_array;
  } // End find_date_by_sql()

  /**
   * Queries the database for a list of all CalendarDates with at least one vendor listed that take place after the first of this month. 1 Query
   * 
   * @return CalendarDate[] The list of CalendarDate objects WITH populated listed_vendor attributes
   */
  static public function find_all_dates_with_vendors() {
    $month_first_day = static::month_first_day();

    $sql = "SELECT c.calendar_id, c.date, v.vendor_display_name, v.vendor_id ";
    $sql .= "FROM calendar c, calendar_listing li, vendors v ";
    $sql .= "WHERE c.calendar_id = li.li_calendar_id ";
    $sql .= "AND li.li_vendor_id = v.vendor_id ";
    $sql .= "AND date >= '" . static::to_sql_datetime($month_first_day) . "';";

    return static::find_listing_by_sql($sql);
  }

  /**
   * Queries the database for a list of all CalendarDates that take place after the first of this month. Does not populate listed_vendor attributes. 1 Query
   * 
   * @return CalendarDate[] The list of CalendarDate objects
   */
  static public function find_all_dates_empty() {
    $month_first_day = static::month_first_day();

    $sql = "SELECT * FROM calendar ";
    $sql .= "WHERE date >= '" . static::to_sql_datetime($month_first_day) . "';";

    return static::find_date_by_sql($sql);
  }

  /**
   * Queries the database for a list of all CalendarDates that a given vendor is attending. Does not populate listed_vendor attributes. 1 Query
   * 
   * @param int $vendor_id The vendor's id as it appears in the vendors table
   * 
   * @return CalendarDate[] The list of CalendarDate objects
   */
  static public function find_by_vendor($vendor_id) {

    $sql = "SELECT c.calendar_id, c.date ";
    $sql .= "FROM calendar c, calendar_listing li ";
    $sql .= "WHERE c.calendar_id = li.li_calendar_id ";
    $sql .= "AND li.li_vendor_id = " . $vendor_id . " ";
    $sql .= "AND c.date >= '" . date("Y-m-d") . "';";

    return static::find_date_by_sql($sql);
  }

  /**
   * Returns the CalendarDate object corresponding to the next occurring market day, even if that is today (regardless of whether vendors are attending). 1 Query
   * 
   * @return CalendarDate|false the next market day
   */
  static public function get_next_market_day() {
    date_default_timezone_set('America/New_York');
    $sql = "SELECT * FROM calendar ";
    $sql .= "WHERE date >= '" . date("Y-m-d") . "' ";
    $sql .= "ORDER BY date ";
    $sql .= "LIMIT 1;";

    $result = static::find_by_sql($sql);

    if(!empty($result)){
      $next_market_day = $result[0];
      return $next_market_day;
    } else {
      return false;
    }
    
  }

  /**
   * Returns the CalendarDate object corresponding to the second to next occurring market day (regardless of whether vendors are attending). 1 Query
   * 
   * @return CalendarDate|false the second to next market day
   */
  static public function get_upcoming_market_day() {
    date_default_timezone_set('America/New_York');
    $sql = "SELECT * FROM calendar ";
    $sql .= "WHERE date >= '" . date("Y-m-d") . "' ";
    $sql .= "ORDER BY date ";
    $sql .= "LIMIT 2;";

    $result = static::find_by_sql($sql);

    if(!empty($result)){
      $upcoming_market_day = $result[1];
      return $upcoming_market_day;
    } else {
      return false;
    }
    
  }

  /**
   * Returns the CalendarDate object corresponding to a given date.
   * 
   * @param string $date a date formatted to SQL datetime, i.e '2024-4-7'
   * 
   * @return CalendarDate|false the CalendarDate object corresponding to that date
   */
  static public function find_by_date($date){
    $sql = "SELECT * FROM calendar ";
    $sql .= "WHERE date = '" . $date . "';";
    // I can probably add LIMIT 1

    $result = static::find_by_sql($sql);

    if(!empty($result)){
      $found_date = $result[0];
      return $found_date;
    } else {
      // Report the error
      return false;
    }
  }

  /**
   * Queries the database to find the listing_id from the calendar_listing table row that corresponds with both this CalendarDate object and a given vendor id. 1 Query
   * 
   * @param int $vendor_id The vendor's id as it appears in the vendors table
   * 
   * @return int|false The listing_id as an int, if it exists
   */
  public function find_listing_id($vendor_id){

    $sql = "SELECT listing_id ";
    $sql .= "FROM calendar_listing ";
    $sql .= "WHERE li_calendar_id = '" . $this->calendar_id . "' ";
    $sql .= "AND li_vendor_id = '" . $vendor_id . "' ";
    $sql .= "LIMIT 1;";

    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed. " . $sql);
    }

    // results into objects
    $listing_id = false;

    // Reading each row
    while($row = $result->fetch_assoc()) {
      // Reading each cell
      foreach($row as $property => $value) {
        if($property === 'listing_id') {
          $listing_id = $value;
        } 
      } // End foreach for cells
    } // End while for rows

    $result->free();

    return $listing_id;
  }

  /**
   * Populates the listed_vendors attributes of a given list of CalendarDate objects, queries the database. 1 Query
   * 
   * @param CalendarDate[] $calendarDateArray an unorganized array of CalendarDate objects, without populated vendor_lists
   * 
   * @return CalendarDate[] a now populated associative array of CalendarDate objects, with their keys being their calendar_ids
   */
  static public function populate_vendor_list($calendarDateArray){
    // Creating a new associative array
    $calendarDate_by_id = [];

    // Getting all the calender_ids of the calendar date and storing them into an associative array with their id as the key
    foreach($calendarDateArray as $calendarDate){
      $calendarDate_by_id[$calendarDate->calendar_id] = $calendarDate;
    }

    // Building the sql query using a two table join
    $sql = "SELECT li.li_calendar_id, v.vendor_display_name, v.vendor_id ";
    $sql .= "FROM calendar_listing li, vendors v ";
    $sql .= "WHERE li.li_vendor_id = v.vendor_id ";
    $sql .= "AND li.li_calendar_id IN (";

    // Making a list of calendar_ids from the associative array's keys
    $calendar_id_list = array_keys($calendarDate_by_id);

    // Going through each id and adding it to the list
    while($calendar_id_list) {
      // Popping off the first element of the list of ids
      $current_id = array_shift($calendar_id_list);

      // Adding it to the IN statement
      $sql .=  $current_id;

      // Adding a trailing comma and space if there are more in the list
      if($calendar_id_list) { $sql .= ", "; }
    }

    // Closing the sql statement
    $sql .= ");";

    // Querying the database with the constructed sql statement
    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed. " . $sql);
    }

    // Storing the results into the existing calendarDates that are stored in the associative array

    // Reading each row
    while($row = $result->fetch_assoc()) {
      $li_calendar_id = '';
      $vendor_id = '';
      $vendor_display_name = '';

      // Reading each cell
      foreach($row as $property => $value) {
        if($property === 'vendor_display_name') {
          $vendor_display_name = $value;
        } elseif($property === 'li_calendar_id') {
          $li_calendar_id = $value;
        } elseif($property === 'vendor_id'){
          $vendor_id = $value;
        }
      } // End foreach for cells

      // Appending the vendor_display_name to the CalendarDate object with the id of $li_
      $calendarDate_by_id[$li_calendar_id]->listed_vendors[$vendor_id] = $vendor_display_name;
    } // End while for rows

    $result->free();

    // Returns the now populated associative array
    return $calendarDate_by_id;
  } 

  /**
   * Populates this CalendarDate's vendor list, queries the database. 1 Query
   */
  public function populate_this_vendor_list(){
    $single_date_array[] = $this;
    $this->listed_vendors = CalendarDate::populate_vendor_list($single_date_array)[$this->calendar_id]->listed_vendors;
  }
  
  /**
   * Gets a list of vendor ids corresponding to vendors appearing on this date. 1 Query
   * 
   * @return int[]|false a list of all vendor ids marked as appearing on this date, if there are any.
   */
  public function get_vendor_ids(){
    // Finding all vendor_ids associated with that date
    $sql = "SELECT li_vendor_id ";
    $sql .= "FROM calendar_listing ";
    $sql .= "WHERE li_calendar_id = '" . $this->calendar_id . "';";

    $result = self::$database->query($sql); 

    // Results into list of vendor ids
    $vendor_ids = [];

     // Reading each row
    while($row = $result->fetch_assoc()) {
      $vendor_id = '';

      // Reading each cell
      foreach($row as $property => $value) {
        if($property === 'li_vendor_id'){
          $vendor_id = $value;
        }
      } // End foreach for cells

      if($vendor_id !== ''){
        $vendor_ids[] = $vendor_id;
      }
    } // End while for rows

    $result->free();

    if(!empty($vendor_ids)){
      return $vendor_ids;
    } else {
      return false;
    }
    
  }

  /**
   * Creates a CalendarDate object based on a date and inserts it into the calendar table. 1 Query
   * 
   * @param string $date a date formatted to SQL datetime, i.e '2024-4-7'
   * 
   * @return mysqli_result|bool the query result
   */
  static public function create_new_date($date) {
    // Creating a new CalendarDate object
    $new_date = new static;

    // Setting the CalendarDate's date attribute
    $new_date->date = $date;

    // Inserting the new date into the calendar table
    $result = $new_date->create();

    // Returning the created date if the query is successful
    if($result){
      return $new_date;
    } else {
      return $result;
    }
    
  }

  /**
   * Inserts this CalendarDate object into the calendar table. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function create() {
    // Making sure the date validates
    $this->validate();
    if(!empty($this->errors)) { return false; }

    // Building the SQL statement
    $sql = "INSERT INTO calendar (";
    $sql .= "date";
    $sql .= ") VALUES ('";
    $sql .= $this->date;
    $sql .= "');";

    // Querying the database
    $result = self::$database->query($sql);

    // Updating the Calendar
    if($result) {
      $this->calendar_id = self::$database->insert_id;
    }
    return $result;
  }

  /**
   * Removes this CalendarDate from the calendar table. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function delete() {
    // Removing all calendar_listings with this date from calendar_listings
    $sql = "DELETE FROM calendar_listing ";
    $sql .= "WHERE li_calendar_id='" . self::$database->escape_string($this->calendar_id) . "';";
    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed. " . $sql);
    }

    // Removing this calendar date from calendar
    $sql = "DELETE FROM " . static::$table_name . " ";
    $sql .= "WHERE calendar_id='" . self::$database->escape_string($this->calendar_id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }

  /**
   * Marks a vendor as attending this CalendarDate. Inserts into the calendar_listing table a row with this CalendarDate object's calendar_id and the given vendor_id. 1 Query
   * 
   * @param int $vendor_id The vendor's id as it appears in the vendors table
   * 
   * @return mysqli_result|bool the query result
   */
  public function create_new_listing($vendor_id){
    if(in_array($vendor_id, $this->listed_vendors)) { return false;}

    $this->validate();
    if(!empty($this->errors)) { return false; }

    $sql = "INSERT INTO calendar_listing (";
    $sql .= "li_calendar_id, li_vendor_id";
    $sql .= ") VALUES ('";
    $sql .= $this->calendar_id . "', '" . $vendor_id;
    $sql .= "');";

    $result = self::$database->query($sql);
    if($result) {
      $this->listed_vendors[] = $vendor_id;
    }
    return $result;
  }

  /**
   * Un-marks a vendor, indicating they are no longer attending this CalendarDate. Deletes from the calendar_listing table. 1 Query
   * 
   * @param int $listing_id The listing_id as it appears in the calendar_listing table
   * 
   * @return mysqli_result|bool the query result
   */
  public function delete_listing($listing_id) {
    $sql = "DELETE FROM calendar_listing ";
    $sql .= "WHERE listing_id='" . self::$database->escape_string($listing_id) . "'  ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }



  // CALENDAR RENDERING FUNCTIONS =====================================

  /**
   * Takes in an unorganized array of CalendarDates and sorts them into a sorted three tiered associative array of CalendarDates where each key is the object's date
   * 
   * @param CalendarDate[] $calendarDateArray an unorganized array of CalendarDate objects
   * 
   * @return CalendarDate[][][] a sorted three tiered associative array, [year][month][day]
   */
  static public function explode_dates($calendarDateArray) {
    $full_calendar = [];
    
    foreach($calendarDateArray as $calendarDate){
      $explodedDate = explode('-', $calendarDate->date);

      $year = $explodedDate[0];
      $month = $explodedDate[1];
      // Turning days with leading 0s into single digit strings
      if($explodedDate[2] < 10){
        $explodedDate[2] = substr($explodedDate[2], 1);
      }
      $day = $explodedDate[2];

      $full_calendar[$year][$month][$day] = $calendarDate;
    }

    return $full_calendar;
  }

  /**
   * Renders out a series of CalendarDates as HTML tables, each month being its own table.  
   * 
   * @param CalendarDate[] $calendarDateArray an unorganized array of CalendarDate objects
   */
  static public function create_calendar($calendarDateArray) {
    // Organizing the unordered array of CalendarDates into a three tiered associative array, $full_calendar[$year][$month][$day]
    $full_calendar = static::explode_dates($calendarDateArray);

    // Year loop
    foreach($full_calendar as $year => $month){
      echo "<h3>" . $year . "</h3>";

      // Month loop
      foreach($month as $month => $days){
        // Creates a table to contain the entire month
        echo '<table data-date="' . $year . '-' . $month . '">';

        // Captions the month with the month name, i.e 'April'
        echo "<caption>" . date("F", static::month_first_day($month, $year)) . "</caption>";
        echo "<tr>";
        echo '<th>Mon<span class="unabbreviated">day</span></th>';
        echo '<th>Tue<span class="unabbreviated">sday</span></th>';
        echo '<th>Wed<span class="unabbreviated">nesday</span></th>';
        echo '<th>Thu<span class="unabbreviated">rsday</span></th>';
        echo '<th>Fri<span class="unabbreviated">day</span></th>';
        echo '<th>Sat<span class="unabbreviated">urday</span></th>';
        echo '<th>Sun<span class="unabbreviated">day</span></th>';
        echo '</tr>';

        $days_in_month = static::days_in_month($month, $year);
        $day_counter = 1;
        $starting_weekday = static::month_starting_day_number($month, $year);
        $weekday_counter = 1;

        // Starting the first week
        echo "<tr>";

        //Adds empty day cells if the month doesn't start on a monday
        while($weekday_counter < $starting_weekday) {
          echo '<td class="empty"></td>';
          $weekday_counter++;
        }

        // Day Loop
        while ($day_counter <= $days_in_month) {
          // If it's the start of a new week, add a new row, unless the month started on a monday
          if($weekday_counter == 1 && $day_counter != 1){
            echo "<tr>";
          }

          // If the [$month][] associative array contains a stored object for a given day, mark it as a market day
          if(array_key_exists($day_counter, $days)){
            
            // Adds leading 0s to the day counter for single digit days
            $day_counter_string = $day_counter >= 10 ? $day_counter : '0' . $day_counter;
            $full_date_as_string = $year . '-' . $month . '-' . $day_counter_string;

            // Change the cells class to market day and give it a dataset 'date' value, then print the day number
            echo '<td class="market_day" data-date="' . $full_date_as_string . '">';

            echo '<a href="' . url_for('calendar/show.php?date=' . $full_date_as_string) . '" class="show-link">' . $day_counter . '</a>';
            echo '<span class="day-counter">' . $day_counter . '</span>';

            // Adds the Market day text and lists out all vendors
            $days[$day_counter]->list_as_day($full_date_as_string);

          } 
          // If the [$month][] associative array, does NOT contain a stored object for this day, print the cell as normal
          else {
            // Prints the day number

            // Adds leading 0s to the day counter for single digit days
            $day_counter_string = $day_counter >= 10 ? $day_counter : '0' . $day_counter;
            $full_date_as_string = $year . '-' . $month . '-' . $day_counter_string;
            echo '<td>';
            echo '<a href="' . url_for('calendar/show.php?date=' . $full_date_as_string) . '" class="show-link">' . $day_counter . '</a>';
            echo '<span class="day-counter">' . $day_counter . '</span>';
            echo '<div class="day-content"></div';
          }

          // Close the cell
          echo "</td>";

          // Count further into the week, i.e 'Monday' -> 'Tuesday'
          $weekday_counter++;

          // If 7 days have been printed, end the week and reset the weekday counter
          if($weekday_counter > 7){
            echo "</tr>";
            $weekday_counter = 1;
          }
          // Count further into the month, i.e 'April 1' -> 'April 2'
          $day_counter++;
        } // End Day

        // If the month does not end on a Sunday, finish out any hanging weeks with empty cells
        while($weekday_counter <= 7 && $weekday_counter != 1) {
          echo '<td class="empty"></td>';
          $weekday_counter++;
          if($weekday_counter > 7){
            echo "</tr>";
          }
        }

        echo "</table>";
      } // End Month
    } // End Year
  } // End create_calendar()


  /**
   * Called in CalendarDate::create_calendar(). Causes this CalendarDate object to list itself as a market day in the HTML calendar table and list out its listed_vendors in a ul if it as any.
   * 
   * @param string $full_date_as_string
   */
  public function list_as_day($full_date_as_string) {
    echo '<div class="day-content">';
    echo "<br>";
    echo "Market day<br>";
    if(count($this->listed_vendors) > 0){
      echo "<ul>";
      $num_listed_vendors = 1;
      foreach($this->listed_vendors as $vendor_id => $vendor_display_name) {
        // Only allow for a set maximum of vendors to be displayed normally
        if($num_listed_vendors <= CalendarDate::$max_vendors_per_calendar_list) {
          echo '<li><a href="' . url_for('vendors/show.php?id=' . h($vendor_id)) . '">' . $vendor_display_name . '</a></li>';
        } 
        // Afterwards, print the link an change the class of the list items to be hidden
        else {
          // If it's the first listed vendor after the maximum, print the link
          if($num_listed_vendors == CalendarDate::$max_vendors_per_calendar_list + 1) {
            echo '<a href="' . url_for('calendar/show.php?date=' . h($full_date_as_string)) . '" class="view-full">Show all Vendors.</a>';
          }
          // Print the listing with a special hidden class
          echo '<li class="excess-vendor"><a href="' . url_for('vendors/show.php?id=' . h($vendor_id)) . '">' . $vendor_display_name . '</a></li>';
        }

        
        $num_listed_vendors++;
      }
      echo "</ul>";
    }
    echo '</div>';
  }

  /**
   * Reduces the listed vendors of the provided calendar dates to a given amount.
   * 
   * @param CalendarDate[] $calendarDateArray an unorganized array of CalendarDate objects with populated vendors
   * @param int $number_of_vendors the maximum number of vendors to reduce the CalendarDates listed vendors to.
   */
  static public function randomly_select_featured_vendors($calendarDateArray, $number_of_vendors) {
    foreach($calendarDateArray as $calendarDate) {
      $vendors = $calendarDate->listed_vendors;
      // If there aren't any vendors, just continue to the next calendarDate
      if(!$vendors) { continue; }
      // If there aren't enough vendors in the array to exclude any, don't do anything
      if(count($vendors) <= $number_of_vendors) {
        // Everyone is featured! Yaaaay!
      } 
      // Otherwise go through and keep randomly selecting vendors until the count is met
      else {
        $selected_vendors = [];
        $num_vendors_selected = 0;
        while($num_vendors_selected < $number_of_vendors) {
          $vendor_ids = array_keys($vendors);
          // Randomly choose a vendor in the array
          $selected_index = $vendor_ids[random_int(0, count($vendor_ids)-1)];
          // Remove the vendor from the array
          $selected_vendor = $vendors[$selected_index];
          unset($vendors[$selected_index]);
          // Add the vendor to the list of selected vendors
          $selected_vendors[$selected_index] = $selected_vendor;
          // Increment the counter
          $num_vendors_selected++;
        }
        // Store the selected vendors in the calendarDate
        $calendarDate->listed_vendors = $selected_vendors;
      }
    }
  } 

}

?>