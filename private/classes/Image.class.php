<?php

class Image extends DatabaseObject {

  static protected $table_name = 'images';
  static protected $db_columns = ['image_id', 'im_user_id', 'content', 'upload_date', 'alt_text', 'im_vendor_id', 'im_product_id', 'image_purpose'];

  /**
   * This image's id as it corresponds to the images table.
   */
  public $image_id;

  /**
   * The id of this image's uploader as it corresponds to the user table.
   */
  public $im_user_id;

  /**
   * The name of this image's filename within the images upload folder as designated by the target_dir attribute.
   */
  public $content;

  /**
   * The date this image was uploaded.
   */
  public $upload_date;

  /**
   * This image's text to be printed in the alt text field of HTML img tags.
   */
  public $alt_text;

  /**
   * The id of the vendor associated with this image, as it corresponds to the vendors table.
   */
  public $im_vendor_id;

  /**
   * The id of the product associated with this image, as it corresponds to the products table.
   */
  public $im_product_id;

  /**
   * A string describing this image's purpose, used to filter for images that should appear at certain places.
   * Valid purposes: profile (vendor profile pics), inventory (product pictures added by inventory listings)
   */
  public $image_purpose;

  /**
   * The public path to the image upload folder.
   */
  static public $public_image_path = 'images/uploads/';

  /**
   * The public path to the placeholder image.
   */
  static public $placeholder_image_path = 'images/placeholder.png';

  /**
   * The maximum size of a valid uploaded file, in bytes.
   */
  static public $file_size_limit = 8000000; // 8 MB




  // SQL FUNCTIONS ==================================================================




  /**
   * Ties the image file to this Image object, then stores the Image object in the database. Fills this Image Object's errors[] array if there are any errors. 1 Query
   * 
   * @param mixed[] $image_file the $_FILES["form_field_name"] returned from a post request.
   * 
   * @return mysqli_result|bool the query result
   */
  public function upload($image_file) {
    // Make sure the image is valid, exit the function with errors in the image object if it has any.
    $image_is_valid = $this->validate_image_file($image_file);
    if(!$image_is_valid) { return false; }

    // Get the name of the image file in the upload file, cleaning the file name of any ' or " characters
    $image_name = date("y-m-d-H-i-s") . '-$' . str_replace(array('"', "'", ), "", basename($image_file["name"]));
    
    // Create the File Path to store the image at, relative to this Class
    $uploadFile = path_to_public() . Image::$public_image_path . $image_name;

    // Move the uploaded file to the /images/uploads/ folder
    $file_upload_result = move_uploaded_file($image_file["tmp_name"], $uploadFile);

    // If there was an error uploading the file, return false with a new error message
    if(!$file_upload_result) {
      $this->errors[] = "Sorry, there was an error uploading your file to the server.";
      return false;
    }

    $this->content = $image_name;
    $this->upload_date = date("Y-m-d H:i:s");


    $result = $this->save();

    if($result) {
      return $result;
    } else {
      $this->errors[] = "Could not store the image in the database.";
      return false;
    }    
  }

  /**
   * Checks if a given image file is valid to be uploaded. Fills this Image Object's errors[] array if there are any errors.
   * 
   * @param mixed[] $image_file the $_FILES["form_field_name"] returned from a post request
   * 
   * @return bool if the image is valid to be uploaded
   */
  public function validate_image_file($image_file) {
    $imageFileType = strtolower(pathinfo(basename($image_file["name"]), PATHINFO_EXTENSION));
    $check = getimagesize($image_file["tmp_name"]);

    // Check if file is an actual image
    if ($check == false) {
      $this->errors[] = "Could not process the image file.";
    }

    // Checking the file size
    if($image_file["size"] > Image::$file_size_limit) {
      $this->errors[] = "Image is too large.";
    }

    // Checking the file format
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
      $this->errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    return empty($this->errors);
  }

