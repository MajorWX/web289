<?php require_once('../../private/initialize.php'); 

require_admin_login();
?>


<?php $page_title = 'User List'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


  <main>
    Stub
    <a href="<?php echo url_for('/users/show.php') ?>">Read</a>
    <a href="<?php echo url_for('/users/edit.php') ?>">Update</a>
    <a href="<?php echo url_for('/users/delete.php') ?>">Delete</a>
  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>