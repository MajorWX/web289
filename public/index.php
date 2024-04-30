<?php require_once('../private/initialize.php');

// Fetching home content
$home_content = read_home_content();
$about_section_content = $home_content['about_section_content'];
$address_content = $home_content['address_content'];
$market_hour_content = $home_content['market_hour_content'];

// Fetching all images
$home_page_images = Image::find_by_purpose('home_page');

// Handling Next Market Day
$next_market_day = CalendarDate::get_next_market_day();
$upcoming_market_day = CalendarDate::get_upcoming_market_day();

$is_today = $next_market_day->is_today();

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
    foreach ($home_page_images as $home_page_image) {
      $home_page_image->print_image(600, 400);
    }
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
        foreach ($next_market_day->listed_vendors as $vendor_id => $vendor) {
          echo '<li><a href="' . url_for('vendors/show.php?id=' . $vendor_id) . '">' . $vendor . '</a></li>';
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
        foreach ($upcoming_market_day->listed_vendors as $vendor_id => $vendor) {
          echo '<li><a href="' . url_for('vendors/show.php?id=' . $vendor_id) . '">' . $vendor . '</a></li>';
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