  /**
   * Determines if this object already exists in the database and then stores it in a new or existing row. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function save() {
    // A new record will not have an ID yet
    if(isset($this->image_id)) {
      return $this->update();
    } else {
      return $this->create();
    }
  }

  /**
   * Creates a new row in the images table based on this image. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  protected function create() {
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $sql = "INSERT INTO " . static::$table_name . " (";
    $sql .= join(', ', array_keys($attributes));
    $sql .= ") VALUES ('";
    $sql .= join("', '", array_values($attributes));
    $sql .= "')";
    $result = self::$database->query($sql);
    if($result) {
      $this->image_id = self::$database->insert_id;
    }
    return $result;
  }

  /**
   * Modifies an existing row in the images table based on this image. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  protected function update() {
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $attribute_pairs = [];
    foreach($attributes as $key => $value) {
      $attribute_pairs[] = "{$key}='{$value}'";
    }

    $sql = "UPDATE " . static::$table_name . " SET ";
    $sql .= join(', ', $attribute_pairs);
    $sql .= " WHERE image_id='" . self::$database->escape_string($this->image_id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }

  /**
   * Removes a row from the images table based on this image's image_id, then removes the file. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function delete() {
    $sql = "DELETE FROM " . static::$table_name . " ";
    $sql .= "WHERE image_id='" . self::$database->escape_string($this->image_id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    if($result) {
      $unlink_result = unlink(path_to_public() . Image::$public_image_path . $this->content);
    }

    if($result && $unlink_result) {
      return $result;
    } else {
      return false;
    }
    

    // After deleting, the instance of the object will still
    // exist, even though the database record does not.
    // This can be useful, as in:
    //   echo $user->first_name . " was deleted.";
    // but, for example, we can't call $user->update() after
    // calling $user->delete().
  }

  /**
   * Finds all images associated with a given vendor id. 1 Query
   * 
   * @param int $vendor_id the id of the vendor to filter by
   * 
   * @return Image[]|bool the image objects found by this search, if they exist.
   */
  static public function find_by_vendor($vendor_id) {
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE im_vendor_id = '" . $vendor_id . "';";

    return static::find_by_sql($sql);
  }

  /**
   * Finds the image object with a given image_id. 1 Query
   * 
   * @param int $image_id the id of the image to filter by
   * 
   * @return Image|bool the image object found by this search, if it exists.
   */
  static public function find_by_id($image_id) {
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE image_id = '" . $image_id . "';";

    return static::find_by_sql($sql)[0];
  }

  /**
   * Finds all images with a given image_purpose. 1 Query
   * 
   * @param string $image_purpose the image purpose to filter by
   * 
   * @return Image[]|bool the image objects found by this search, if they exist.
   */
  static public function find_by_purpose($image_purpose) {
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE image_purpose = '" . $image_purpose . "';";

    return static::find_by_sql($sql);
  }

  /**
   * Finds all images associated with a given product id. 1 Query
   * 
   * @param int $product_id the id of the product to filter by
   * 
   * @return Image[]|bool the image objects found by this search, if they exist.
   */
  static public function find_by_product($product_id) {
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE im_product_id = '" . $product_id . "';";

    return static::find_by_sql($sql);
  }

  // RENDERING FUNCTIONS ==============================================

  

  /**
   * Prints the image to the HTML page with echo.
   * 
   * @param int $max_width the maximum width the image can have, shrinking the image to fit.
   * @param int $max_height the maximum height the image can have, shrinking the image to fit.
   * @param bool $grow [optional] if the image is allowed to increase in size to fit its constraints, defaults to true.
   */
  public function print_image($max_width, $max_height, $grow=true) {
    if(!file_exists(path_to_public() . Image::$public_image_path . $this->content)) { 
      echo "NO FILE WITH PATH: " . path_to_public() . Image::$public_image_path . $this->content;
      return false;}

    // Getting the current image size.
    $image_size = getimagesize(path_to_public() . Image::$public_image_path . $this->content);
    if(!$image_size) { return false; }
    $default_width = $image_size[0]; // 275
    $default_height = $image_size[1]; // 363 

    // Finding the ratios
    $width_ratio = $max_width/$default_width; // 200/275 = 0.727
    $height_ratio = $max_height/$default_height; // 200/363 = 0.551
    

    // Setting the image ratio to the smaller ratio
    $image_ratio = min($width_ratio, $height_ratio);

    if(!$grow) {
      $image_ratio = min($image_ratio, 1);
    }

    $width = floor($default_width * $image_ratio);
    $height = floor($default_height * $image_ratio);

    // Printing the image
    echo '<img src="' . url_for(Image::$public_image_path . $this->content) . '" alt="' . $this->alt_text .'" width="' . $width . '" height="' . $height . '">';
  }

