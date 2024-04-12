<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Products'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<?php 
  // Getting all products
  $products = Product::find_all_products();

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
  
  // OLD METHOD OF FILTERING that was very SQL query heavy
  // foreach($sorted_product_array as $category_name => $products){
    
  //   foreach($products as $product) {

  //     $product->populate_listings();
  //     $product->filter_listings_by_date($next_market_day);
  //   }
  // }

  // Final Query count, N+3 
?>

<!-- Begin HTML -->


  <main id="product">
    <h2>Products</h2>
    <h3>Search Products</h3>
    <form>
      <label for="product-category">Product Category: </label>
      <select id="product-category" name="product-category">
        <!-- Populate each <option></option> with php-->
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
        <!-- Populate each <option></option> with php-->
          <?php 
            Product::create_datalist($sorted_product_array);
          ?>
      </datalist>
      <input type="submit" value="Search">
    </form>

    <h3>Full Products List</h3>
    <?php 
      
      Product::create_product_list($sorted_product_array);
    ?>

  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>