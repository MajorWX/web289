<?php require_once('../../private/initialize.php'); ?>

<?php

$product_id = $_GET['id'];

// Making sure there is a get value for the id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no product_id provided.');
  redirect_to(url_for('products.php'));
}

// Finding the product using id
$product = Product::find_by_id($product_id);

// If the product object hasn't been made, redirect
if (!$product) {
  $session->message('Could not find a product with a product_id of ' . $product_id);
  redirect_to(url_for('products.php'));
}

// Finding the next market day to filter listings
$next_market_day = CalendarDate::get_next_market_day();

// Getting all listings for this product
$product->populate_listings();

// If there was a found next_market day, filter listings by that date
if ($next_market_day) {
  $product->filter_listings_by_date($next_market_day);
}

// Getting all product images
$product_images = Image::find_by_product($product_id);

if ($product_images) {
  // Storing the product images by product id
  $images_sorted_by_product_id = Image::sort_images_by_product_id($product_images);
  // Selecting three images to display
  $selected_images_by_product_id = Image::randomly_select_image_per_product($images_sorted_by_product_id, 3);
  $selected_images =  $selected_images_by_product_id[$product_id];
}

?>

<?php $page_title = 'Product Listings: ' . $product->product_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->
<main class="show">
  <a href="<?php echo url_for('products.php') ?>">Back to Product List</a>

  <h2>Product Listings: <?php echo $product->product_name; ?></h2>

  <?php
  if ($selected_images) {
    // Maybe add a div around the images for styling
    foreach ($selected_images as $image) {
      $image->print_image(400, 400);
    }
  }
  ?>

  <dl>
    <dt>Product Name</dt>
    <dd><?php echo $product->product_name; ?></dd>
    <dt>Category</dt>
    <dd><?php echo $product->category_name; ?></dd>
    <dt>Vendor Listings</dt>
    <?php
    if ($product->inventory_listings) {
      VendorInventory::create_vendor_table($product->inventory_listings);
    } else {
      echo "We couldn't find any vendors listed as carrying " . $product->product_name . " on the next market day: " . $next_market_day->date . ".";
    }
    ?>
  </dl>
</main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>