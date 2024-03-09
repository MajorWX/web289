<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Calendar'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<?php 
  // Get the current date CURDATE()
  // Get the current month
  // Create an SQL DATE value that is the first of the current month
  // Get all calendar dates after the first of this month and store it in an array 
  // Generate the month as a table
  // Generate the week as a table row
  // Generate each day as a cell
  // 
  // Use javascript to generate a list 
?>
<!-- Begin HTML -->

  <main id="calendar">
    <h2>Calendar</h2>

    <?php
      $calendarDateArray = CalendarDate::find_all_dates();

      CalendarDate::create_calendar($calendarDateArray);
    ?>

  </main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
