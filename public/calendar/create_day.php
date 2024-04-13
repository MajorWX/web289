<?php require_once('../../private/initialize.php'); ?>

<?php 

$date = h($_GET['date']);

// Checking to make sure only admins can create new market dates
if(!$session->is_admin_logged_in()){
  $session->message("You do not have permission to create market days.");
  redirect_to(url_for('calendar.php'));
}

// Making sure the date doesn't already exist
if(CalendarDate::find_by_date($date) != false){
  $session->message($date . " is already an existing date.");
  redirect_to(url_for('calendar.php'));
}

// Create the new market dates
$result = CalendarDate::create_new_date($date);

if($result == true){
  $session->message("You have created the market day: " . $date . " successfully.");
  redirect_to(url_for('calendar.php'));
} else {
  // Show Errors
}