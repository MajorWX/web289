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
  $session->message('You must be logged in as this vendor to view them as themselves.');
  redirect_to(url_for('login.php'));
}

// Creating the vendor object
$vendor = Vendor::populate_full($vendor_id);

// If the vendor object hasn't been made, redirect
if (!$vendor) {
  $session->message('Could not find a vendor with vendor_id of ' . $vendor_id);
  redirect_to(url_for('index.php'));
}

// Fetching all images
$vendor_images = Image::find_by_vendor($vendor_id);

if ($vendor_images) {
  $profile_images = Image::filter_by_purpose($vendor_images, "profile");
  $inventory_images = Image::filter_by_purpose($vendor_images, "inventory");

  // Sorting the images for the vendor_inventory by their product id
  if ($inventory_images) {
    $inventory_images_by_product_id = [];
    foreach ($inventory_images as $image) {
      $inventory_images_by_product_id[$image->im_product_id] = $image;
    }
  }
}


?>


<?php $page_title = 'Vendor Account Management: ' . $vendor->vendor_display_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main class="show">


  <h2><?php echo $vendor->vendor_display_name; ?></h2>

  <?php
  if (isset($profile_images)) {
    foreach ($profile_images as $profile_image) {
      $profile_image->print_image(600, 400);
    }
  }
  ?>

  <a href="<?php echo url_for('/vendors/edit.php?id=' . h(u($vendor_id))); ?>" class="edit-button">Edit Vendor Profile</a>

  <dl>
    <dt>Vendor Display Name</dt>
    <dd><?php echo $vendor->vendor_display_name; ?></dd>
    <dt>Description</dt>
    <dd><?php echo (!is_blank($vendor->vendor_desc)) ? $vendor->vendor_desc : 'This vendor does not currently have a description.'; ?></dd>
    <dt>Address</dt>
    <dd><?php echo $vendor->address; ?></dd>
    <dt>City</dt>
    <dd><?php echo $vendor->city; ?></dd>
    <dt>State</dt>
    <dd><?php echo $vendor->state; ?></dd>
    <dt>Zip Code</dt>
    <dd><?php echo $vendor->zip; ?></dd>

    <dt>Phones</dt>

    <?php
    if (count($vendor->phone_numbers) > 0) {
      foreach ($vendor->phone_numbers as $phone) {
        echo "<dd>" . ucwords($phone['phone_type']) . ": " . Vendor::phone_to_string($phone['phone_number']) . "</dd>";
      }
    } else {
      echo '<dd>This vendor does not currently have any listed phone numbers.</dd>';
    }

    ?>

    <dt>Vendor Inventory</dt>
    <dd>
      <?php
      if ($vendor->vendor_inventory) {
        $sorted_inventory_array = VendorInventory::sort_into_categories($vendor->vendor_inventory);

        if ($sorted_inventory_array) {
          VendorInventory::create_products_table($sorted_inventory_array, $inventory_images_by_product_id ?? []);
        }
      } else {
        echo '<p>This vendor does not currently have any listed inventory.</p>';
      }
      ?>
    </dd>
    <?php if ($vendor->vendor_inventory) { ?>
      <a href="<?php echo url_for('/vendor_inventory/edit.php?id=' . h(u($vendor_id))); ?>" class="edit-button">Edit Your Existing Product Listings</a>
    <?php
    } ?>

    <a href="<?php echo url_for('/vendor_inventory/create.php?id=' . h(u($vendor_id))); ?>" class="create-button">Create a New Product Listing</a>


    <dt>Upcoming Market Days</dt>
    <p class="sign-up-warning"><?php echo date('l, F jS', CalendarDate::last_day_in_this_month()); ?>, is the last day to sign up for all <?php echo CalendarDate::next_month_name(); ?> market days.</p>
    <a href="<?php echo url_for('calendar.php'); ?>">Jump to Calendar</a>
    <dd>
      <ul>
        <?php
        if (count($vendor->listed_dates) > 0) {
          foreach ($vendor->listed_dates as $listed_date) {
            echo "<li>" . $listed_date->print_date() . "</li>";
          }
        } else {
          echo '<p>This vendor has not currently marked themselves as attending any market days.</p>';
        }

        ?>
      </ul>
    </dd>
  </dl>

  <a href="<?php echo url_for('/vendors/delete.php?id=' . $vendor_id); ?>" class="delete-button">Delete Vendor Account</a>
</main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
