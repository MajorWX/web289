<?php require_once('../private/initialize.php'); ?>
<?php $page_title = 'Home'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

  <main id="home">
    <h2>Home Page</h2>
    <section id="about">
      <div>
        <h3>About Reynolds Hill Farmers Market</h3>
        <p>Welcome to Reynold's Hill Farmers Market, where community and agriculture intertwine to celebrate local bounty. Nestled in the heart of Asheville, North Carolina, our market is a vibrant gathering place that bridges the gap between farmers and consumers.</p>
        <p>At Reynold's Hill Farmers Market, we pride ourselves on showcasing the finest produce, meats, cheeses, and artisanal goods that our region has to offer. Our farmers and producers are committed to sustainable practices, ensuring that every purchase supports both the local economy and the environment.</p>
        <p>Beyond just a marketplace, Reynold's Hill Farmers Market is a hub of community activity. It's a place where neighbors come together to share stories, swap recipes, and forge connections with the people who grow their food. Our market hosts live music, cooking demonstrations, and family-friendly events, creating an atmosphere that is both festive and welcoming.</p>
        <p>Whether you're a food enthusiast, a supporter of local agriculture, or simply looking for a place to enjoy a Saturday morning, Reynold's Hill Farmers Market invites you to join us in celebrating the abundance of Western North Carolina. We're more than just a market — we're a cornerstone of the Asheville community, dedicated to fostering a healthier, happier, and more connected way of life.</p>    
      </div>
      <div>
        <h3 id="address">Address</h3>
        <p>210 Old Charlotte Hwy,<br>
          Asheville, NC 28803</p>
        <h3 id="market-hours">Market Hours</h3>
        <p>Mon-Th: 9am-7pm</p>
        <p>Fri-Sat: 9am-9pm</p>
        <p>Sundays: 12pm-7pm</p>
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
        <a href="<?php echo url_for('/calendar.php') ?>"><span>See Full Calendar</span></a>
      </div>
    </section>
  </main>

<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>
