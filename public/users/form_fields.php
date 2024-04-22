<?php
//prevents this code from being loaded directly in the browser
// or without first setting the necessary object
if (!isset($user)) {
  redirect_to(url_for('index.php'));
}
?>

<dt>Display Name</dt>
<dd><input type="text" name="user[display_name]" value="<?php echo h($user->display_name); ?>" required></dd>


<dt>Email</dt>
<dd><input type="text" name="user[email]" value="<?php echo h($user->email); ?>" required></dd>