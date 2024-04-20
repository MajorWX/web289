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

// Getting all listings for this product
$product->populate_listings();

// Checking for Post Request
if (is_post_request()) {
  // Post Request

  // Getting the product from the form
  $form_product = $_POST['product'];
  $form_product['product_name'] = $form_product_name = h(ucwords($form_product['product_name']));
  $form_product['prd_category_id'] = $form_product_category_id = h($form_product['category_id']);

  // Merging the attributes
  $product->merge_attributes($form_product);

  // Saving the changes to the product
  $result = $product->save();

  if($result) {
    $session->message('Modified "' . $product->product_name . '" successfully.');
    redirect_to(url_for('products.php'));
  } else {
    // Display errors
  }


} else {
  // Non Post Requests

  // Display form
}

?>

<?php $page_title = 'Edit Product: ' . $product->product_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main>
  <a href="<?php echo url_for('products.php'); ?>">Back to Products Page</a>

  <h2>Edit Product: <?php echo $product->product_name; ?></h2>

  <?php echo display_errors($product->errors); ?>

  <form action="<?php echo url_for('/products/edit.php?id=' . h(u($product_id))); ?>" method="post">
    <dl>
      <dt>Product Name</dt>
      <dd>
        <input type="text" name="product[product_name]" value="<?php echo $product->product_name; ?>" required>
      </dd>

      <dt>Category</dt>
      <dd>
        <select name="product[prd_category_id]" required>
          <option value="">Select a Category: </option>
          <?php Product::create_category_datalist_edit($product); ?>
        </select>
      </dd>
    </dl>

    <input type="submit" value="Edit Product">
  </form>

</main>

<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>