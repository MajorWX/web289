<?php require_once('../../private/initialize.php'); ?>

<?php 
require_login();

$id = $_GET['id'];

$vendor = Vendor::populate_full($id);

?>

<?php $page_title = 'View Vendor Application: ' . $vendor->vendor_display_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


<main class="show">
    <a href="<?php echo url_for('index.php')?>">Back to Home Page</a>
    <dl>
      <dt>Vendor Display Name</dt>
      <dd><?php echo $vendor->vendor_display_name?></dd>
      <dt>Description</dt>
      <dd><?php echo $vendor->vendor_desc?></dd>
      <!-- <dt>Contact Info</dt>
      <dd><?php //echo $vendor->contact_info?></dd> -->
      <dt>Address</dt>
      <dd><?php echo $vendor->address?></dd>
      <dt>City</dt>
      <dd><?php echo $vendor->city?></dd>
      <dt>State</dt>
      <dd><?php echo $vendor->state?></dd>
  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>