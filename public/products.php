<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Products'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<?php 
  // Getting all products
  $products = Product::find_all_products();

  // PHP Filtering fall back
  if(is_post_request()) {
    // Filtering by the product name if it's set
    if(!is_blank($_POST['product-search'])) {
      $product_search_query = strtolower(trim($_POST['product-search']));
      $filtered_products = [];
      foreach($products as $product) {
        if(str_contains(strtolower($product->product_name), $product_search_query)) {$filtered_products[] = $product;}
      }
      $products = $filtered_products;
    }

    // Filtering by the category if it's set
    if(!is_blank($_POST['product-category'])) {
      $product_category_query = strtolower(trim($_POST['product-category']));
      $filtered_products = [];
      foreach($products as $product) {
        if(strtolower($product->category_name) == $product_category_query) {$filtered_products[] = $product;}
      }
      $products = $filtered_products;
    }
    
  }


  // Getting the next market day
  $next_market_day = CalendarDate::get_next_market_day();

  // Fallback if there is no next market day listed
  if(!$next_market_day){
    // Sorting the products into categories
    $sorted_product_array = Product::sort_into_categories($products);
  } else {
    // Populating the number of listings for each product
    $populated_products = Product::populate_listings_by_date($products, $next_market_day);
    // Sorting the products into categories
    $sorted_product_array = Product::sort_into_categories($populated_products);
  }
  // Getting the list of categories
  $category_list = Product::get_categories_list($sorted_product_array);
  
  // Getting all product images
  $product_images = Image::find_by_purpose('inventory');

  if($product_images) {
    // Storing the product images by product id
    $images_sorted_by_product_id = Image::sort_images_by_product_id($product_images);
    // Selecting one image per product to show
    $selected_images_by_product_id = Image::randomly_select_image_per_product($images_sorted_by_product_id);
  }
?>

<!-- Begin HTML -->
<?php 
  // If the user isn't an admin, use search_products.js
  if(!$session->is_admin_logged_in()) { ?>
    <script src="<?php echo url_for('/js/search_products.js');?>" defer></script>
    <?php
  } else { ?>
    <script src="<?php echo url_for('/js/search_products_admin.js');?>" defer></script>
    <?php
  }
?>

  <main id="product">
    <h2>Products</h2>

    <h3>Search Products</h3>
    <form action="<?php echo url_for('products.php'); ?>" method="post">
      <label for="product-category">Product Category: </label>
      <select id="product-category" name="product-category">
        <option value="">Select a category:</option>
        <?php 
          foreach($category_list as $category_name => $product_count){
            echo '<option value="' . $category_name . '">' . $category_name . ' (' . $product_count . ')</option>';
          }
        ?>
      </select>

      <label for="product-search">Search Term: </label>
      <input type="text" name="product-search" id="product-search" list="product-suggestions">
      <datalist id="product-suggestions">
          <?php 
            Product::create_datalist($sorted_product_array);
          ?>
      </datalist>
      <input type="submit" value="Search">
    </form>

    <h3>Full Products List</h3>
    <?php 
      
      if($session->is_admin_logged_in()) { 
        // Admin View ?>
        <a href="<?php echo url_for('/products/create_category.php'); ?>" class="edit-button">Create New Product Category</a>
        <a href="<?php echo url_for('/products/create.php') ; ?>" class="create-button">Create a New Product</a>
        <?php
        Product::create_admin_crud_table($sorted_product_array, $selected_images_by_product_id ?? []);
      } else {
        // Public view
        Product::create_product_list($sorted_product_array, $selected_images_by_product_id ?? []);
      }
      
    ?>

  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