  /**
   * Prints the placeholder image to the HTML page with echo.
   * 
   * @param int $max_width the maximum width the image can have, shrinking the image to fit.
   * @param int $max_height the maximum height the image can have, shrinking the image to fit.
   * @param bool $grow [optional] if the image is allowed to increase in size to fit its constraints, defaults to true.
   */
  static public function print_placeholder($max_width, $max_height, $grow=true) {
    if(!file_exists(path_to_public() . Image::$placeholder_image_path)) { 
      echo "NO FILE WITH PATH: " . path_to_public() . Image::$placeholder_image_path;
      return false;}

    $default_width = 400; 
    $default_height = 400; 

    // Finding the ratios
    $width_ratio = $max_width/$default_width; // 200/275 = 0.727
    $height_ratio = $max_height/$default_height; // 200/363 = 0.551
    

    // Setting the image ratio to the smaller ratio
    $image_ratio = min($width_ratio, $height_ratio);

    if(!$grow) {
      $image_ratio = min($image_ratio, 1);
    }

    $width = floor($default_width * $image_ratio);
    $height = floor($default_height * $image_ratio);

    // Printing the image
    echo '<img src="' . url_for(Image::$placeholder_image_path) . '" alt="A placeholder image for product icons." width="' . $width . '" height="' . $height . '">';
  }



  /**
   * Filters an array of images by a given purpose.
   * 
   * @param Image[] $image_array a list of unfiltered images
   * @param string $purpose the purpose to filter by
   * 
   * @return Image[]|bool the filtered list of images, if they exist
   */
  static public function filter_by_purpose($image_array, $purpose) {
    $filtered_images = [];

    foreach($image_array as $image) {
      if($image->image_purpose == $purpose) {
        $filtered_images[] = $image;
      }
    }

    if(count($filtered_images) > 0) {
      return $filtered_images;
    } else {
      return false;
    }
  }

  /**
   * Takes in a list of Image objects and creates an associative array of arrays keyed by the product id of each image.
   * 
   * @param Image[] $image_array a list of images to be sorted
   * 
   * @return Image[][] an associative array of arrays keyed by product ids
   */
  static public function sort_images_by_product_id($image_array) {
    $images_sorted_by_product_id = [];

    // Going through each image an storying it in the array of each product
    foreach($image_array as $image) {
      $images_sorted_by_product_id[$image->im_product_id][] = $image;
    }

    // Returning the sorted array of arrays
    return $images_sorted_by_product_id;
  }

  /**
   * Takes in an associative array of arrays from sort_images_by_product_id() and reduces it down to a given amount of images per product, chosen randomly.
   * 
   * @param Image[][] $images_sorted_by_product_id an associative array of arrays from sort_images_by_product_id()
   * @param int $image_count_per [OPTIONAL] the maximum number of images per product to randomly select from
   * 
   * @return Image[][]|Image[] a one dimensional associative array if $image_count_per is 1, or a two dimensional associative array otherwise
   */
  static public function randomly_select_image_per_product($images_sorted_by_product_id, $image_count_per=1) {
    // Making a copy of the array of arrays to be manipulated
    $unsorted_assoc = $images_sorted_by_product_id;

    // Making an empty array to be returned
    $selected_images_by_product_id = [];

    // The single image per product option
    if($image_count_per <= 1) {
      // Go through each product id of the provided associative array of arrays and select one image to use
      foreach($unsorted_assoc as $product_id => $images) {
        // If there's just one image to choose from, choose that image
        if(count($images) == 1) {
          $selected_images_by_product_id[$product_id] = $images[0];
        } 
        // Otherwise randomly select one from all possible images in the array
        else {
          $selected_index = random_int(0, count($images)-1);
          $selected_images_by_product_id[$product_id] = $images[$selected_index];
        }
      } // End Foreach

      // Return the one dimensional array
      return $selected_images_by_product_id;

    } // End single image per product option

    // The multiple results per product option
    else {
      // Go through each product id of the provided associative array of arrays and select a number of images
      foreach($unsorted_assoc as $product_id => $images) {
        // If there aren't enough images in the array to exclude any, store the entire array
        if(count($images) <= $image_count_per) {
          $selected_images_by_product_id[$product_id] = $images;
        }
        // Otherwise go through and keep randomly selecting images until the count is met
        else {
          $num_images_selected = 0;
          while($num_images_selected < $image_count_per) {
            // Randomly choosing an image in the array
            $selected_index = random_int(0, count($images)-1);
            // Removing the image from the array
            $selected_image = array_splice($images, $selected_index, 1);
            // Appending the image to the two dimensional array
            $selected_images_by_product_id[$product_id][] = $selected_image;
            // Increment the counter
            $num_images_selected++;
          }
        }
      } // End Foreach

      // Return the two dimensional array
      return $selected_images_by_product_id;
    }
  } // End randomly_select_image_per_product()
  

}
