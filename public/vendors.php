<?php require_once('../private/initialize.php');

// Fetching all vendors
$vendors = Vendor::list_all();

// Checking if the session is an admin to load the CRUD features later in the page
$is_admin_view = $session->is_admin_logged_in();

?>


<?php $page_title = 'Vendors'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


<main id="vendor">
  <h2>Vendors</h2>

  <h3>Search Vendors</h3>
  <form>
    <input type="text" name="vendor-search" id="vendor-search" list="vendor-suggestions">
    <datalist id="vendor-suggestions">
      <?php
      foreach ($vendors as $vendor) {
        echo '<option value="' . $vendor->vendor_display_name . '"></option>';
      }
      ?>
    </datalist>
    <input type="submit" value="Search">
  </form>

  <h3>Full Vendor List</h3>

  <?php

  ?>

  <table>
    <tr>
      <th>Vendor Display Name</th>
      <th>&nbsp;</th>
      <?php 
        // Admin specific vendor CRUD columns
        if($is_admin_view) { 
          echo "<th>&nbsp;</th>";
          echo "<th>&nbsp;</th>";
        }
      ?>
    </tr>
    <?php
    foreach ($vendors as $vendor) {
      echo "<tr>";
      echo "<td>" . $vendor->vendor_display_name . "</td>";
      echo '<td><a href="' . url_for('/vendors/show.php?id=' . $vendor->vendor_id) . '">View Details</a></td>';

      // Admin specific vendor CRUD links
      if($is_admin_view) {
        echo '<td><a href="' . url_for('/vendors/edit.php?id=' . $vendor->vendor_id) . '">Edit</a></td>';
        echo '<td><a href="' . url_for('/vendors/delete.php?id=' . $vendor->vendor_id) . '">Delete</a></td>';
      }

      echo "</tr>";
    }
    ?>
  </table>
</main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>