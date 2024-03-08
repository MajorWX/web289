<?php require_once('../../private/initialize.php'); ?>
<?php $page_title = 'Vendor Application List'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


  <main>
    Stub
    <a href="<?php echo url_for('/pending_vendors/create.php') ?>">Create</a>
    <a href="<?php echo url_for('/pending_vendors/show.php') ?>">Read</a>
    <a href="<?php echo url_for('/pending_vendors/edit.php') ?>">Update</a>
    <a href="<?php echo url_for('/pending_vendors/delete.php') ?>">Delete</a>
  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>