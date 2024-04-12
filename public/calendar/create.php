<?php require_once('../../private/initialize.php'); ?>

<?php 

$id = $_GET['id'];
$date = $_GET['date'];

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if($id != $session->active_vendor_id && !$session->is_admin_logged_in()){
  $session->message("You do not have permission to edit attendance for that vendor.");
  redirect_to(url_for('calendar.php'));
}

// Fetching the CalendarDate object
$calendarDate = CalendarDate::find_by_date($date);

// If there is no calendar date object 
if(!$calendarDate){
  $session->message($date . " is not currently a valid date.");
  redirect_to(url_for('calendar.php'));
}

// POPULATE VENDOR LISTINGS!
// To check if the vendor is already part of the date

//Create the listing

$result = $calendarDate->create_new_listing($id);

if($result === true){
  $session->message("You have marked your availability for: " . $date . " successfully.");
  redirect_to(url_for('calendar.php'));
} else {
  // Show Errors
}
