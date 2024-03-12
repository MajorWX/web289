<?php 
require_once('../../private/initialize.php'); 

require_login();

if(is_post_request()) {

  // Create record using post parameters
  $args = $_POST['vendor'];
  $vendor = new Vendor($args);
  $vendor->vd_user_id = $session->get_user_id();
  $vendor->is_pending = 1;
  $result = $vendor->save();

  if($result === true) {
    $new_vendor_id = $vendor->vendor_id;

    $session->active_vendor_id = $_SESSION['active_vendor_id'] = $new_vendor_id;
    $session->active_vendor_name = $_SESSION['active_vendor_name'] = $vendor->vendor_display_name;
    $session->is_pending = $_SESSION['is_pending'] = $vendor->is_pending;

    $session->message(`You've submitted your vendor form successfully.`);
    redirect_to(url_for('/index.php'));
  }
} else {
  // display the form
  $vendor = new Vendor;
}



?>


<?php $page_title = 'Create Vendor Application'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


  <main>
    <h2>Vendor Application</h2>
    <?php echo display_errors($vendor->errors); ?>

    <form action="<?php echo url_for('/pending_vendors/create.php');?>" method="post">
      
      <?php include('../vendors/form_fields.php'); ?>

      <div id="operations">
        <input type="submit" value="Submit Application" />
      </div>
    </form>

  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>