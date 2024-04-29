<?php

require_once('../private/initialize.php');

if (is_post_request()) {

  // Create record using post parameters
  $args = $_POST['user'];
  $user = new User($args);
  $user->role = 'm';
  $user->set_hashed_password();
  $result = $user->save();

  if ($result === true) {
    $session->message(`You've signed up successfully.`);
    $session->login($user);
    redirect_to(url_for('/index.php'));
  } else {
    // show errors
  }
} else {
  // display the form
  $user = new User;
}

?>

<?php $page_title = 'Sign Up for Membership'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<main>
  <div class="user new">
    <h2>Become a Vendor</h2>
    <p>If you wish to become a vendor, please first create a user account using the form below. You will then be able to submit a vendor application for admin approval.</p>


    <h3>Create User Account</h3>

    <?php echo display_errors($user->errors); ?>
      
    <form action="<?php echo url_for('signup.php'); ?>" method="post">

      <dl>
        <?php include('users/form_fields.php'); ?>
      </dl>
      <dl>
        <dt>Password</dt>
        <ul>
          <li>must contain 8 or more characters</li>
          <li>must contain at least 1 uppercase letter</li>
          <li>must contain at least 1 lowercase letter</li>
          <li>must contain at least 1 number</li>
          <li>must contain at least 1 symbol</li>
        </ul>
        <dd>
          <label for="password">Password: </label>
          <input type="password" id="password" name="user[password]" value="" required>
        </dd>
      </dl>

      <dl>
        <dt>Confirm Password</dt>
        <dd>
          <label for="confirm-password">Confirm Password: </label>
          <input type="password" id="confirm-password" name="user[confirm_password]" value="" required>
        </dd>
      </dl>

      <div id="operations">
        <input type="submit" value="Sign Up">
      </div>
    </form>

  </div>

</main>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
