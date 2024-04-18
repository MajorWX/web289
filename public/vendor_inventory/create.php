<?php require_once('../../private/initialize.php'); ?>
<?php

$id = h($_GET['id']);

// Making sure there is a get value for the id
if (!isset($_GET['id'])) {
  $session->message('Failed to load page, no vendor_id provided.');
  redirect_to(url_for('index.php'));
}

// Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
if ($id != $session->active_vendor_id && !$session->is_admin_logged_in()) {
  $session->message('You must be logged in as this vendor to edit them.');
  redirect_to(url_for('index.php'));
}

// Creating the vendor object
$vendor = Vendor::find_by_id($id);

// If the vendor object hasn't been made, redirect
if (!$vendor) {
  $session->message('Could not find a vendor with vendor_id of ' . $id);
  redirect_to(url_for('index.php'));
}

// Populating the products
$all_products = Product::find_all_products();
$sorted_product_array = Product::sort_into_categories($all_products);
$sorted_by_name = Product::sort_by_product_name($all_products);
$categories_by_id = Product::find_all_categories();

// Creating the empty VendorInventory
$new_listing = new VendorInventory;
$new_listing->vendor = $vendor;
$new_listing->inv_vendor_id = $vendor->vendor_id;

// Checking for Post Request
if (is_post_request()) {
  
  // Creating the product listing.
  $form_listing = $_POST['listing'];
  $new_listing->listing_price = $form_listing['listing_price'];
  $new_listing->in_stock = (array_key_exists('in_stock', $form_listing)) ? 1 : 0; 

  // Getting the product from the form
  $form_product = $_POST['product'];
  $form_product_name = h(ucwords($form_product['product_name']));
  $form_product_category_id = h($form_product['category_id']);

  // Seeing if the product name already exists
  if(array_key_exists($form_product_name, $sorted_by_name)){
    // The product name DOES exist
    $existing_product = $sorted_by_name[$form_product_name];
    $new_listing->inv_product_id = $existing_product->product_id;
    $new_listing->product = $existing_product;
    
    // Checking if the category ids match between the existing product and the form's product
    if($form_product_category_id == $existing_product->prd_category_id) {
      // The category ids match, continue to storing the listing
    } else {
      // The category ids DO NOT match
      $new_listing->errors[] = "Invalid category for " . $form_product_name . ". Please set the category to " . $categories_by_id[$existing_product->prd_category_id] . ". If you suspect this is wrong, please contact us.";
    }

    // Storing the listing if there are no errors
    if(empty($new_listing->errors)) {
      $result = $new_listing->save();

      if($result) {
        $session->message("You've created a new product listing successfully.");
        redirect_to(url_for('/vendors/user_view.php?id=' . $id));
      } else {
        // Show Errors
      }
    }
  } // End code for the product name DOES exist
  
  else {
    // The product name DOES NOT EXIST
    
    // Create a new product object to store in the products table
    $new_listing->product = new Product;
    $new_listing->product->prd_category_id = $form_product_category_id;
    $new_listing->product->product_name = $form_product_name;
    $result = $new_listing->product->save();

    if($result) {
      // Stored product successfully

      $new_listing->product->product_id = $new_listing->product->id;
      $new_listing->inv_product_id = $new_listing->product->id;

      $result = $new_listing->save();
      if($result) {
        $session->message("You've created a new product listing successfully.");
        redirect_to(url_for('/vendors/user_view.php?id=' . $id));
      } else {
        // Error with storing the VendorInventory
      }

    } else {
      // error with storing the Product
    }
  }


} else {
  // Display the form
}

?>

<?php $page_title = 'Create New Inventory Listing'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main>
  <a href="<?php echo url_for('/vendors/user_view.php?id=' . h(u($id))); ?>">Back to Vendor User Page</a>

  <h2>Create a New Inventory Listing</h2>

  <?php echo display_errors($new_listing->errors); ?>

  <form action="<?php echo url_for('/vendor_inventory/create.php?id=' . h(u($id))); ?>" method="post">
    <dl>
      <dt>Product Name</dt>
      <dd>
        <input type="text" name="product[product_name]" value="<?php echo (isset($new_listing->product->product_name)) ? $new_listing->product->product_name : "" ; ?>" list="product-suggestions" required>
        <datalist id="product-suggestions">
          <!-- Populate each <option></option> with php-->
          <?php
            Product::create_datalist($sorted_product_array);
          ?>
        </datalist>
      </dd>

      <dt>Category</dt>
      <dd>
        <select name="product[category_id]" required>
          <option value="">Select a Category: </option>
          <?php Product::create_category_datalist(); ?>
        </select>
      </dd>

      <dt>Listing Price</dt>
      <dd>
        $<input type="number" name="listing[listing_price]" value="<?php echo (isset($new_listing->listing_price)) ? $new_listing->listing_price : 0.00; ?>" min="0" step="0.01" required>
      </dd>

      <dt>In Stock</dt>
      <dd>
        <input type="checkbox" name="listing[in_stock]" 
        <?php if(!isset($new_listing->in_stock)) { echo "checked"; 
        } else {echo ($new_listing->in_stock > 0) ? "checked": "" ; } ?>
        >
      </dd>
    </dl>

    <input type="submit" value="Create Listing">
  </form>
</main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
