<?php

class CalendarDate extends DatabaseObject {

  static protected $table_name = 'calendar';
  static protected $db_columns = ['calendar_id', 'date'];

  public $calendar_id;
  public $date;
  public $listed_vendors = array();

  // SQL
  // SELECT date, vendor_id
  // FROM calendar c, calender_listing li, vendors v
  // WHERE c.calendar_id = li.li_calendar_id
  //   AND li.li_vendor_id = v.vendor_id
  //   AND (after day)

}

?>