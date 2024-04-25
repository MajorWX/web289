<?php require_once('../../private/initialize.php');

require_admin_login();

$image_id = h($_GET['id']);

// Making sure there is a get value for the id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no image_id provided.');
  redirect_to(url_for('/images/list.php'));
}
// Finding the image
$image = Image::find_by_id($image_id);

// If the image object hasn't been made, redirect
if (!$image) {
  $session->message('Could not find an image with image_id of ' . $image_id);
  redirect_to(url_for('/images/list.php'));
}

// Checking for post request
if (is_post_request()) {
  $args = $_POST['image'];
  $image->merge_attributes($image);
  $result = $image->save();

  if ($result) {
    $session->message("Edited the image with the id of " . $image_id . " successfully.");
    redirect_to(url_for('/images/show.php?id=' . $image_id));
  } else {
    // Show errors
  }  
} else {
  // Display the form
}

?>

<?php $page_title = 'Edit Image'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main>
  <a href="<?php echo url_for('/images/list.php'); ?>">Back to Image list</a>

  <h2>Edit Image</h2>

  <?php $image->print_image(600, 600, false); ?>

  <form action="<?php echo url_for('/images/edit.php?id=' . $image_id); ?>" method="post">
    <label>Image Purpose</label>
    <input type="text" name="image[image_purpose]" value="<?php echo $image->image_purpose ?? ''; ?>"><br>

    <input type="submit" value="Edit Image Data">
  </form>

</main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
