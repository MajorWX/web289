<?php require_once('../../private/initialize.php'); ?>

<?php
require_login();

$vendor_id = h($_GET['id']);

// Making sure there is a get value for the id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if ($vendor_id != $session->active_vendor_id && !$session->is_admin_logged_in()) {
  $session->message('You must be logged in as this vendor to view them.');
  redirect_to(url_for('index.php'));
}

$vendor = Vendor::populate_full($vendor_id);

// If the vendor object hasn't been made, redirect
if (!$vendor) {
  $session->message('Could not find a vendor with a vendor_id of ' . $vendor_id);
  redirect_to(url_for('index.php'));
}


?>

<?php $page_title = 'View Vendor Application: ' . $vendor->vendor_display_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


<main class="show">
  <?php
  if ($session->is_admin_logged_in()) { ?>
    <a href="<?php echo url_for('/pending_vendors/list.php') ?>">Back to Pending Vendor List</a><br>
  <?php
  }
  ?>
  <a href="<?php echo url_for('index.php') ?>">Back to Home Page</a>
  <a href="<?php echo url_for('/pending_vendors/edit.php?id=' . $vendor_id); ?>" class="edit-button">Edit Vendor Application</a>
  <dl>
    <dt>Vendor Display Name</dt>
    <dd><?php echo $vendor->vendor_display_name ?></dd>
    <dt>Description</dt>
    <dd><?php echo $vendor->vendor_desc ?></dd>
    <!-- <dt>Contact Info</dt>
      <dd><?php //echo $vendor->contact_info
          ?></dd> -->
    <dt>Address</dt>
    <dd><?php echo $vendor->address ?></dd>
    <dt>City</dt>
    <dd><?php echo $vendor->city ?></dd>
    <dt>State</dt>
    <dd><?php echo $vendor->state ?></dd>
</main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>