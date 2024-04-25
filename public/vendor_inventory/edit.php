<?php require_once('../../private/initialize.php'); ?>
<?php

$vendor_id = h($_GET['id']);

// Making sure there is a get value for the id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if ($vendor_id != $session->active_vendor_id && !$session->is_admin_logged_in()) {
  $session->message('You must be logged in as this vendor to edit them.');
  redirect_to(url_for('index.php'));
}

// Creating the vendor object
$vendor = Vendor::find_by_id($vendor_id);

// If the vendor object hasn't been made, redirect
if (!$vendor) {
  $session->message('Could not find a vendor with vendor_id of ' . $vendor_id);
  redirect_to(url_for('index.php'));
}

// Getting all the VendorInventory product listings for this vendor
$vendor->populate_inventory();

// Writing all product ids to a list
$valid_product_ids = [];
foreach ($vendor->vendor_inventory as $inventory_listing) {
  $valid_product_ids[] = $inventory_listing->inv_product_id;
}

// Fetching all images
$vendor_images = Image::find_by_vendor($vendor_id);

if ($vendor_images) {
  $inventory_images = Image::filter_by_purpose($vendor_images, "inventory");

  // Sorting the images for the vendor_inventory by their product id
  if ($inventory_images) {
    $inventory_images_by_product_id = [];
    foreach ($inventory_images as $image) {
      $inventory_images_by_product_id[$image->im_product_id] = $image;
    }
  }
}


// Checking for Post Request
if (is_post_request()) {
  // Get the changes and deletions
  if ($vendor->vendor_inventory) {
    // Getting the form's post values
    $form_values = $_POST['inventory'];

    // Checking for any image deletions
    // First checking if there are any images to be deleted
    if ($inventory_images && isset($_POST['delete_image'])) {
      // Making an empty array to store the deletion results
      $result_array_image_del = [];

      // Retrieving the product ids of images marked for deletion
      $product_ids = array_keys($_POST['delete_image']);

      // Going through each one and deleting the image
      foreach ($product_ids as $product_id) {
        // Checking that the product id lines up with an image
        if (array_key_exists($product_id, $inventory_images_by_product_id)) {
          // Deleting the image and storing the result in the deletion results array
          $result_array_image_del[] = $inventory_images_by_product_id[$product_id]->delete();
        }
      }

      // Making sure none of the image deletions returned false
      $image_deletion_result = !in_array(false, $result_array_image_del);
    } else {
      $image_deletion_result = true;
    }

    // Checking for image uploads
    if (count($_FILES) > 0) {
      // Getting the files
      $new_images = $_FILES;

      // Making an empty array to store the upload results
      $result_array_image_upload = [];

      // Going through each of the files to be uploaded and uploading them
      foreach ($new_images as $product_key => $new_image_file) {
        // Making sure the uploaded image correlates to a valid product id and that there actually is a file
        if (in_array($product_key, $valid_product_ids) && strlen($new_image_file['name']) > 0) {
          // Creating the new listing image and setting its attributes
          $new_listing_image = new Image;
          $new_listing_image->im_user_id = $session->get_user_id();
          $new_listing_image->im_vendor_id = $vendor->vendor_id;
          $new_listing_image->im_product_id = $product_key;
          $new_listing_image->image_purpose = "inventory";

          // Uploading the image
          $result_array_image_upload[] = $new_listing_image->upload($new_image_file);

          // Merging the errors
          array_push($vendor->errors, ...$new_listing_image->errors);
        }
      }

      // Making sure none of the image uploads returned false
      $image_upload_result = !in_array(false, $result_array_image_upload);
    } else {
      $image_upload_result = true;
    }

    // Getting the changes
    $changed_listings = VendorInventory::get_listing_changes($vendor->vendor_inventory, $form_values);

    // If there are changes, start updating
    if ($changed_listings) {
      $result_array = VendorInventory::update_changes($changed_listings, $vendor);
      $change_result = !in_array(false, $result_array);
    } else {
      $change_result = true;
    }

    // Getting the deletions
    $listings_to_delete = VendorInventory::get_listing_deletions($vendor->vendor_inventory, $form_values);

    // If there are deletions, start deleting them
    if ($listings_to_delete) {
      $result_array = VendorInventory::delete_changes($listings_to_delete, $inventory_images_by_product_id ?? []);
      $delete_result = !in_array(false, $result_array);
    } else {
      $delete_result = true;
    }
    
  } else {
    $change_result = true;
    $delete_result = true;
    $image_deletion_result = true;
    $image_upload_result = true;
  }

  if ($change_result && $delete_result && $image_deletion_result && $image_upload_result) {
    $session->message('Modified inventory listings for "' . $vendor->vendor_display_name . '" successfully.');
    redirect_to(url_for('/vendors/user_view.php?id=' . $vendor_id));
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
  <a href="<?php echo url_for('/vendors/user_view.php?id=' . h(u($vendor_id))); ?>">Back to Vendor User view</a>

  <h2>Edit Inventory Listings For <?php echo $vendor->vendor_display_name ?></h2>

  <?php echo display_errors($vendor->errors); ?>

  <p>Rules for image uploading: </p>
  <ul>
    <li>Can only have one image per product.</li>
    <li>8 MB maximum file size.</li>
    <li>Square images work best.</li>
    <li>Will be scaled to fit.</li>
  </ul>

  <form action="<?php echo url_for('/vendor_inventory/edit.php?id=' . h(u($vendor_id))); ?>" method="post" enctype="multipart/form-data">
    <?php
    // The table for editing existing listings
    if ($vendor->vendor_inventory) {
      $sorted_inventory_array = VendorInventory::sort_into_categories($vendor->vendor_inventory);

      if ($sorted_inventory_array) {
        echo "<h3>Existing Listings</h3>";
        VendorInventory::create_edit_vendor_inventory_table($sorted_inventory_array, $inventory_images_by_product_id ?? []);
      }
    }
    ?>

    <input type="submit" value="Submit Changes">
  </form>

</main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
