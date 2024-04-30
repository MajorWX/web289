<?php require_once('../../private/initialize.php');
require_admin_login();

// Reading and storing information
$readFile = fopen("home-content.txt", "r");
$about_section_content = "";
$address_content = "";
$market_hour_content = "";
$contact_info_content = "";

// Reading the first line of the about section
$line = trim(fgets($readFile));
// Adding lines to the about section until the end of the file or until the address marker appears
while (!feof($readFile) && $line != '#Address') {
  $about_section_content .= $line . "\n";
  $line = trim(fgets($readFile));
}
// Trimming the last newline of the about section
$about_section_content = trim($about_section_content);


// Reading the first line of the Address section
$line = trim(fgets($readFile));
// Adding lines to the address until the end of the file or until the market hours marker appears
while (!feof($readFile) && $line != '#Market Hours') {
  $address_content .= $line . "\n";
  $line = trim(fgets($readFile));
}
// Trimming the last newline of the address section
$address_content = trim($address_content);


// Reading the first line of the Market Hours section
$line = trim(fgets($readFile));
// Adding lines to the market hours until the end of the file or until the contact info marker appears
while (!feof($readFile) && $line != '#Contact Info') {
  $market_hour_content .= $line . "\n";
  $line = trim(fgets($readFile));
}
// Trimming the last newline of the address section
$market_hour_content = trim($market_hour_content);


// Reading the first line of the contact info section
$line = trim(fgets($readFile));
// Adding lines to the contact infos until the end of the file
while (!feof($readFile)) {
  $contact_info_content .= $line . "\n";
  $line = trim(fgets($readFile));
}
// Trimming the last newline of the address section
$contact_info_content = trim($contact_info_content);

// Closing the file
fclose($readFile);


// Fetching all images
$home_page_images = Image::find_by_purpose('home_page');
$num_home_page_images = 0;
if ($home_page_images) {
  $num_home_page_images = count($home_page_images);
}

$errors = [];

if (is_post_request()) {
  // Writing to the file
  $writeFile = fopen("home-content.txt", "w");

  fwrite($writeFile, trim(h($_POST['about-section'] ?? "-")));

  fwrite($writeFile, "\n#Address\n");
  fwrite($writeFile, trim(h($_POST['address'] ?? "-")));

  fwrite($writeFile, "\n#Market Hours\n");
  fwrite($writeFile, trim(h($_POST['market-hours'] ?? "-")));

  fwrite($writeFile, "\n#Contact Info\n");
  fwrite($writeFile, trim(h($_POST['contact-info'] ?? "-")));

  // Closing the file
  fwrite($writeFile, "\n");
  fclose($writeFile);


  // Deleting profile images marked for deletion, if there are any
  if (isset($_POST['delete_home_image'])) {
    $image_ids_to_delete = $_POST['delete_home_image'];
    $image_objects_to_delete = [];

    // Finding this vendor's profile images with matching ids
    foreach ($home_page_images as $home_page_image) {
      if (array_key_exists($home_page_image->image_id, $image_ids_to_delete)) {
        $image_objects_to_delete[] = $home_page_image;
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

  // Uploading the new home page image, if it exists
  if (strlen($_FILES["home-page-image"]["name"]) > 0) {
    // Creating the new home page image and setting its attributes
    $new_home_image = new Image;
    $new_home_image->im_user_id = $session->get_user_id();
    $new_home_image->im_vendor_id = 0;
    $new_home_image->image_purpose = "home_page";

    // Uploading the image
    $new_image_result = $new_home_image->upload($_FILES["home-page-image"]);

    // Merging the errors
    array_push($errors, ...$new_home_image->errors);
  } else {
    $new_image_result = true;
  }

  if( $new_image_result && $image_deletion_result) {
    $session->message("Edited Home Page Content Successfully");
    redirect_to(url_for('index.php'));
  } else {
    // Show Errors
  }
} else {
  // Display the form
}

?>

<?php $page_title = 'Edit Home Page Content'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main>
  <h2>Edit Home Page Content</h2>

  <?php echo display_errors($errors); ?>

  <form action="<?php echo url_for('/home/edit.php'); ?>" method="post" enctype="multipart/form-data">
    <dl>
      <dt>About Section</dt>
      <dd>
        <label for="about-section">About Section: </label>
        <textarea id="about-section" name="about-section" rows="10" cols="120"><?php echo $about_section_content; ?></textarea>
      </dd>

      <dt>Address</dt>
      <dd>
        <label for="address">Address: </label>
        <textarea id="address" name="address" rows="3" cols="25"><?php echo $address_content; ?></textarea>
      </dd>

      <dt>Market Hours</dt>
      <p>Please edit the market hours in the CalendarDate class directly after changing anything here.</p>
      <dd>
        <label for="market-hours">Market Hours: </label>
        <textarea id="market-hours" name="market-hours" rows="5" cols="25"><?php echo $market_hour_content; ?></textarea>
      </dd>

      <dt>Contact Info</dt>
      <dd>
        <label for="contact-info">Contact Info: </label>
        <textarea id="contact-info" name="contact-info" rows="5" cols="25"><?php echo $contact_info_content; ?></textarea>
      </dd>

      <?php
      if ($home_page_images) { ?>
        <dl>
          <dt>Existing Home Page Images</dt>
          <?php
          foreach ($home_page_images as $home_page_image) {
            echo '<div>';
            $home_page_image->print_image(600, 400);
            echo '<br>';
            echo '<label for="image-' . $home_page_image->image_id . '-delete">Mark Above Image For Deletion: </label>';
            echo '<input type="checkbox" id="image-' . $home_page_image->image_id . '-delete" name="delete_home_image[' . $home_page_image->image_id . ']">';
            echo '</div>';
          }
          ?>
        </dl>
      <?php
      }
      ?>


      <?php
      if ($num_home_page_images < 3) { ?>
        <dt>Upload a Photo</dt>
        <dd>Images that will display on the home page.</dd>
        <ul>
          <li>May only have 3 home page images.</li>
          <li>8 MB maximum file size.</li>
          <li>Ratio of 3 wide by 2 high works best.</li>
          <li>Will be scaled to fit.</li>
        </ul>
        <dd>
          <label for="home-page-image"></label>
          <input type="file" id="home-page-image" name="home-page-image">
        </dd>
      <?php
      }
      ?>

    </dl>

    <input type="submit" value="Save Changes">
  </form>

</main>

<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>