<?php
//prevents this code from being loaded directly in the browser
// or without first setting the necessary object
if (!isset($user)) {
  redirect_to(url_for('index.php'));
}
?>

<dt>Display Name</dt>
<dd>
  <label for="display-name">Display Name: </label>
  <input type="text" id="display-name" name="user[display_name]" value="<?php echo h($user->display_name); ?>" required>
</dd>


<dt>Email</dt>
<dd>
  <label for="email">Email: </label>
  <input type="text" id="email" name="user[email]" value="<?php echo h($user->email); ?>" required>
</dd>