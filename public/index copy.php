<?php require_once('../private/initialize.php'); ?>

<?php include(SHARED_PATH . '/public_header.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Reynolds Hill Farmers Market - Home</title>
  <link href="css/styles.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <header>
    <!-- Wrap this in an <a> to make this link back to home -->
    <h1>Reynolds Hill Farmers Market</h1>
    <nav>
      <ul>
        <li><a href="#">Calendar</a></li>
        <li><a href="#">Vendors</a></li>
        <li><a href="#">Products</a></li>
        <li><a href="#">Register / Login</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section id="about">
      <div>
        <h2>About Reynolds Hill Farmers Market</h2>
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
        <h2>[Today's/Next] Market</h2>
        <!-- Populate this list with php -->
        <h3>Featured Vendors</h3>
        <ul>

        </ul>
      </div>

      <div>
        <!-- Populate this header content with php-->
        <h2>[Next/Upcoming] Market</h2>
        <!-- Populate this list with php -->
        <h3>Featured Vendors</h3>
        <ul>

        </ul>
      </div>

      <div>
        <a href="#">See Full Calendar</a>
      </div>
    </section>
  </main>

  <footer>
    <h2>Contact Us</h2>
    <p>Contact info</p>
  </footer>
</body>

</html>
