<?php
require_once('../private/initialize.php');
require_login();

// Log out the member
$session->logout();

redirect_to(url_for('/login.php'));

?>
