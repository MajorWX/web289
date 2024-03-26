<?php require_once('../../private/initialize.php'); ?>
<?php 
  // Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
  if($id != $session->active_vendor_id && !$session->is_admin_logged_in()){
    $session->message('You must be logged in as this vendor to edit them.');
    redirect_to(url_for('index.php'));
  }
?>

<?php $page_title = 'Edit Vendor'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


  <main>
    Stub

  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>