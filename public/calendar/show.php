<?php require_once('../../private/initialize.php'); ?>
<?php

$date = h($_GET['date']);


// Making sure the date already exists

$this_date = CalendarDate::find_by_date($date);

if (!$this_date) {
  $is_market_date = false;
} else {
  $is_market_date = true;
  // Populates the vendor list for this CalendarDate object
  $this_date->populate_this_vendor_list();

  // Gets all vendors
  $vendors = Vendor::list_all();

  $vendor_id = false;
  if($session->has_vendor() && !$session->is_pending) {
    $vendor_id = $session->active_vendor_id;
  }
}
?>

<?php $page_title = 'View date: ' . $date; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main>
  <a href="<?php echo url_for('calendar.php') ?>">Back to Calendar</a>

  <h2>View Date <?php echo $date; ?></h2>

  <?php 
    if(!$is_market_date) {
      echo '<p>' . CalendarDate::print_date_from_string($date) . ' is not currently listed as a market day.</p>';
      if($session->is_admin_logged_in()) { ?>
        <a class="create-button" href="<?php url_for('/calendar/create_day.php?date=' . $date); ?>">Add day as Market Day</a>
        <?php
      }
    } else {
      echo '<p>' . CalendarDate::print_date_from_string($date)  . ' is listed as a market day.</p>';
      if($session->is_admin_logged_in()) { ?>
        <a class="delete-button" href="<?php url_for('/calendar/delete_day.php?date=' . $date); ?>">Remove day as Market Day</a>
        <a class="edit-button" href="<?php url_for('/calendar/edit_listings.php?date=' . $date); ?>">Edit Listings</a>
        <?php
      }
      // If there are existing vendors listed
      if(count($this_date->listed_vendors) > 0) { ?>
        <h3>Currently Listed Vendors</h3>
        <table>
          <tr>
            <th>Vendor Display Name</th>
            <th>&nbsp;</th>
            <?php if($session->is_admin_logged_in()) { ?>
              <th>&nbsp;</th>
              <?php
            } ?>
          </tr>
          <?php 
          $all_vendor_ids = [];
            foreach($this_date->listed_vendors as $vendor_id => $vendor_display_name) {
              $all_vendor_ids[] = $vendor_id;
              echo '<tr>';
              echo '<td>' . $vendor_display_name . '</td>';
              echo '<td><a href="' . url_for('/vendors/show.php?id=' . $vendor_id) . '">View Details</a></td>';
              if($session->is_admin_logged_in()) { 
                echo '<td><a href="' . url_for('/calendar/delete.php?id=' . $vendor_id . '&date=' . $this_date->date) . '">Remove this Vendor</a></td>';
              }
              echo "</tr>";
            }
          ?>
        </table>
            <?php 
              // If the vendor is logged in
              if($vendor_id) {
                if(in_array($vendor_id, $all_vendor_ids)) {
                  echo '<a class="delete-button" href="' . url_for('/calendar/delete.php?id=' . $vendor_id . '&date=' . $date) . '">Retract availability for this day.</a>';
                } else {
                  echo '<a class="create-button" href="' . url_for('/calendar/create.php?id=' . $vendor_id . '&date=' . $date) . '">Sign up for this day.</a>';
                }
              }
            ?>



      <?php } // End existing vendors
      else {
        echo "<p>There are no currently listed vendors.</p>";
        if($vendor_id) {
          echo '<a class="create-button" href="' . url_for('/calendar/create.php?id=' . $vendor_id . '&date=' . $date) . '">Sign up for this day.</a>';
        }
      }
    }
  ?>

<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>
