<?php

  // is_blank('abcd')
  // * validate data presence
  // * uses trim() so empty spaces don't count
  // * uses === to avoid false positives
  // * better than empty() which considers "0" to be empty

  /**
   * Checks if a given value is blank.
   * 
   * @param mixed $value the value to check
   * 
   * @return bool if the value is blank
   */
  function is_blank($value) {
    return !isset($value) || trim($value) === '';
  }


  // has_presence('abcd')
  // * validate data presence
  // * reverse of is_blank()
  // * I prefer validation names with "has_"

  /**
   * Checks if a given value is not blank.
   * 
   * @param mixed $value the value to check
   * 
   * @return bool if the value is not blank
   */
  function has_presence($value) {
    return !is_blank($value);
  }


  // has_length_greater_than('abcd', 3)
  // * validate string length
  // * spaces count towards length
  // * use trim() if spaces should not count

  /**
   * Checks if a given string is longer than a given amount of characters.
   * 
   * @param string $value the string to check
   * @param int $min the amount of characters to check the string length against
   * 
   * @return bool if the string is longer
   */
  function has_length_greater_than($value, $min) {
    $length = strlen($value);
    return $length > $min;
  }


  // has_length_less_than('abcd', 5)
  // * validate string length
  // * spaces count towards length
  // * use trim() if spaces should not count

  /**
   * Checks if a given string is shorter than a given amount of characters.
   * 
   * @param string $value the string to check
   * @param int $max the amount of characters to check the string length against
   * 
   * @return bool if the string is shorter
   */
  function has_length_less_than($value, $max) {
    $length = strlen($value);
    return $length < $max;
  }

  // has_length_exactly('abcd', 4)
  // * validate string length
  // * spaces count towards length
  // * use trim() if spaces should not count

  /**
   * Checks if a given string is exactly as long as a given amount of characters.
   * 
   * @param string $value the string to check
   * @param int $exact the amount of characters to check the string length against
   * 
   * @return bool if the string is exactly as long
   */
  function has_length_exactly($value, $exact) {
    $length = strlen($value);
    return $length == $exact;
  }

  // has_length('abcd', ['min' => 3, 'max' => 5])
  // * validate string length
  // * combines functions_greater_than, _less_than, _exactly
  // * spaces count towards length
  // * use trim() if spaces should not count

  /**
   * Validates string lengths based on provided options, inclusive. i.e ['min' => 3, 'max' => 5] or ['exact' => 4].
   * 
   * @param string $value the string to check
   * @param int[] $options an associative array of options
   * 
   * @return bool if the string meets validation
   */
  function has_length($value, $options) {
    if(isset($options['min']) && !has_length_greater_than($value, $options['min'] - 1)) {
      return false;
    } elseif(isset($options['max']) && !has_length_less_than($value, $options['max'] + 1)) {
      return false;
    } elseif(isset($options['exact']) && !has_length_exactly($value, $options['exact'])) {
      return false;
    } else {
      return true;
    }
  }

  // has_inclusion_of( 5, [1,3,5,7,9] )
  // * validate inclusion in a set

  /**
   * Checks if a given value is in a set.
   * 
   * @param mixed $value the value to check
   * @param array $set the array to check inside
   * 
   * @return bool if the value is in the set
   */
  function has_inclusion_of($value, $set) {
    return in_array($value, $set);
  }

  // has_exclusion_of( 5, [1,3,5,7,9] )
  // * validate exclusion from a set
  function has_exclusion_of($value, $set) {
    return !in_array($value, $set);
  }

  // has_string('nobody@nowhere.com', '.com')
  // * validate inclusion of character(s)
  // * strpos returns string start position or false
  // * uses !== to prevent position 0 from being considered false
  // * strpos is faster than preg_match()

  /**
   * Checks if a given string contains another given string.
   * 
   * @param string $value the string to search inside
   * @param string $required_string to string to search for
   * 
   * @return bool if the required string was found
   */
  function has_string($value, $required_string) {
    return strpos($value, $required_string) !== false;
  }

  // has_valid_email_format('nobody@nowhere.com')
  // * validate correct format for email addresses
  // * format: [chars]@[chars].[2+ letters]
  // * preg_match is helpful, uses a regular expression
  //    returns 1 for a match, 0 for no match
  //    http://php.net/manual/en/function.preg-match.php

  /**
   * Checks if the provided string is a valid email address.
   * 
   * @param string $value the string to check
   * 
   * @return bool if the string is a valid email address
   */
  function has_valid_email_format($value) {
    $email_regex = '/\A[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\Z/i';
    return preg_match($email_regex, $value) === 1;
  }

  // has_unique_username('johnqpublic')
  // * Validates uniqueness of members.username
  // * For new records, provide only the username.
  // * For existing records, provide current ID as second argument
  //   has_unique_username('johnqpublic', 4)

  /**
   * Checks if there is a user with a given display name, other than the user associated with the provided user_id
   * 
   * @param string $display_name the user's display name to check for
   * @param int $current_id [OPTIONAL] the id of the user currently being checked
   * 
   * @return bool if the display name is unique
   */
  function has_unique_display_name($display_name, $current_id="0") {
    // Need to re-write for OOP
    $user = User::find_by_display_name($display_name);
    return ($user === false || $user->user_id == $current_id);
  }

?>
