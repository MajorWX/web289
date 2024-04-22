<?php require_once('../../private/initialize.php');

$user_id = h($_GET['id']);

// Making sure there is a get value for the user id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no user_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if ($user_id != $session->get_user_id() && !$session->is_admin_logged_in()) {
  $session->message('You must be logged in as this user to delete them.');
  redirect_to(url_for('login.php'));
}

// Getting the user object
$user = User::find_by_id($user_id);

// If the user object couldn't be found, redirect
if (!$user) {
  $session->message('Could not find a user with user_id of ' . $user_id);
  redirect_to(url_for('index.php'));
}

// Making sure that only super admins can edit the profiles of super admins
if ($user->role == 's') {
  if (!$session->is_super_admin_logged_in()) {
    $session->message('You must be logged in as a super admin to delete the user accounts of super admins.');
    redirect_to(url_for('/users/list.php'));
  }
}
// Making sure that admin profiles can only be edited by themselves and super admins
elseif ($user->role == 'a') {
  if ($user_id != $session->get_user_id() && !$session->is_super_admin_logged_in()) {
    $session->message('You must be logged in as a super admin to delete the user accounts of other admins.');
    redirect_to(url_for('/users/list.php'));
  }
}

// Find vendor associated with this account
$associated_vendor = Vendor::find_by_user_id($user_id);

// Checking for Post Request
if (is_post_request()) {
  $result = $user->delete();
  if ($result) {
    $session->message("Deleted user " . $user->display_name . " successfully.");
    if ($session->is_admin_logged_in()) {
      redirect_to(url_for('/users/list.php'));
    } else {
      $session->logout();
      redirect_to(url_for('index.php'));
    }
  } else {
    // Show errors
  }
} else {
  // Show the form as normal
}


?>
<?php $page_title = 'Delete User'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


<main>
  <?php // Show a back link to the user list if the user is an admin
  if ($session->is_admin_logged_in()) { ?>
    <a href="<?php echo url_for('/users/list.php'); ?>">Back to User List</a><br><br>
  <?php
  } ?>
  <a href="<?php echo url_for('/users/show.php?id=' . $user_id); ?>">Back to User Profile</a>

  <h2>Delete User: <?php echo $user->display_name; ?></h2>

  <p>Are you sure you want to delete the user: <?php echo $user->display_name; ?>? This action can't be undone.</p>

  <?php // Showing the vendor it will delete, if there is one.
  if ($associated_vendor) { ?>
    <p>This will also delete the vendor: <?php echo $associated_vendor->vendor_display_name; ?>.</p>
  <?php
  }
  ?>

  <form action="<?php echo url_for('/users/delete.php?id=' . $user->user_id); ?>" method="post">
    <label for="confirm-deletion">Yes, I'm Sure</label>
    <input id="confirm-deletion" type="checkbox" required><br>
    <input type="submit" value="Delete User">
  </form>

</main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>