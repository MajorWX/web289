<?php require_once('../../private/initialize.php'); ?>

<?php 

$product_id = $_GET['id'];

$product = Product::find_by_id($product_id);
$product->populate_listings();
$next_market_day = CalendarDate::get_next_market_day();
$product->filter_listings_by_date($next_market_day);

?>

<?php $page_title = 'Product Listings: ' . $product->product_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->
  <main class="show">
    <a href="<?php echo url_for('products.php')?>">Back to Product List</a>
    <dl>
      <dt>Product Name</dt>
      <dd><?php echo $product->product_name; ?></dd>
      <dt>Category</dt>
      <dd><?php echo $product->category_name; ?></dd>
      <dt>Vendor Listings</dt>
      <dd>
        <?php 
          VendorInventory::create_vendor_table($product->inventory_listings);
        ?>
      </dd>
    </dl>
  </main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
