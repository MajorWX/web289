<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Reynolds Hill Farmers Market <?php if (isset($page_title)) {
    echo ' - ' . h($page_title);
  } ?></title>
  <link rel="stylesheet" href="<?php echo url_for("/css/styles.css"); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div id="wrapper">
    <header>
      <nav>
        <a href="<?php echo url_for('/index.php') ?>">
          <h1>Reynolds Hill Farmers Market</h1>
        </a>
        <label for="nav-checkbox" id="nav-trigger">Menu</label>
        <input type="checkbox" id="nav-checkbox">
        <ul>
          <li><a href="<?php echo url_for('/calendar.php') ?>">Calendar</a></li>
          <li><a href="<?php echo url_for('/vendors.php') ?>">Vendors</a></li>
          <li><a href="<?php echo url_for('/products.php') ?>">Products</a></li>

          <?php // What shows up when logged in
          if ($session->is_logged_in()) { ?> 
            <li id="logged-in">
              <a href="<?php echo url_for('/users/show.php?id=' . $session->get_user_id()); ?>"><span>User: <?php echo $session->display_name; ?></span></a>

              <?php // What shows up if user is an approved vendor
                if($session->has_vendor() && !$session->is_pending ){
                  echo '<a href="' . url_for('/vendors/user_view.php?id=' . $session->active_vendor_id) . '">' . $session->active_vendor_name . '</a>';
                }
              ?>

              <?php // What shows up if user has no listed vendor
                if(!$session->has_vendor()){
                  echo '<a href="' . url_for('/pending_vendors/create.php') . '">Apply to be a Vendor</a>';
                }
              ?>

              <?php // What shows up the user has applied to be a vendor, but isn't approved
                if($session->has_vendor() && $session->is_pending ){
                  echo '<a href="' . url_for('/pending_vendors/show.php?id=' . $session->active_vendor_id) . '">View your pending application</a>';
                }
              ?>
              <a href="<?php echo url_for('/logout.php'); ?>">Logout</a>
            </li>
          <?php } 
          //What shows up when logged out
          else { ?>
            <li><a href="<?php echo url_for('/signup.php') ?>">Become a Vendor</a></li>
            <li><a href="<?php echo url_for('/login.php') ?>">Login</a></li>
          <?php } ?>
        </ul>
        <?php // What shows up when logged in as an admin 
        if ($session->is_admin_logged_in()) {?>
          <ul>
            <li><a href="<?php echo url_for('/pending_vendors/list.php') ?>">Pending Vendors</a></li>
            <li><a href="<?php echo url_for('/users/list.php') ?>">Users List</a></li>
            <li><a href="<?php echo url_for('/images/list.php') ?>">Image List</a></li>
          </ul>
        <?php } ?>
      </nav>
    </header>

    <?php echo display_session_message(); ?>
