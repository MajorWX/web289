<?php 
require_once('../../private/initialize.php'); 

require_login();

$vendor_id = h($_GET['id']);

// Making sure there is a get value for the id
if(!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('index.php'));
}

// Getting the user object
$vendor = Vendor::find_by_id($vendor_id);

// If the vendor object hasn't been made, redirect
if(!$vendor) {
  $session->message('Could not find a vendor with a vendor_id of ' . $id);
  redirect_to(url_for('/pending_vendors/list.php'));
}

$vendor->is_pending = 0;

$result = $vendor->save();

if($result) {
  $session->message("Approved vendor: " . $vendor->vendor_display_name);
  redirect_to(url_for('/pending_vendors/list.php'));
} else {
  // Display errors
}
