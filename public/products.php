<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Products'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<?php 
  $products = Product::find_all_products();
  $sorted_product_array = Product::sort_into_categories($products);
  $category_list = Product::get_categories_list($sorted_product_array);
  $next_market_day = CalendarDate::get_next_market_day();
  foreach($sorted_product_array as $category_name => $products){
    foreach($products as $product) {
      $product->populate_listings();
      $product->filter_listings_by_date($next_market_day);
    }
  }
?>

<!-- Begin HTML -->


  <main>
    <h2>Products</h2>
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

    <h3>All Products</h3>
    <?php 
      
      Product::create_product_list($sorted_product_array);
    ?>

  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>