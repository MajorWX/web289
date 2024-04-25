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

// Finding the details for the uploader and associated vendor and product
$user = User::find_by_id($image->im_user_id);

if (!empty($image->im_vendor_id)) {
  $vendor = Vendor::find_by_id($image->im_vendor_id);
} else {
  $vendor = false;
}

if (!empty($image->im_product_id)) {
  $product = Product::find_by_id($image->im_product_id);
} else {
  $product = false;
}
?>

<?php $page_title = 'Image Details'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->


<main>
  <a href="<?php echo url_for('/images/list.php'); ?>">Back to Image list</a>

  <h2>Images Details</h2>

  <a href="<?php echo url_for('/images/edit.php?id=' . $image_id); ?>" class="edit-button">Edit Image</a>

  <a href="<?php echo url_for('/images/delete.php?id=' . $image_id); ?>" class="delete-button">Delete Image</a>

  <dl>
    <dt>Image</dt>
    <dd><?php $image->print_image(600, 600, false); ?></dd>

    <dt>Image ID</dt>
    <dd><?php echo $image->image_id; ?></dd>

    <dt>Image Path</dt>
    <dd><?php echo "public" . Image::$public_image_path . $image->content; ?></dd>

    <dt>Uploader</dt>
    <dd><?php echo $user->display_name; ?></dd>

    <dt>Upload Date</dt>
    <dd><?php echo $image->upload_date; ?></dd>

    <dt>Listed Purpose</dt>
    <dd><?php echo $image->image_purpose; ?></dd>

    <?php // If the vendor exists, show it 
    if ($vendor) { ?>
      <dt>Associated Vendor</dt>
      <dd><?php echo $vendor->vendor_display_name; ?></dd>
    <?php
    }

    // If the product exists, show it
    if ($product) { ?>
      <dt>Associated Product</dt>
      <dd><?php echo $product->product_name; ?></dd>
    <?php
    }
    ?>
  </dl>
</main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>