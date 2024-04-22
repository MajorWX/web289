<?php require_once('../../private/initialize.php');

$user_id = h($_GET['id']);

// Making sure there is a get value for the user id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no user_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if($user_id != $session->get_user_id() && !$session->is_admin_logged_in()){
  $session->message('You must be logged in as this user to view them.');
  redirect_to(url_for('login.php'));
}

// Getting the user object
$user = User::find_by_id($user_id);

// If the user object couldn't be found, redirect
if(!$user){
  $session->message('Could not find a user with user_id of ' . $user_id);
  redirect_to(url_for('index.php'));
}

// Find vendor associated with this account
$associated_vendor = Vendor::find_by_user_id($user_id);


?>
<?php $page_title = 'User Details for ' . h($user->display_name); ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


  <main>
    <?php // Show a back link to the user list if the user is an admin
      if($session->is_admin_logged_in()) { ?>
        <a href="<?php echo url_for('/users/list.php'); ?>">Back to User List</a><br><br>
        <?php
      }

      // Show a back link to the home page if the user is viewing their own profile, even if they are an admin
      if($user_id == $session->get_user_id()) { ?>
        <a href="<?php echo url_for('index.php'); ?>">Back to Home Page</a>
        <?php
      }
    ?>

    <h2><?php echo 'User Details for ' . $user->display_name; ?></h2>
    <a href="<?php echo url_for('/users/edit.php?id=' . $user->user_id); ?>" class="edit-button">Edit User Profile</a>

    <a href="<?php echo url_for('/users/edit.php?id=' . $user->user_id); ?>" class="delete-button">Delete User Profile</a>

    <dl>
      <dt>User ID</dt>
      <dd><?php echo $user->user_id; ?></dd>
      <dt>Display Name</dt>
      <dd><?php echo $user->display_name; ?></dd>
      <dt>Email</dt>
      <dd><?php echo $user->email; ?></dd>
      <dt>Role</dt>
      <dd><?php echo $user->role_to_string(); ?></dd>
      <?php 
        // If an associated vendor was found
        if($associated_vendor) { ?>
          <dt>Associated Vendor</dt>
          <dd><?php echo $associated_vendor->vendor_display_name; ?></dd>
          <dd>
            <a href="<?php echo url_for('/vendors/show.php?id=' . $associated_vendor->vendor_id)?>">View Vendor Details</a>
          </dd>
          <?php
        }
      ?>
    </dl>
  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
