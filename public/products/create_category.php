<?php require_once('../../private/initialize.php'); 
require_admin_login();

$errors = [];
$category_name = '';

// Checking for Post Request
if (is_post_request()){

  // Storing the form data
  $category_name = $_POST['category_name'];

  // Making sure there isn't a category with the category name.
  $categories_by_id = Product::find_all_categories();

  if(in_array($category_name, $categories_by_id)){
    $errors[] = "'" . $category_name . "' is already the name of an existing category.";
  } else {
    $result = Product::create_category($category_name);
    if($result) {
      $session->message("You've created the product category: '" . $category_name . "' successfully.");
      redirect_to(url_for('products.php'));
    } else {
      // Show errors
    }
  }

} else {
  // Display the form
}
?>


<?php $page_title = 'Create a New Product Category'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main>
  <a href="<?php echo url_for('products.php'); ?>">Back to Products Page</a>

  <h2>Create a New Inventory Listing</h2>

  <?php echo display_errors($errors); ?>

  <form action="<?php echo url_for('/products/create_category.php'); ?>" method="post">
    <dl>
      <dt>Category Name</dt>
      <dd>
        <input type="text" name="category_name" vale="<?php echo (isset($category_name)) ? $category_name : '' ; ?>" required>
      </dd>
    </dl>

    <input type="submit" name="submit" value="Create Category" >
  </form>
</main>