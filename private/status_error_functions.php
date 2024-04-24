<?php

/**
 * Restricts access to the page only to users who have logged in. Redirects to the login page if the user is not logged in.
 */
function require_login() {
  global $session;
  if(!$session->is_logged_in()) {
    $session->message('You must be logged in to view this page.');
    redirect_to(url_for(('/login.php')));
  }
}

/**
 * Restricts access to the page only to users who have logged in. Redirects to the home page if the user is logged in but not an admin. Redirects to the login page if the user is not logged in.
 */
function require_admin_login() {
  global $session;
  if(!$session->is_admin_logged_in()) {
    $session->message('This page requires admin access.');
    // If not logged in
    if(!$session->is_logged_in()) {
      redirect_to(url_for(('/login.php')));
    } else {
      // If logged in
      redirect_to(url_for(('/index.php')));
    }
    
  }
}

/**
 * Turns an array of arrays into a single printable string that contains a list of errors in html tags.
 * 
 * @param array $errors the errors to be printed
 * 
 * @return string the printable string of html tags
 */
function display_errors($errors=array()) {
  $output = '';
  if(!empty($errors)) {
    $output .= "<div class=\"errors\">";
    $output .= "Please fix the following errors:";
    $output .= "<ul>";
    foreach($errors as $error) {
      $output .= "<li>" . h($error) . "</li>";
    }
    $output .= "</ul>";
    $output .= "</div>";
  }
  return $output;
}

/**
 * Gets the session message and clears it.
 * 
 * @return string|void The message found in the session data, if it exists
 */
function get_and_clear_session_message() {
  if(isset($_SESSION['message']) && $_SESSION['message'] != '') {
    $msg = $_SESSION['message'];
    unset($_SESSION['message']);
    return $msg;
  }
}

/**
 * Gets the session message and returns it as a printable string with html tags.
 * 
 * @return string|void the message in html tags, if it exists
 */
function display_session_message() {
  $msg = get_and_clear_session_message();
  if(isset($msg) && $msg != '') {
    return '<div id="message">' . h($msg) . '</div>';
  }
}

?>
