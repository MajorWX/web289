<?php

class CalendarDate extends DatabaseObject {

  static protected $table_name = 'calendar';
  static protected $db_columns = ['calendar_id', 'date'];

  public $calendar_id;
  public $date;
  public $listed_vendors = [];

  // public $year;
  // public $month;
  // public $day;


  // DATE FUNCTIONS ================================================

  static public function to_sql_datetime($date){
    return date("Y-m-d", $date);
  }

  static public function current_month_name(){
    return date('F');
  }

  static public function current_month_number(){
    return date('m');
  }

  static public function current_year(){
    return date('Y');
  }

  public function print_date(){
    $explodedDate = explode('-', $this->date);

    $year = $explodedDate[0];
    $month = $explodedDate[1];
    $day = $explodedDate[2];

    return date('F, jS', mktime(0, 0, 0, $month, $day, $year));

  }

  /**
   * Returns the first day of a given month, as a DateTime object
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
   */
  static public function month_starting_day_number($monthNumber = NULL, $year = NULL){
    // Setting default values for $monthNumber and $year
    $monthNumber = ($monthNumber) ? $monthNumber : static::current_month_number();
    $year = ($year) ? $year : static::current_year();

    $firstDay = static::month_first_day($monthNumber, $year);
    $dayOfTheWeekNumber = date('N', $firstDay);
    return $dayOfTheWeekNumber;
  }

  static public function days_in_month($monthNumber = NULL, $year = NULL){
    // Setting default values for $monthNumber and $year
    $monthNumber = ($monthNumber) ? $monthNumber : static::current_month_number();
    $year = ($year) ? $year : static::current_year(); 
    
    $firstDay = static::month_first_day($monthNumber, $year);
    $daysInMonth = date('t', $firstDay);
    return $daysInMonth;
  }

  // SQL FUNCTIONS =====================================================

  static public function find_listing_by_sql($sql) {
    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed.");
    }

    // results into objects
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
  }

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

  // TO ADD, FIND ALL EMPTY AND FULL DATES

  static public function find_by_vendor($vendor_id) {

    $sql = "SELECT c.calendar_id, c.date ";
    $sql .= "FROM calendar c, calendar_listing li ";
    $sql .= "WHERE c.calendar_id = li.li_calendar_id ";
    $sql .= "AND li.li_vendor_id = " . $vendor_id . " ";
    $sql .= "AND c.date >= '" . date("Y-m-d") . "';";

    // echo $sql;

    return static::find_listing_by_sql($sql);
  }

  static public function get_next_market_day() {
    $sql = "SELECT * FROM calendar ";
    $sql .= "WHERE date >= '" . date("Y-m-d") . "';";

    // $sql .= "WHERE date >= '" . date("Y-m-d") . "' ";
    // $sql .= "LIMIT 1;";

    $result = static::find_by_sql($sql);

    if(!empty($result)){
      $next_market_day = $result[0];
      return $next_market_day;
    } else {
      // echo "There is no calendar date listed";
      return false;
    }
    
  }

  static public function find_by_date($date){
    $sql = "SELECT * FROM calendar ";
    $sql .= "WHERE date = '" . $date . "';";

    $result = static::find_by_sql($sql);

    if(!empty($result)){
      $found_date = $result[0];
      return $found_date;
    } else {
      // Report the error
      return false;
    }
  }

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

  public function populate_vendor_list(){

  }

  public function create_new_listing($vendor_id){
    if(in_array($vendor_id, $this->listed_vendors)) { return false;}

    $this->validate();
    if(!empty($this->errors)) { return false; }

    $sql = "INSERT INTO calendar_listing (";
    $sql .= "li_calendar_id, li_vendor_id";
    $sql .= ") VALUES ('";
    $sql .= $this->calendar_id . "', '" . $vendor_id;
    $sql .= "')";
    $result = self::$database->query($sql);
    if($result) {
      $this->listed_vendors[] = $vendor_id;
    }
    return $result;
  }

  public function delete_listing($listing_id) {
    $sql = "DELETE FROM calendar_listing ";
    $sql .= "WHERE listing_id='" . self::$database->escape_string($listing_id) . "'  ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }



  // CALENDAR RENDERING FUNCTIONS =====================================

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

  static public function create_calendar($calendarDateArray) {
    $full_calendar = static::explode_dates($calendarDateArray);
    foreach($full_calendar as $year => $month){
      // echo '<div value="' . $year . '">';
      echo "<h3>" . $year . "</h3>";
      foreach($month as $month => $days){
        echo '<table>';
        // echo '<table value="' . $month . '">';
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

        // Starting week
        echo "<tr>";

        //Adds empty days 
        while($weekday_counter < $starting_weekday) {
          echo '<td class="empty"></td>';
          $weekday_counter++;
        }

        // Day Loop
        while ($day_counter <= $days_in_month) {
          if($weekday_counter == 1 && $day_counter != 1){
            echo "<tr>";
          }

          
          if(array_key_exists($day_counter, $days)){
            $day_counter_string = $day_counter >= 10 ? $day_counter : '0' . $day_counter;
            echo '<td class="market_day" data-date="' . $year . '-' . $month . '-' . $day_counter_string . '">' . $day_counter;
            $days[$day_counter]->list_as_day();
          } else {
            echo "<td>" . $day_counter;
          }

          echo "</td>";

          $weekday_counter++;
          if($weekday_counter > 7){
            echo "</tr>";
            $weekday_counter = 1;
          }
          $day_counter++;
        } // End Day

        // Finishing out any hanging weeks with empty cells
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
  }

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