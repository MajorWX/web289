<?php
require_once('../private/initialize.php');

$errors = [];
$display_name = '';
$password = '';

if(is_post_request()) {

  $display_name = $_POST['display_name'] ?? '';
  $password = $_POST['password'] ?? '';

  // Validations
  if(is_blank($display_name)) {
    $errors[] = "Display_name cannot be blank.";
  }
  if(is_blank($password)) {
    $errors[] = "Password cannot be blank.";
  }

  // if there were no errors, try to login
  if(empty($errors)) {
    $user = User::find_by_display_name($display_name);
    // test if user found and password is correct
    if($user != false && $user->verify_password($password)) {
      // Mark user as logged in
      $session->login($user);
      redirect_to(url_for('/index.php'));
    } else {
      // display_name not found or password does not match
      $errors[] = "Log in was unsuccessful.";
    }

  }

}

?>

<?php $page_title = 'Log in'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<main>
  <h2>Log in</h2>

  <?php echo display_errors($errors); ?>

  <form action="login.php" method="post">
    Display Name:<br>
    <input type="text" name="display_name" value="<?php echo h($display_name); ?>" required><br>
    Password:<br>
    <input type="password" name="password" value="" required><br>
    <input type="submit" name="submit" value="Submit" >
  </form>

</main>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
