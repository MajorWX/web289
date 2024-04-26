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

// // PHP on Windows does not have a money_format() function.
// // This is a super-simple replacement.
// if(!function_exists('money_format')) {
//   function money_format($format, $number) {
//     return '$' . number_format($number, 2);
//   }
// }

?>
