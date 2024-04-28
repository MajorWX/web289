<?php require_once('../../private/initialize.php'); ?>
<?php
require_admin_login();


// Creating the new empty product
$new_product = new Product;

// Checking for Post Request
if (is_post_request()) {


  // Populating the products
  $all_products = Product::find_all_products();
  $sorted_by_name = Product::sort_by_product_name($all_products);

  // Getting the product from the form
  $form_product = $_POST['product'];
  $form_product['product_name'] = $form_product_name = h(ucwords($form_product['product_name']));
  $form_product['prd_category_id'] = $form_product_category_id = h($form_product['prd_category_id']);

  // Merging the attributes
  $new_product->merge_attributes($form_product);

  // Seeing if the product name already exists
  if (array_key_exists($form_product_name, $sorted_by_name)) {
    // The product name DOES exist
    $new_product->errors[] = "Product must have a unique product name: " . $form_product_name . " is already an existing product.";
  }

  
  
  // Storing the listing if there are no errors
  if(empty($new_product->errors)) {
    var_dump($new_product);
    $result = $new_product->save();
    var_dump($result);
    if($result) {
      $session->message("You've successfully created a new product: " . $new_product->product_name);
      redirect_to(url_for('products.php'));
    } else {
      // Errors with saving
      
    }
  } else {
    // Errors with the product
  }

} else {
  // Display the form
}

?>

<?php $page_title = 'Create New Product'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main>
  <a href="<?php echo url_for('products.php'); ?>">Back to Products Page</a>

  <h2>Create a New Product</h2>

  <?php echo display_errors($new_product->errors); ?>

  <form action="<?php echo url_for('/products/create.php'); ?>" method="post">
    <dl>
      <dt>Product Name (plural)</dt>
      <dd>
        <label for="product-name">Product Name (plural): </label>
        <input type="text" id="product-name" name="product[product_name]" value="<?php echo   $new_product->product_name; ?>" required>
      </dd>

      <dt>Category</dt>
      <dd>
        <labeL for="product-category">Category: </labeL>
        <select id="product-category" name="product[prd_category_id]" required>
          <option value="">Select a Category: </option>
          <?php Product::create_category_datalist_edit($new_product); ?>
        </select>
      </dd>
    </dl>

    <input type="submit" value="Create Product">
  </form>

</main>

<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>
