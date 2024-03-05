<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Reynolds Hill Farmers Market <?php if(isset($page_title)) {echo ' - ' . h($page_title); }?></title>
  <!-- Change this link to use <a href="<~?php echo url_for('/index.php'); ?>" -->
  <link href="<?php echo url_for('/css/styles.css') ?>" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <header>
    <!-- Wrap this in an <a> to make this link back to home -->
    <a href="<?php echo url_for('/index.php')?>">
      <h1>Reynolds Hill Farmers Market</h1>
    </a>
    <!-- Change all these <a> links to use <a href="<~?php echo url_for('/index.php'); ?>" -->
    <nav>
      <ul>
        <li><a href="<?php echo url_for('/calendar.php')?>">Calendar</a></li>
        <li><a href="<?php echo url_for('/vendors.php')?>">Vendors</a></li>
        <li><a href="<?php echo url_for('/products.php')?>">Products</a></li>

        <!-- What shows up when logged in -->
        <?php if($session->is_logged_in()) {?>
          <li>User: <?php echo $session->display_name; ?></li>
          <li><a href="<?php echo url_for('/index.php'); ?>">Menu</a></li>
          <li><a href="<?php echo url_for('/logout.php'); ?>">Logout</a></li>
        <?php } else {?>
          <!-- What shows up when logged out -->
          <li><a href="<?php echo url_for('/login.php')?>">Login</a></li>
          <li><a href="<?php echo url_for('/signup.php')?>">Register</a></li>

        <?php } ?>
      </ul>
    </nav>
  </header>

  <?php echo display_session_message(); ?>
