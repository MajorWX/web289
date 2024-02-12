<?php

//Defining Database Credentials

// localhost
define("DB_SERVER", "localhost");
define("DB_USER", "farmersMarketUser");
define("DB_PASS", "web289");
define("DB_NAME", "farmers_market");

// Kira's Webhost
// define("DB_SERVER", "localhost");
// define("DB_USER", "umuzpnjuhogpj ");
// define("DB_PASS", "ggicyhnls95h");
// define("DB_NAME", "dbu7plrbumrykh");


//Connecting to the database

function db_connect() {
  $connection = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
  return $connection;
}

$database = db_connect();


function print_table($table_name){
  // Using the database
  global $database;
  // Writing the SQL for the query
  $sql = "SELECT * FROM " . $table_name;
  // Querying the database
  $result = $database->query($sql);

  // Creating an array for field names
  $field_names = [];

  // Adding each field name to the array
  while ($field = mysqli_fetch_field($result)) {
    array_push($field_names, $field->name);
  }

  // Printing the HTML tags to display the table
  echo "<h2>" . $table_name . "</h2>";
  echo "<table>";
  echo "<tr>";

  // Printing each field name as a column header
  foreach($field_names as $name){
    echo "<th>" . $name . "</th>";
  }

  echo "</tr>";

  // Getting rows of results, one row at a time
  while ($row = mysqli_fetch_array($result)) {
    echo "<tr>";

    // Getting each value from the row, one cell at a time
    foreach($row as $property => $value) {
      // Removing extra data by checking properties against the field names
      if(in_array($property, $field_names)) {
        // Printing each cell value to a cell
        echo "<td>" . $value . "</td>";
      }
    }
    echo "</tr>";
  }
  echo "</table>";
}

?>

<!-- Writing the HTML page -->

<!doctype html>

<html lang="en">
  <head>
    <title>Kira Beitler - WEB289 - Database Connection</title>
    <meta charset="utf-8">
    <style>
      table, th, td {
        border: 1px solid black;
        padding: 2px;
      }
    </style>
  </head>

  <body>
  
    <header>
      <h1>Farmer's Market Database Tables</h1>
    </header>

    <?php 
    print_table("vendors");
    print_table("users");
    print_table("calendar");
    print_table("calendar_listing");
    print_table("phone_numbers");
    print_table("images");
    print_table("vendor_inventory");
    print_table("products");
    print_table("product_categories");
    ?>
  </body>
</html>
