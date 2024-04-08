<?php

class CalendarDate extends DatabaseObject {

  static protected $table_name = 'calendar';
  static protected $db_columns = ['calendar_id', 'date'];

  /**
   * The CalendarDate's id value within the calendar table.
   */
  public $calendar_id;

  /**
   * The CalendarDate's date, usually stored in SQL datetime, i.e '2024-4-7'.
   */
  public $date;

  /**
   * A list of vendor_display_names from the vendors table that have a calendar_listing match with this CalendarDate.
   */
  public $listed_vendors = [];

  // public $year;
  // public $month;
  // public $day;


  // DATE FUNCTIONS ================================================

  /**
   * Returns a date formatted to SQL datetime, i.e '2024-4-7'.
   * 
   * @param string $date Unformatted date, Unix timestamp
   * 
   * @return string Formatted date
   */
  static public function to_sql_datetime($date){
    return date("Y-m-d", $date);
  }

  /**
   * Returns the current month as a string, i.e 'April'
   * 
   * @return string The name of the current month
   */
  static public function current_month_name(){
    return date('F');
  }

  /**
   * Returns the current month as an integer out of 12, i.e April would give '4'
   * 
   * @return int The number of the current month
   */
  static public function current_month_number(){
    return date('m');
  }

  /**
   * Returns the current year as a four digit integer, i.e '2024'.
   * 
   * @return int The current year
   */
  static public function current_year(){
    return date('Y');
  }


