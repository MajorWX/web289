<?php require_once('../private/initialize.php');

// Fetching home content
$home_content = read_home_content();
$about_section_content = $home_content['about_section_content'];
$address_content = $home_content['address_content'];
$market_hour_content = $home_content['market_hour_content'];

// Fetching all images
$home_page_images = Image::find_by_purpose('home_page');

// Handling Next Market Day
date_default_timezone_set('America/New_York');
$calendar_has_error = false;

// Attempting to get the next two market days
$next_market_day = CalendarDate::get_next_market_day();
$upcoming_market_day = CalendarDate::get_upcoming_market_day();

// Adding a new dummy upcoming market day if one does not exist
if(!$upcoming_market_day) {
  // If next market day does exist, there is exactly one date with vendors ahead and only one new dummy needs to be made
  // Otherwise two market days must be made
  if(!$next_market_day) {
    // Creating a new next date a week after today
    $new_market_date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 7, date("Y")));
    $next_market_day = CalendarDate::create_new_date($new_market_date);
    // Making sure the new CalendarDate object was created
    if(!$next_market_day) {
      $calendar_has_error = true;
    } else {
      // Adding the admin vendor as a listed vendor
      $result = $next_market_day->create_new_listing(1);
      if(!$result) {
        $calendar_has_error = true;
      }
    }
  }

  $market_day_exploded = $next_market_day->explode_this_date();
  // Creating a new upcoming date a week after the next market day
  $new_upcoming_date = date("Y-m-d", mktime(0, 0, 0, $market_day_exploded[1], $market_day_exploded[2] + 7, $market_day_exploded[0]));
  $upcoming_market_day = CalendarDate::create_new_date($new_upcoming_date);
  // Making sure the new CalendarDate object was created
  if(!$upcoming_market_day) {
    $calendar_has_error = true;
  } else {
    // Adding the admin vendor as a listed vendor
    $result = $upcoming_market_day->create_new_listing(1);
    if(!$result) {
      $calendar_has_error = true;
    }
  }
}

// Checks if the next market day is the current date, changing the language for the calendar section
$is_today = ($next_market_day) ? $next_market_day->is_today() : false;

$next_market_day->populate_this_vendor_list();
$upcoming_market_day->populate_this_vendor_list();

CalendarDate::randomly_select_featured_vendors([$next_market_day, $upcoming_market_day], 3);
?>
<?php $page_title = 'Home'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main id="home">
  <?php
  // Edit Button for Home Page Content if user is an admin
  if ($session->is_admin_logged_in()) { ?>
    <a href="<?php echo url_for('/home/edit.php') ?>" class="edit-button">Edit Home Page Content</a>
  <?php
  }
  ?>
  <h2>Home Page</h2>

  <?php
  if ($home_page_images) {
    echo '<div class="home-image-holder">';
    foreach ($home_page_images as $home_page_image) {
      echo '<div class="home-image">';
      $home_page_image->print_image(600, 400);
      echo '</div>';
    }
    echo '</div>';
  }
  ?>

  <section id="about">
    <div>
      <h3>About Reynolds Hill Farmers Market</h3>
      <?php print_as_paragraphs($about_section_content); ?>

    </div>
    <div>
      <h3 id="address">Address</h3>
      <?php print_as_lines($address_content); ?>

      <h3 id="market-hours">Market Hours</h3>
      <?php print_as_paragraphs($market_hour_content); ?>
    </div>
  </section>

  <section id="calendar-section">
    <div>
      <h3><?php echo ($is_today) ? "Today's" : "Next"; ?> Market</h3>
      <?php echo "<p>" . $next_market_day->print_date() . "</p>";
      echo "<p>Hours: " . $next_market_day->print_market_hours() . "</p>"; ?>
      <!-- Populate this list with php -->
      <h4>Featured Vendors</h4>
      <ul>
        <?php
        if(count($next_market_day->listed_vendors) > 0) {
          foreach ($next_market_day->listed_vendors as $vendor_id => $vendor) {
            echo '<li><a href="' . url_for('vendors/show.php?id=' . $vendor_id) . '">' . $vendor . '</a></li>';
          }
        }
        ?>
      </ul>
    </div>

    <div>
      <h3><?php echo ($is_today) ? "Next" : "Upcoming"; ?> Market</h3>
      <?php echo "<p>" . $upcoming_market_day->print_date() . "</p>";
      echo "<p>Hours: " . $upcoming_market_day->print_market_hours() . "</p>"; ?>
      <!-- Populate this list with php -->
      <h4>Featured Vendors</h4>
      <ul>
        <?php
        if(count($upcoming_market_day->listed_vendors) > 0) {
          foreach ($upcoming_market_day->listed_vendors as $vendor_id => $vendor) {
            echo '<li><a href="' . url_for('vendors/show.php?id=' . $vendor_id) . '">' . $vendor . '</a></li>';
          }
        }
        ?>
      </ul>
    </div>

    <div>
      <a href="<?php echo url_for('/calendar.php') ?>"><span>See Full Calendar</span></a>
    </div>
  </section>
</main>

<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>
