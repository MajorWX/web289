<?php require_once('../../private/initialize.php'); ?>
<?php
require_admin_login();

$product_id = h($_GET['id']);

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


// Delete the product
$result = $product->delete();

if($result) {
  $session->message('Deleted ' . $product->product_name . ' successfully.');
  redirect_to(url_for('products.php'));
} else {
    // Display errors
}