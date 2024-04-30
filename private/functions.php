<?php

/**
 * Gets the full URL for a provided filepath in the public folder.
 * 
 * @param string $script_path the path from the public folder to a given file
 * 
 * @return string the full url for a file
 */
function url_for($script_path) {
  // add the leading '/' if not present
  if($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

/**
 * Gets the relative path to the public folder.
 * 
 * @return string the relative path from the current file to the public folder
 */
function path_to_public() {
  $current_location = $_SERVER['REQUEST_URI'];
  $path_here_from_public = substr(strstr($current_location, '/public/'), 8);
  $num_folders = substr_count($path_here_from_public, '/');
  $path_back_to_public = '';
  $counter = 0;
  while($counter < $num_folders) {
    $path_back_to_public .= '../';
    $counter++;
  }
  return $path_back_to_public;
}

/**
 * Sanitizes a string to be url encoded.
 * 
 * @param string $string the string to be sanitized
 * 
 * @return string the sanitized string
 */
function u($string="") {
  return urlencode($string);
}

/**
 * Sanitizes a string to be raw url encoded.
 * 
 * @param string $string the string to be sanitized
 * 
 * @return string the sanitized string
 */
function raw_u($string="") {
  return rawurlencode($string);
}

/**
 * Sanitizes a string by converting all special characters to be html entities.
 * 
 * @param string $string the string to be sanitized
 * 
 * @return string the sanitized string
 */
function h($string="") {
  return htmlspecialchars($string);
}

/**
 * Sets the server header for a 404 error.
 */
function error_404() {
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  exit();
}

/**
 * Sets the server header for a 500 error.
 */
function error_500() {
  header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
  exit();
}

/**
 * Changes pages in the browser to the page at a given file path.
 * 
 * @param string $location the file path to the desired page, may want to use url_for() function
 */
function redirect_to($location) {
  header("Location: " . $location);
  exit;
}

/**
 * Checks if the page is a post request.
 * 
 * @return bool if the page is a post request
 */
function is_post_request() {
  return $_SERVER['REQUEST_METHOD'] == 'POST';
}

/**
 * Checks if the page is a get request.
 * 
 * @return bool if the page is a get request
 */
function is_get_request() {
  return $_SERVER['REQUEST_METHOD'] == 'GET';
}

/**
 * Reads the home content text file and returns each section as an array of strings.
 * 
 * @return string[][] an associative array of sting arrays, one array per field: 'about_section_content', 'address_content', and 'market_hour_content'
 */
function read_home_content() {
  // Reading and storing information
  $filepath = path_to_public() . '../../' . url_for('/home/home-content.txt');
  $readFile = fopen($filepath, "r");
  $about_section_content = [];
  $address_content = [];
  $market_hour_content = [];

  // Reading the first line of the about section
  $line = trim(fgets($readFile));
  // Adding lines to the about section until the end of the file or until the address marker appears
  while (!feof($readFile) && $line != '#Address') {
    if(strlen($line) > 0) {
      $about_section_content[] = $line;
    }
    $line = trim(fgets($readFile));
  }


  // Reading the first line of the Address section
  $line = trim(fgets($readFile));
  // Adding lines to the address until the end of the file or until the market hours marker appears
  while (!feof($readFile) && $line != '#Market Hours') {
    $address_content[] = $line;
    $line = trim(fgets($readFile));
  }


  // Reading the first line of the Market Hours section
  $line = trim(fgets($readFile));
  // Adding lines to the market hours until the end of the file or until the contact info marker appears
  while (!feof($readFile) && $line != '#Contact Info') {
    $market_hour_content[] = $line;
    $line = trim(fgets($readFile));
  }


  // Closing the file
  fclose($readFile);

  $fields = [];
  $fields['about_section_content'] = $about_section_content;
  $fields['address_content'] = $address_content;
  $fields['market_hour_content'] = $market_hour_content;
  return $fields;
}

/**
 * Reads the contact info in the home-content file and returns it as an array of strings
 * 
 * @return string[] the contact info with each line as an item in the array
 */
function read_contact_info() {
  $filepath = path_to_public() . '../..' . url_for('/home/home-content.txt');
  $readFile = fopen($filepath, "r");
  $contact_info_content = [];

  // Going through the file until it reaches contact info
  $line = trim(fgets($readFile));
  while (!feof($readFile) && $line != '#Contact Info') {
    $line = trim(fgets($readFile));
  }

  // Reading the first line of the contact info section
  $line = trim(fgets($readFile));
  // Adding lines to the contact info array until the end of the file
  while (!feof($readFile)) {
    $contact_info_content[] = $line;
    $line = trim(fgets($readFile));
  }

  // Closing the file
  fclose($readFile);

  // Returning the content info array
  return $contact_info_content;
}


function print_as_paragraphs($lines) { 
  if(count($lines) > 0) {
    foreach ($lines as $line) {
      echo "<p>" . $line . "</p>";
    }
  }
}

function print_as_lines($lines) {
  if(count($lines) > 0) {
    echo "<p>";
    foreach ($lines as $line) {
      echo $line . "<br>";
    }
    echo "</p>";
  }
}

?>
