<?php require_once('../../private/initialize.php');
require_login();

$vendor_id = $_GET['id'];

// Making sure there is a get value for the id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if ($vendor_id != $session->active_vendor_id && !$session->is_admin_logged_in()) {
  $session->message('You must be logged in as this vendor to edit them.');
  redirect_to(url_for('index.php'));
}

$vendor = Vendor::find_by_id($vendor_id);

// If the vendor object hasn't been made, redirect
if (!$vendor) {
  $session->message('Could not find a vendor with a vendor_id of ' . $vendor_id);
  redirect_to(url_for('index.php'));
}

if (is_post_request()) {
  $args = $_POST['vendor'];
  $vendor->merge_attributes($args);
  $result = $vendor->save();

  if ($result) { 
    $session->message("Edited application for " . $vendor->vendor_display_name . " successfully.");
    redirect_to(url_for('/pending_vendors/show.php?id=' . $vendor_id));
  } else {
    // Show errors
  }
} else {
  // Display the form
}


?>
<?php $page_title = 'Edit Vendor Application'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>



<!-- Begin HTML -->


<main>
  <h2>Edit Vendor Application</h2>

  <?php
  if ($session->is_admin_logged_in()) { ?>
    <a href="<?php echo url_for('/pending_vendors/list.php') ?>">Back to Pending Vendor List</a><br>
  <?php
  }
  ?>
  <a href="<?php echo url_for('/pending_vendors/show.php?id=' . $vendor_id); ?>">Back to Viewing Vendor Application</a>

  <?php echo display_errors($vendor->errors); ?>

  <form action="<?php echo url_for('/pending_vendors/edit.php?id=' . $vendor_id); ?>" method="post">

    <?php include('../vendors/form_fields.php'); ?>

    <input type="submit" value="Edit Application" />
  </form>

</main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>