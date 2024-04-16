<?php require_once('../../private/initialize.php'); ?>
<?php 

  $id = h($_GET['id']);

  // Making sure there is a get value for the id
  if(!isset($_GET['id'])) {
    $session->message('Failed to load page, no vendor_id provided.');
    redirect_to(url_for('index.php'));
  }

  // Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
  if($id != $session->active_vendor_id && !$session->is_admin_logged_in()){
    $session->message('You must be logged in as this vendor to edit them.');
    redirect_to(url_for('index.php'));
  }

  // Creating the vendor object
  $vendor = Vendor::find_by_id($id);

  // If the vendor object hasn't been made, redirect
  if(!$vendor) {
    $session->message('Could not find a vendor with vendor_id of ' . $id);
    redirect_to(url_for('index.php'));
  }

  // Getting all the VendorInventory product listings for this vendor
  $vendor->populate_inventory();

  // Checking for Post Request
  if(is_post_request()) {

    // Get the changes and deletions
    if($vendor->vendor_inventory) {
      // Getting the form's post values
      $form_values = $_POST['inventory'];

      // Getting the changes
      $changed_listings = VendorInventory::get_listing_changes($vendor->vendor_inventory, $form_values);

      // If there are changes, start updating
      if($changed_listings) {
        $result_array = VendorInventory::update_changes($changed_listings, $vendor);
        $change_result = !in_array(false, $result_array);

      } else { $change_result = true; }

      // Getting the deletions
      $listings_to_delete = VendorInventory::get_listing_deletions($vendor->vendor_inventory, $form_values);

      // If there are deletions, start deletings
      if($listings_to_delete) {
        $result_array = VendorInventory::delete_changes($listings_to_delete);
        $delete_result = !in_array(false, $result_array);
      } else { $delete_result = true; }

    } else {
      $change_result = true; 
      $delete_result = true;
    }

    if($change_result && $delete_result) {
      $session->message('Modified inventory listings for "' . $vendor->vendor_display_name . '" successfully.');
      redirect_to(url_for('/vendors/user_view.php?id=' . $id));
    } else {
      // show errors
    }

    

  } else {
    // Display the form
  }

?>

<?php $page_title = 'Edit Inventory Listings'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

  <main>
    <a href="<?php echo url_for('/vendors/user_view.php?id=' . h(u($id)));?>">Back to Vendor User view</a>

    <h2>Edit Inventory Listings For <?php echo $vendor->vendor_display_name?></h2>

    <?php echo display_errors($vendor->errors); ?>

    <form action="<?php echo url_for('/vendor_inventory/edit.php?id=' . h(u($id))); ?>" method="post">
      <?php 
        // The table for editing existing listings
        if($vendor->vendor_inventory){
          $sorted_inventory_array = VendorInventory::sort_into_categories($vendor->vendor_inventory);

          if($sorted_inventory_array){
            echo "<h3>Existing Listings</h3>";
            VendorInventory::create_edit_vendor_inventory_table($sorted_inventory_array);
          }
        }          
      ?>

      <input type="submit" value="Submit Changes">
    </form>

  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
