<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Reynolds Hill Farmers Market <?php if(isset($page_title)) {echo ' - ' . h($page_title); }?></title>
  <!-- Change this link to use <a href="<~?php echo url_for('/index.php'); ?>" -->
  <link href="css/styles.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <header>
    <!-- Wrap this in an <a> to make this link back to home -->
    <h1>Reynolds Hill Farmers Market</h1>
    <!-- Change all these <a> links to use <a href="<~?php echo url_for('/index.php'); ?>" -->
    <nav>
      <ul>
        <li><a href="#">Calendar</a></li>
        <li><a href="#">Vendors</a></li>
        <li><a href="#">Products</a></li>
        <!-- Edit this to be views and split the links into a login and a signup -->
        <li><a href="#">Register / Login</a></li>
      </ul>
    </nav>
  </header>

