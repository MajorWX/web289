<?php require_once('../../private/initialize.php'); ?>

<?php 

$date = h($_GET['date']);

// Checking to make sure only admins can create new market dates
if(!$session->is_admin_logged_in()){
  $session->message("You do not have permission to edit this market day.");
  redirect_to(url_for('calendar.php'));
}

// Making sure the date already exists
$this_date = CalendarDate::find_by_date($date);
if(!$this_date){
  $session->message($date . " is not an existing date.");
  redirect_to(url_for('calendar.php'));
}

// Populates the vendor list for this CalendarDate object
$this_date->populate_this_vendor_list();

// Gets all vendors
$vendors = Vendor::list_all();
?>

<?php $page_title = 'Edit Listings for ' . $date; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

  <main>
    <a href="<?php echo url_for('calendar.php')?>">Back to Calendar</a>
    <h2>Edit Listings for <?php echo $date; ?></h2>
    
    <?php 
      // If there are existing vendors listed
      if(count($this_date->listed_vendors) > 0) { ?>
        <h3>Currently Listed Vendors</h3>
        <table>
          <tr>
            <th>Vendor Display Name</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <?php 
            foreach($this_date->listed_vendors as $vendor_id => $vendor_display_name){
              echo '<tr>';
              echo '<td>' . $vendor_display_name . '</td>';
              echo '<td><a href="' . url_for('/vendors/show.php?id=' . $vendor_id) . '">View Details</a></td>';
              echo '<td><a href="' . url_for('/calendar/delete.php?id=' . $vendor_id . '&date=' . $this_date->date) . '">Remove this Vendor</a></td>';
              echo "</tr>";
            }
          ?>
        </table>
      <?php } // End existing vendors ?>

      <h3>Full Vendor List</h3>

      <table>
      <tr>
        <th>Vendor Display Name</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
      <?php
        foreach($vendors as $vendor){
          if(!in_array($vendor->vendor_id, array_keys($this_date->listed_vendors))){
            echo "<tr>";
            echo "<td>" . $vendor->vendor_display_name . "</td>";
            echo '<td><a href="' . url_for('/vendors/show.php?id=' . $vendor->vendor_id) . '">View Details</a></td>';
            echo '<td><a href="' . url_for('/calendar/create.php?id=' . $vendor->vendor_id . '&date=' . $this_date->date) . '">Add this Vendor</a></td>';
            echo "</tr>";
          }
        }
      ?>
    </table>
    

  </main>
<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>
