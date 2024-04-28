<?php require_once('../../private/initialize.php');

require_admin_login();

// Finding all images
$images = Image::find_all();

// Finding all users, vendors, and products to minimize queries when referencing IDs
$users = User::find_all();
// $vendors = Vendor::list_all();
// $products = Product::find_all_products();

// Sorting user, vendors, and products into associative arrays with ids as keys
$users_by_id = [];
if ($users) {
  foreach ($users as $user) {
    $users_by_id[$user->user_id] = $user;
  }
}

// $vendors_by_id = [];
// if ($vendors) {
//   foreach ($vendors as $vendor) {
//     $vendors_by_id[$vendor->vendor_id] = $vendor;
//   }
// }

// $products_by_id = [];
// if ($products) {
//   foreach ($products as $product) {
//     $products_by_id[$product->product_id] = $product;
//   }
// }


?>

<?php $page_title = 'Image List'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main class="images">
  <h2>Images List</h2>

  <div style="overflow-x:auto;">
    <table>
      <tr>
        <th>&nbsp;</th>
        <th>Image ID</th>
        <th>Image Name</th>
        <th>Uploader</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>

      <?php

      foreach ($images as $image) {
        echo "<tr>";
        // Image itself
        echo "<td>";
        $image->print_image(200, 200);
        echo "</td>";
        // Image ID
        echo "<td>" . $image->image_id . "</td>";
        // Image Path
        echo "<td>" . $image->content . "</td>";
        // Uploader
        echo "<td>" . $users_by_id[$image->im_user_id]->display_name . "</td>";

        // View
        echo '<td><a href="' . url_for('/images/show.php?id=' . $image->image_id) . '">View</td>';
        // Edit
        echo '<td><a href="' . url_for('/images/edit.php?id=' . $image->image_id) . '">Edit</td>';
        // Delete
        echo '<td><a href="' . url_for('/images/delete.php?id=' . $image->image_id) . '">Delete</td>';
        echo "</tr>";
      }
      ?>

    </table>
  </div>


</main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>