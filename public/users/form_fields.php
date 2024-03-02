<!-- </?php
// prevents this code from being loaded directly in the browser
// or without first setting the necessary object
if(!isset($user)) {
  redirect_to(url_for('/users/index.php'));
}
?/> -->

<dl>
  <dt>Display Name</dt>
  <dd><input type="text" name="user[display_name]" value="<?php echo h($user->display_name); ?>" /></dd>
</dl>

<dl>
  <dt>Email</dt>
  <dd><input type="text" name="user[email]" value="<?php echo h($user->email); ?>" /></dd>
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
  <dd><input type="password" name="user[password]" value="" /></dd>
</dl>

<dl>
  <dt>Confirm Password</dt>
  <dd><input type="password" name="user[confirm_password]" value="" /></dd>
</dl>
