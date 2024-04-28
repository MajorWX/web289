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


// Find the vendor using id
$vendor = Vendor::populate_full($vendor_id);

// If the vendor object hasn't been made, redirect
if (!$vendor) {
  $session->message('Could not find a vendor with a vendor_id of ' . $vendor_id);
  redirect_to(url_for('index.php'));
}

// Fetching all images
$vendor_images = Image::find_by_vendor($vendor_id);
$num_profile_images = 0;
if ($vendor_images) {
  $profile_images = Image::filter_by_purpose($vendor_images, "profile");
  $num_profile_images = count($profile_images);
}

// Checking for Post Request
if (is_post_request()) {

  // Create record using post parameters
  $args = $_POST['vendor'];
  $vendor->merge_attributes($args);
  $vendor_result = $vendor->save();

  // Deleting profile images marked for deletion, if there are any
  if (isset($_POST['delete_profile_image'])) {
    $image_ids_to_delete = $_POST['delete_profile_image'];
    $image_objects_to_delete = [];

    // Finding this vendor's profile images with matching ids
    foreach ($profile_images as $profile_image) {
      if (array_key_exists($profile_image->image_id, $image_ids_to_delete)) {
        $image_objects_to_delete[] = $profile_image;
      }
    }

    // Deleting all images in the array, if there are any
    $image_deletion_results = [];
    if (count($image_objects_to_delete) > 0) {
      foreach ($image_objects_to_delete as $image_object) {
        $image_deletion_results[] = $image_object->delete();
      }
      $image_deletion_result = !in_array(false, $image_deletion_results);
    } else {
      $image_deletion_result = true;
    }
  } else {
    $image_deletion_result = true;
  }

  // Uploading the new profile image, if it exists
  if (strlen($_FILES["profile_image"]["name"]) > 0) {
    // Creating the new profile image and setting its attributes
    $new_profile_image = new Image;
    $new_profile_image->im_user_id = $session->get_user_id();
    $new_profile_image->im_vendor_id = $vendor->vendor_id;
    $new_profile_image->image_purpose = "profile";

    // Uploading the image
    $new_image_result = $new_profile_image->upload($_FILES["profile_image"]);

    // Merging the errors
    array_push($vendor->errors, ...$new_profile_image->errors);
  } else {
    $new_image_result = true;
  }

  $result = $vendor_result && $new_image_result && $image_deletion_result;

  if ($result) {
    $session->message('Modified vendor: "' . $vendor->vendor_display_name . '" successfully.');
    redirect_to(url_for('/vendors/user_view.php?id=' . $vendor_id));
  } else {
    // show errors
  }
} else {
  // Display form
}




?>

<?php $page_title = 'Edit Vendor'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->
<script src="<?php echo url_for('/js/vendor_add_phones.js'); ?>" defer></script>

<main>
  <a href="<?php echo url_for('/vendors/user_view.php?id=' . h(u($vendor_id))); ?>">Back to Vendor User Page</a>

  <h2>Edit Vendor: <?php echo $vendor->vendor_display_name; ?></h2>

  <?php echo display_errors($vendor->errors); ?>


  <form action="<?php echo url_for('/vendors/edit.php?id=' . h(u($vendor_id))); ?>" method="post" enctype="multipart/form-data">

    <?php include('form_fields.php'); ?>

    <?php
    if (count($vendor->phone_numbers) > 0) { ?>

      <dl>
        <dt>Existing Phones</dt>
        <?php
        foreach ($vendor->phone_numbers as $phone_id => $phone_attributes) {
          // Phone Number
          echo '<dd>';
          echo '<label for="phone-' . $phone_id . '-number">Phone Number: </label>';
          echo '<input type="text" id="phone-' . $phone_id . '-number" name="vendor[phone_numbers][' . $phone_id . '][phone_number]' . '" value="' . Vendor::phone_to_string($phone_attributes['phone_number']) . '">';

          // Phone Type
          echo ' <label for="phone-' . $phone_id . '-type">Phone Type: </label>';
          echo '<select id="phone-' . $phone_id . '-type" name="vendor[phone_numbers][' . $phone_id . '][phone_type]">';
          echo '<option value="">Select a phone type:</option>';

          $phone_type = $phone_attributes['phone_type'];

          echo '<option value="home"';
          if ($phone_type == 'home') {
            echo ' selected';
          }
          echo '>Home</option>';

          echo '<option value="mobile"';
          if ($phone_type == 'mobile') {
            echo ' selected';
          }
          echo '>Mobile</option>';

          echo '<option value="work"';
          if ($phone_type == 'work') {
            echo ' selected';
          }
          echo '>Work</option>';

          echo '</select>';

          // Deletion
          echo ' <label for="phone-' . $phone_id . '-delete">Mark For Deletion: </label>';
          echo '<input type="checkbox" id="phone-' . $phone_id . '-delete" name="vendor[phone_numbers][' . $phone_id . '][delete]">';

          echo '</dd>';
        }
        ?>


      </dl>
    <?php
    }
    ?>

    <dl class="new-phones">
      <dt>Add New Phones</dt>
      <a>Click to add a phone.</a>
    </dl>

    <?php
    if (isset($profile_images)) { ?>
      <dl>
        <dt>Existing Profile Pictures</dt>
        <?php
        foreach ($profile_images as $profile_image) {
          $profile_image->print_image(600, 400);
          echo '<br>';
          echo '<label for="image-' . $profile_image->image_id . '-delete">Mark Above Image For Deletion: </label>';
          echo '<input type="checkbox" id="image-' . $profile_image->image_id . '-delete" name="delete_profile_image[' . $profile_image->image_id . ']">';
        }
        ?>
      </dl>
    <?php
    }
    ?>

    <?php
    if ($num_profile_images < 3) { ?>
      <dl>
        <dt>Upload A Profile Image</dt>
        <dd>Images that will display on your vendor profile page.</dd>
        <ul>
          <li>May only have 3 profile images.</li>
          <li>8 MB maximum file size.</li>
          <li>Ratio of 3 wide by 2 high works best.</li>
          <li>Will be scaled to fit.</li>
        </ul>
        <dd>
          <label for="profile-image"></label>
          <input type="file" id="profile-image" name="profile_image">
        </dd>
      </dl>
    <?php
    }
    ?>

    <input type="submit" value="Edit Vendor">
  </form>
</main>


<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>