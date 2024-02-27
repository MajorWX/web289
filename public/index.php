<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Home'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

  <main>
    <h2>Home Page</h2>
    <section id="about">
      <div>
        <h3>About Reynolds Hill Farmers Market</h3>
        <p>Blurb goes here</p>
      </div>
      <div>
        <p id="address">Address</p>
        <p id="market-hours">Market Hours</p>
      </div>
    </section>

    <section id="calendar-section">
      <div>
        <!-- Populate this header content with php-->
        <h3>[Today's/Next] Market</h3>
        <!-- Populate this list with php -->
        <h4>Featured Vendors</h4>
        <ul>

        </ul>
      </div>

      <div>
        <!-- Populate this header content with php-->
        <h3>[Next/Upcoming] Market</h3>
        <!-- Populate this list with php -->
        <h4>Featured Vendors</h4>
        <ul>

        </ul>
      </div>

      <div>
        <a href="#">See Full Calendar</a>
      </div>
    </section>
  </main>

<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>