  /**
   * Returns this CalendarDate object's date as part of a grammatical sentence, i.e: 'March, 27th'.
   * 
   * @return string This CalendarDate object's date as part of a sentence
   */
  public function print_date(){
    $explodedDate = explode('-', $this->date);

    $year = $explodedDate[0];
    $month = $explodedDate[1];
    $day = $explodedDate[2];

    return date('F, jS', mktime(0, 0, 0, $month, $day, $year));
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
    // Setting default values for $monthNumber and $year
    $monthNumber = ($monthNumber) ? $monthNumber : static::current_month_number();
    $year = ($year) ? $year : static::current_year(); 
    
    $firstDay = static::month_first_day($monthNumber, $year);
    $daysInMonth = date('t', $firstDay);
    return $daysInMonth;
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

      $vendor_display_name = '';
      // Reading each cell
      foreach($row as $property => $value) {
        if(property_exists($object, $property)) {
          $object->$property = $value;
        } elseif($property === 'vendor_display_name') {
          $vendor_display_name = $value;
        }
      } // End foreach for cells

      // if there's already a CalendarDate object with that calendar_id
      if(in_array($object->calendar_id, $existing_ids)) {
        // Add to that CalendarDate object's listed_vendors instead of making a new object
        $object_array[$object->calendar_id]->listed_vendors[] = $vendor_display_name;
      } else {
        // Add the id to the list of existing ids
        $existing_ids[] = $object->calendar_id;
        // Add the vendor display name to this object
        $object->listed_vendors[] = $vendor_display_name;
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

    $sql = "SELECT c.calendar_id, c.date, v.vendor_display_name ";
    $sql .= "FROM calendar c, calendar_listing li, vendors v ";
    $sql .= "WHERE c.calendar_id = li.li_calendar_id ";
    $sql .= "AND li.li_vendor_id = v.vendor_id ";
    $sql .= "AND date >= '" . static::to_sql_datetime($month_first_day) . "';";

    // echo $sql;

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

    // Debugging
    // echo $sql;

    return static::find_listing_by_sql($sql);
  }

  /**
   * Returns the CalendarDate object corresponding to the next occurring market day, even if that is today (regardless of whether vendors are attending).
   * 
   * @return CalendarDate|false the next market day
   */
  static public function get_next_market_day() {
    $sql = "SELECT * FROM calendar ";
    $sql .= "WHERE date >= '" . date("Y-m-d") . "' ";
    $sql .= "ORDER BY date ";
    $sql .= "LIMIT 1;";

    $result = static::find_by_sql($sql);

    if(!empty($result)){
      $next_market_day = $result[0];
      return $next_market_day;
    } else {
      // echo "There is no calendar date listed";
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
    $sql = "SELECT li.li_calendar_id, v.vendor_display_name ";
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
      $vendor_display_name = '';

      // Reading each cell
      foreach($row as $property => $value) {
        if($property === 'vendor_display_name') {
          $vendor_display_name = $value;
        } elseif($property === 'li_calendar_id') {
          $li_calendar_id = $value;
        }
      } // End foreach for cells

      // Appending the vendor_display_name to the CalendarDate object with the id of $li_
      $calendarDate_by_id[$li_calendar_id]->listed_vendors[] = $vendor_display_name;
    } // End while for rows

    $result->free();

    // Returns the now populated associative array
    return $calendarDate_by_id;
  } 

  /**
   * TO DO RETURN DOCUMENTATION ==============================================================================================
   * Creates a CalendarDate object based on a date and inserts it into the calendar table.
   * 
   * @param string $date a date formatted to SQL datetime, i.e '2024-4-7'
   * 
   * 
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
   * Inserts this CalendarDate object into the calendar table.
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
   * TO DO RETURN DOCUMENTATION ==============================================================================================
   * Marks a vendor as attending this CalendarDate. Inserts into the calendar_listing table a row with this CalendarDate object's calendar_id and the given vendor_id. 1 Query
   * 
   * @param int $vendor_id The vendor's id as it appears in the vendors table
   * 
   * 
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
   * TO DO RETURN DOCUMENTATION ==============================================================================================
   * Un-marks a vendor, indicating they are no longer attending this CalendarDate. Deletes from the calendar_listing table. 1 Query
   * 
   * @param int $listing_id The listing_id as it appears in the calendar_listing table
   * 
   * 
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

      // Debugging
      // echo $year . "-" . $month . "-" . $day . "<br>";
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
      // echo '<div value="' . $year . '">';
      echo "<h3>" . $year . "</h3>";

      // Month loop
      foreach($month as $month => $days){
        // Creates a table to contain the entire month
        echo '<table data-date="' . $year . '-' . $month . '">';
        // echo '<table value="' . $month . '">';

        // Captions the month with the month name, i.e 'April'
        echo "<caption>" . date("F", static::month_first_day($month, $year)) . "</caption>";
        echo "<tr>";
        echo "<th>Monday</th>";
        echo "<th>Tuesday</th>";
        echo "<th>Wednesday</th>";
        echo "<th>Thursday</th>";
        echo "<th>Friday</th>";
        echo "<th>Saturday</th>";
        echo "<th>Sunday</th>";
        echo "</tr>";

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
            // Change the cells class to market day and give it a dataset 'date' value, then print the day number
            echo '<td class="market_day" data-date="' . $year . '-' . $month . '-' . $day_counter_string . '">' . $day_counter;

            // Adds the Market day text and lists out all vendors
            $days[$day_counter]->list_as_day();

          } 
          // If the [$month][] associative array, does NOT contain a stored object for this day, print the cell as normal
          else {
            // Prints the day number
            echo "<td>" . $day_counter;
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
      // echo "</div>";
    } // End Year
  } // End create_calendar()


  /**
   * Called in CalendarDate::create_calendar(). Causes this CalendarDate object to list itself as a market day in the HTML calendar table and list out its listed_vendors in a ul if it as any.
   */
  public function list_as_day(){
    echo "<br>";
    echo "Market day<br>";
    if(count($this->listed_vendors) > 0){
      echo "<ul>";
      foreach($this->listed_vendors as $vendor_display_name){
        echo "<li>" . $vendor_display_name . "</li>";
      }
      echo "</ul>";
    }
  }


  // SQL
  // SELECT calendar_id, date, vendor_display_name
  // FROM calendar c, calender_listing li, vendors v
  // WHERE c.calendar_id = li.li_calendar_id
  //   AND li.li_vendor_id = v.vendor_id
  //   AND (after day)

}

?>