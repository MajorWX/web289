<?php require_once('../../private/initialize.php');
require_admin_login();

$pending_vendors = Vendor::find_all_pending();
?>


<?php $page_title = 'Vendor Application List'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


<main>
  <h2>Pending Vendor List</h2>
  <?php if ($pending_vendors) { ?>
    <table>
      <tr>
        <th>Vendor Display Name</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
      <?php
      foreach ($pending_vendors as $vendor) {
        echo "<tr>";
        echo "<td>" . $vendor->vendor_display_name . "</td>";
        echo '<td><a href="' . url_for('/pending_vendors/show.php?id=' . $vendor->vendor_id) . '">View</a></td>';
        echo '<td><a href="' . url_for('/pending_vendors/approve.php?id=' . $vendor->vendor_id) . '">Approve</a></td>';
        echo '<td><a href="' . url_for('/pending_vendors/edit.php?id=' . $vendor->vendor_id) . '">Edit</a></td>';
        echo '<td><a href="' . url_for('/pending_vendors/delete.php?id=' . $vendor->vendor_id) . '">Delete</a></td>';
        echo "</tr>";
      }
      ?>

    </table>
  <?php } else {
    echo "<p>There are currently no pending vendors.</p>";
  } ?>
</main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>