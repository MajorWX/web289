<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Vendors'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<?php 
  $vendors = Vendor::list_all();
?>

<!-- Begin HTML -->


  <main id="vendor">
    <h2>Vendors</h2>

    <h3>Search Vendors</h3>
    <form>
      <input type="text" name="vendor-search" id="vendor-search" list="vendor-suggestions">
      <datalist id="vendor-suggestions">
        <?php 
          foreach($vendors as $vendor){
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
      </tr>
      <?php
        foreach($vendors as $vendor){
          echo "<tr>";
          echo "<td>" . $vendor->vendor_display_name . "</td>";
          echo '<td><a href="' . url_for('/vendors/show.php?id=' . $vendor->vendor_id) . '">View Details</a></td>';
          echo "</tr>";
        }
      ?>
    </table>
  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
