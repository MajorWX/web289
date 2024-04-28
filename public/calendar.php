<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Calendar'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>



<!-- Begin HTML -->

<main id="calendar">
  <h2>Calendar</h2>

  <?php
  // Public view
  if (!$session->has_vendor()) {
    // Only show dates with vendors
    $calendarDateArray = CalendarDate::find_all_dates_with_vendors();
  }
  // User view
  else {
    // Show all dates, even empty ones
    $calendarDateArray = CalendarDate::find_all_dates_empty();
    $calendarDateArray = CalendarDate::populate_vendor_list($calendarDateArray);
  }


  // Debugging
  // foreach($calendarDateArray as $calendarDate){
  //   echo $calendarDate->date . " ";
  //   echo count($calendarDate->listed_vendors) . " ";
  //   echo "<br>";
  // }

  CalendarDate::create_calendar($calendarDateArray);
  ?>


</main>

<div id="outer">
  <div id="inner">

  </div>
</div>

<?php
// User view javascript
if ($session->has_vendor() && !$session->is_pending) { ?>
  <script src="<?php echo url_for('/js/vendor_calendar.js'); ?>" defer></script>
<?php } ?>

<?php
// Admin view javascript
if ($session->is_admin_logged_in()) { ?>
  <script src="<?php echo url_for('/js/admin_calendar.js'); ?>" defer></script>
<?php } ?>

<?php 
  // Responsiveness popup javascript
  ?>
<script src="<?php echo url_for('/js/calendar_responsive.js'); ?>" defer></script>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>