<?php require_once('../../private/initialize.php'); ?>

<?php 

$vendor_id = $_GET['id'];

// Making sure there is a get value for the id
if(!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('vendors.php'));
}
// Creating the vendor object
$vendor = Vendor::populate_full($vendor_id);

// If the vendor object hasn't been made, redirect
if(!$vendor) {
  $session->message('Could not find a vendor with vendor_id of ' . $vendor_id);
  redirect_to(url_for('vendors.php'));
}

// Fetching all images
$vendor_images = Image::find_by_vendor($vendor_id);
if($vendor_images) {
  $profile_images = Image::filter_by_purpose($vendor_images, "profile");
  $inventory_images = Image::filter_by_purpose($vendor_images, "inventory");

  // Sorting the images for the vendor_inventory by their product id
  if($inventory_images) {
    $inventory_images_by_product_id = [];
    foreach($inventory_images as $image) {
      $inventory_images_by_product_id[$image->im_product_id] = $image;
    }
  }
}


?>


<?php $page_title = 'Vendor Details: ' . $vendor->vendor_display_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>



<!-- Begin HTML -->
  <main class="show">
    <a href="<?php echo url_for('vendors.php')?>">Back to Vendor List</a>

    <h2><?php echo $vendor->vendor_display_name; ?></h2>

    <?php 
      if($profile_images) {
        foreach($profile_images as $profile_image) {
          $profile_image->print_image(600, 400);
        }
      }
    ?>
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
      <dt>Phones</dt>
      
      <?php 
        foreach($vendor->phone_numbers as $phone){
          echo "<dd>" . ucwords($phone['phone_type']) . ": " . Vendor::phone_to_string($phone['phone_number']) . "</dd>";
        }
      ?>

      <dt>Vendor Inventory</dt>
      <dd>
        <?php 
        if($vendor->vendor_inventory) {
          $sorted_inventory_array = VendorInventory::sort_into_categories($vendor->vendor_inventory);
          VendorInventory::create_products_table($sorted_inventory_array, $inventory_images_by_product_id ?? []);
        }
        ?>
      </dd>

      <dt>Upcoming Market Days</dt>
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
