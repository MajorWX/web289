<?php require_once('../../private/initialize.php');

$vendor_id = $_GET['id'];

// Making sure there is a get value for the id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if ($vendor_id != $session->active_vendor_id && !$session->is_admin_logged_in()) {
  $session->message('You must be logged in as this vendor to delete them.');
  redirect_to(url_for('index.php'));
}

// Find the vendor object
$vendor = Vendor::find_by_id($vendor_id);

// If the vendor object hasn't been made, redirect
if (!$vendor) {
  $session->message('Could not find a vendor with a vendor_id of ' . $vendor_id);
  redirect_to(url_for('index.php'));
}

// Checking for post request
if (is_post_request()) {
  $result - $vendor->delete();
  if ($result) {
    $session->message("Deleted vendor: " . $vendor->vendor_display_name . " successfully.");
    if ($session->is_admin_logged_in()) {
      redirect_to(url_for('vendors.php'));
    } else {
      $session->no_application();
      redirect_to(url_for('index.php'));
    }
  }
} else {
  // Display the form
}

?>
<?php $page_title = 'Delete Vendor ' . $vendor->vendor_display_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->

<main>

  <?php
  // Link to the vendor's user_view if they are currently logged in.
  if ($vendor_id == $session->active_vendor_id) { ?>
    <a href="<?php echo url_for('/vendors/user_view.php?id=' . $vendor_id); ?>">Back to Viewing your Vendor Profile</a>
  <?php
  } // Link to the public profile if they are not.
  else { ?>
    <a href="<?php echo url_for('/vendors/show.php?id=' . $vendor_id); ?>">Back to Viewing Vendor Details</a>
  <?php
  }
  ?>

  <h2>Delete Vendor: <?php echo $vendor->vendor_display_name; ?></h2>

  <p>Are you sure you want to delete <?php echo $vendor->vendor_display_name; ?>'s vendor account? This action can't be undone.</p>
  <p>This will also delete any phone numbers, inventory listings, and calendar listings associated with this vendor.</p>

  <form action="<?php echo url_for('/vendors/delete.php?id=' . $vendor_id); ?>" method="post">
    <label for="confirm-deletion">Yes, I'm Sure</label>
    <input id="confirm-deletion" type="checkbox" required><br>
    <input type="submit" value="Delete Vendor">
  </form>

</main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
