<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Vendors'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


  <main>
    <h2>Vendors</h2>

    <h3>Search Vendors</h3>

    <h3>Full Vendor List</h3>
    <ul>
      <?php
        $vendors = Vendor::list_all();

        foreach($vendors as $vendor){
          echo "<li>" . $vendor->vendor_display_name . " ";
          echo '<a href="' . url_for('/vendors/show.php?id=' . $vendor->vendor_id) . '">View Details</a>';
        }

      ?>
    </ul>
  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
