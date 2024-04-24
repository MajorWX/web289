<?php

/**
 * Creates a new mysqli connection to the database based on the set DB_* constants.
 * 
 * @return mysqli the connection to the database
 */
function db_connect() {
  $connection = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
  confirm_db_connect($connection);
  return $connection;
}

/**
 * Checks if the connection to the database is working properly, displaying an error if it doesn't.
 * 
 * @param mysqli $connection the connection to the database
 */
function confirm_db_connect($connection) {
  if($connection->connect_errno) {
    $msg = "Database connection failed: ";
    $msg .= $connection->connect_error;
    $msg .= " (" . $connection->connect_errno . ")";
    exit($msg);
  }
}

/**
 * Closes the connection to the database.
 * 
 * @param mysqli $connection the connection to the database
 */
function db_disconnect($connection) {
  if(isset($connection)) {
    $connection->close();
  }
}

?>
