<?php require_once('../../private/initialize.php'); ?>

<?php 
require_login();

$vendor_id = h($_GET['id']);
$date = h($_GET['date']);

// Checking to make sure that a vendor id was provided
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('calendar.php'));
}

// Checking to make sure that a date was provided
if (!isset($_GET['date'])) {
  $session->message('Failed to load page, no date provided.');
  redirect_to(url_for('calendar.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if($vendor_id != $session->active_vendor_id && !$session->is_admin_logged_in()){
  $session->message("You do not have permission to delete calendar listings for that vendor.");
  redirect_to(url_for('calendar.php'));
}

// Fetching the CalendarDate object
$calendarDate = CalendarDate::find_by_date($date);

// If there is no calendar date object 
if(!$calendarDate){
  $session->message($date . " is not currently a valid date.");
  redirect_to(url_for('calendar.php'));
}

// Fetch the calendar_listing listing_id
$listing_id = $calendarDate->find_listing_id($vendor_id);

// If there are issues retrieving listing_id
if(!$listing_id){
  $session->message("We could not find a listing that includes this vendor on this date: "  . $date . " " . $calendarDate->calendar_id);
  // echo "delete.php listing_id: " . $listing_id;
  redirect_to(url_for('calendar.php'));
}

// Deleting the listing
$result = $calendarDate->delete_listing($listing_id);

if($result === true){
  $session->message("You have retracted your availability for: " . $date . " successfully.");
  redirect_to(url_for('calendar.php'));
} else {
  // Show Errors
}