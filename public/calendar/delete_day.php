<?php require_once('../../private/initialize.php'); ?>

<?php 

$date = h($_GET['date']);

// Making sure there is a get value for the date
if (!isset($_GET['date'])) {
  $session->message('Failed to load page, no date provided.');
  redirect_to(url_for('calendar.php'));
}

// Checking to make sure only admins can create new market dates
if(!$session->is_admin_logged_in()){
  $session->message("You do not have permission to delete market days.");
  redirect_to(url_for('calendar.php'));
}

// Making sure the date already exists
$old_date = CalendarDate::find_by_date($date);
if(!$old_date){
  $session->message($date . " is not an existing date.");
  redirect_to(url_for('calendar.php'));
}

// Attempting to delete the date
$result = $old_date->delete();

if($result == true){
  $session->message("You have successfully removed: " . $date . " as a market day.");
  redirect_to(url_for('calendar.php'));
} else {
  // Show Errors
  echo "Something went wrong";
}