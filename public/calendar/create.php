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
if($vendor_id != $session->active_vendor_id && !$session->is_admin_logged_in()) {
  $session->message("You do not have permission to edit attendance for that vendor.");
  redirect_to(url_for('calendar.php'));
}

// Checking to make sure only non-pending vendors can access this page, unless they are an admin
if($session->is_pending && !$session->is_admin_logged_in()) {
  $session->message("You must be an approved vendor to sign up for market days.");
  redirect_to(url_for('calendar.php'));
}

// Fetching the CalendarDate object
$calendarDate = CalendarDate::find_by_date($date);

// If there is no calendar date object 
if(!$calendarDate){
  $session->message($date . " is not currently a valid date.");
  redirect_to(url_for('calendar.php'));
}

// Checking to make sure vendors cannot sign up for the current month, unless they are an admin
if($calendarDate->is_current_month() && !$session->is_admin_logged_in()) {
  $session->message("Sorry, it is too late to sign up for market days this month, please contact us via email if you think this is a mistake.");
  redirect_to(url_for('calendar.php'));
}

// Creating a new listing
$result = $calendarDate->create_new_listing($vendor_id);

if($result === true){
  $session->message("You have marked your availability for: " . $date . " successfully.");
  redirect_to(url_for('calendar.php'));
} else {
  // Show Errors
  echo "Could not mark you as available for " . $date . ".";
}
