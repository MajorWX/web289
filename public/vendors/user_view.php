<?php require_once('../../private/initialize.php'); ?>

<?php 

$vendor_id = h($_GET['id']);

// Making sure there is a get value for the id
if(!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if($vendor_id != $session->active_vendor_id && !$session->is_admin_logged_in()){
  $session->message('You must be logged in as this vendor to view them as themselves.');
  redirect_to(url_for('login.php'));
}

// Creating the vendor object
$vendor = Vendor::populate_full($vendor_id);

// If the vendor object hasn't been made, redirect
if(!$vendor) {
  $session->message('Could not find a vendor with vendor_id of ' . $vendor_id);
  redirect_to(url_for('index.php'));
}

// Fetching all images
$vendor_images = Image::find_by_vendor($vendor_id);
if($vendor_images) {
  $profile_images = Image::filter_by_purpose($vendor_images, "profile");
}


?>


<?php $page_title = 'Vendor Account Management: ' . $vendor->vendor_display_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main class="show">

    
    <h2><?php echo $vendor->vendor_display_name; ?></h2>

    <?php 
      if($profile_images) {
        foreach($profile_images as $profile_image) {
          $profile_image->print_image(600, 400);
        }
      }
    ?>

    <a href="<?php echo url_for('/vendors/edit.php?id=' . h(u($vendor_id)));?>" class="edit-button">Edit Vendor Profile</a>
    
    <dl>
      <dt>Vendor Display Name</dt>
      <dd><?php echo $vendor->vendor_display_name; ?></dd>
      <dt>Description</dt>
      <dd><?php echo $vendor->vendor_desc; ?></dd>
      <!-- <dt>Contact Info</dt>
      <dd><?php // echo $vendor->contact_info?></dd> -->
      <dt>Address</dt>
      <dd><?php echo $vendor->address; ?></dd>
      <dt>City</dt>
      <dd><?php echo $vendor->city; ?></dd>
      <dt>State</dt>
      <dd><?php echo $vendor->state; ?></dd>
      
      <dt>Phones</dt>
      
      <?php 
        foreach($vendor->phone_numbers as $phone){
          echo "<dd>" . ucwords($phone['phone_type']) . ": " . Vendor::phone_to_string($phone['phone_number']) . "</dd>";
          
        }
      ?>

      <dt>Vendor Inventory</dt>      
      <dd>
        <?php 
        if($vendor->vendor_inventory){
          $sorted_inventory_array = VendorInventory::sort_into_categories($vendor->vendor_inventory);

          if($sorted_inventory_array){
            VendorInventory::create_products_table($sorted_inventory_array);
          }
        }        
        ?>
      </dd>
      <a href="<?php echo url_for('/vendor_inventory/edit.php?id=' . h(u($vendor_id)));?>" class="edit-button">Edit Your Existing Product Listings</a>
      <a href="<?php echo url_for('/vendor_inventory/create.php?id=' . h(u($vendor_id)));?>" class="create-button">Create a New Product Listing</a>
      

      <dt>Upcoming Market Days</dt>
      <a href="<?php echo url_for('calendar.php'); ?>">Jump to Calendar</a>
      <dd><ul>
        <?php 
          foreach($vendor->listed_dates as $listed_date){
            echo "<li>" . $listed_date->print_date() . "</li>";
          }
        ?>
      </ul></dd>
    </dl>
  </main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
