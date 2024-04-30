<?php require_once('../../private/initialize.php');

$user_id = h($_GET['id']);

// Making sure there is a get value for the user id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no user_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if ($user_id != $session->get_user_id() && !$session->is_admin_logged_in()) {
  $session->message('You must be logged in as this user to edit them.');
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
    $session->message('You must be logged in as a super admin to edit user profiles of super admins.');
    redirect_to(url_for('/users/list.php'));
  }
}
// Making sure that admin profiles can only be edited by themselves and super admins
elseif ($user->role == 'a') {
  if ($user_id != $session->get_user_id() && !$session->is_super_admin_logged_in()) {
    $session->message('You must be logged in as a super admin to edit user profiles of other admins.');
    redirect_to(url_for('/users/list.php'));
  }
}

// Checking for Post Request
if(is_post_request()) {
  $args = $_POST['user'];
  $user->merge_attributes($args);
  $result = $user->save();
  if($result) {
    $session->display_name = $_SESSION['display_name'] = $user->display_name;
    $session->message("Edited User profile for " . $user->display_name . " successfully.");
    redirect_to(url_for('/users/show.php?id=' . $user->user_id));
  } else {
    // Show errors
  }


} else {
  // Show the form as normal
}

?>
<?php $page_title = 'Edit User: ' . h($user->display_name); ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->

<main>
  <?php // Show a back link to the user list if the user is an admin
  if ($session->is_admin_logged_in()) { ?>
    <a href="<?php echo url_for('/users/list.php'); ?>">Back to User List</a><br><br>
  <?php
  } ?>
  <a href="<?php echo url_for('/users/show.php?id=' . $user_id); ?>">Back to User Profile</a>

  <h2>Edit User: <?php echo $user->display_name; ?></h2>

  <?php echo display_errors($user->errors); ?>

  <form action="<?php echo url_for('/users/edit.php?id=' . $user->user_id); ?>" method="post">

    <dl>
    <?php include('form_fields.php'); ?>

    <?php
    if ($session->is_super_admin_logged_in()) { ?>
      <dt>User Role<dt>
      <dd>
        <label for="user-role">User Role: </label>
        <select id="user-role" name="user[role]" required>
          <option value="">Select a Role: </option>
          <option value="m" <?php echo ($user->role == 'm') ? 'selected' : ''; ?>>User</option>
          <option value="a" <?php echo ($user->role == 'a') ? 'selected' : ''; ?>>Admin</option>
          <option value="s" <?php echo ($user->role == 's') ? 'selected' : ''; ?>>Super Admin</option>
        </select>
      </dd>
    <?php
    }
    ?>
    </dl>

    <input type="submit" value="Edit User">
  </form>

</main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>