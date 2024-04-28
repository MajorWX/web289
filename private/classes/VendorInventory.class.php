<?php

class VendorInventory extends DatabaseObject {

  static protected $table_name = 'vendor_inventory';
  static protected $db_columns = ['inventory_id', 'inv_vendor_id', 'inv_product_id', 'listing_price', 'in_stock'];

  /**
   * This inventory listing's unique inventory_id as it appears in the vendor_inventory table.
   */
  public $inventory_id;
  /**
   * The vendor_id of this listing's vendor object as it appears in the vendors table.
   */
  public $inv_vendor_id;
  /**
   * The product_id of this listing's product object as it appears in the products table.
   */
  public $inv_product_id;
  /**
   * The listed price for this listing, in dollars.
   */
  public $listing_price;
  /**
   * A bit representing a bool on whether the vendor has listed this product as in stock.
   */
  public $in_stock;

  /**
   * The vendor object associated with this inventory listing.
   * @var Vendor
   */
  public $vendor;

  /**
   * The product object associated with this inventory listing.
   * @var Product
   */
  public $product;

  /**
   * The image associated with this inventory listing.
   * @var Image
   */
  public $image;




  // SQL FUNCTIONS =====================================================

  /**
   * Queries the database and sets this VendorInventory object's product attribute. 1 Query
   */
  public function populate_product(){
    $this->product = Product::find_by_id($this->inv_product_id);
  }

  /**
   * Queries the database and sets this VendorInventory object's vendor attribute. 1 Query
   */
  public function populate_vendor(){
    $this->vendor = Vendor::find_by_id($this->inv_vendor_id);
  }

  /**
   * Queries the database and gets all VendorInventory objects associated with a given vendor, then populates each one's product attribute. N+1 Queries
   * 
   * @param int $vendor_id the vendor_id of the vendor to search for
   * 
   * @return VendorInventory[]|false all listings associated with the given vendor, each with a populated product attributed
   */
  static public function find_by_vendor($vendor_id){

    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE inv_vendor_id = '" . $vendor_id . "';";

    $obj_array = static::find_by_sql($sql);
    
    if(!empty($obj_array)) {
      foreach($obj_array as $vendorInventory){
        $vendorInventory->populate_product();
      }
      return $obj_array;
    } else {
      return false;
    }
  }

  /**
   * Queries the database and gets all VendorInventory objects associated with a given product, then populates each one's vendor attribute. N+1 Queries
   * 
   * @param int $product_id the product_id of the product to search for
   * 
   * @return VendorInventory[]|false all listings associated with the given product, each with a populated vendor attributed
   */
  static public function find_by_product($product_id) {
    // Constructing the SQL statement
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE inv_product_id = '" . $product_id . "';";

    $obj_array = static::find_by_sql($sql);
    
    if(!empty($obj_array)) {
      foreach($obj_array as $vendorInventory){
        $vendorInventory->populate_vendor();
      }
      return $obj_array;
    } else {
      return false;
    }
  }

  /**
   * Queries the database and gets all VendorInventory objects associated with a given product and a list of valid vendor ids. Does not populate the VendorInventory vendor properties. 1 Query
   * 
   * @param int $product_id the product_id of the product to search for
   * @param int[]  $vendor_ids the vendor_ids to filter the search on
   * 
   * @return VendorInventory[]|false all listings associated with the given product, each with a populated vendor attributed
   */
  static public function find_by_product_with_wl($product_id, $vendor_ids) {
    // Constructing the SQL statement
    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE inv_product_id = '" . $product_id . "' ";
    $sql .= "AND inv_vendor_id IN (";

    // Making a non-reference copy of vendor_ids values for non-destructive workflow
    $vendor_id_whitelist = array_merge($vendor_ids);

    // Going through each id and adding it to the list
    while($vendor_id_whitelist){
      // Popping off the first element of the list of ids
      $current_id = array_shift($vendor_id_whitelist);

      // Adding it to the IN statement
      $sql .=  $current_id;

      // Adding a trailing comma and space if there are more in the list
      if($vendor_id_whitelist) { $sql .= ", "; }
    }

    // Closing the sql statement
    $sql .= ");";

    // Querying the database
    $obj_array = static::find_by_sql($sql);

    if(!empty($obj_array)) {
      return $obj_array;
    } else {
      return false;
    }


  } // End find_by_product_with_wl()


  /**
   * Queries the database and changes the VendorInventory listings in the vendor_inventory table. N Queries
   * 
   * @param VendorInventory[] $changed_listings the list of VendorInventory objects to be changed in the database
   * 
   * @param Vendor $error_vendor the vendor to give all errors to 
   * 
   * @return mysqli_result[]|bool[] the query result
   */
  static public function update_changes($changed_listings, $error_vendor) {
    // Validating all VendorInventory objects, storing any errors in the $error_vendor error array
    foreach($changed_listings as $inventory_listing) {
      $inventory_listing->validate();
      if(!empty($inventory_listing->errors)) { array_push($error_vendor->errors, $inventory_listing->errors); }
    }

    // Ending the function early if there are any errors
    if(!empty($error_vendor->errors)) { return false; }

    $result_array = [];

    foreach($changed_listings as $inventory_listing) {
      $result_array[] = $inventory_listing->update();
    }

    return $result_array;
  }

  /**
   * Updates an existing inventory_listing in the vendor_inventory table. 1 Query
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
    $sql .= " WHERE inventory_id='" . self::$database->escape_string($this->inventory_id) . "' ";
    $sql .= "LIMIT 1;";
    $result = self::$database->query($sql);
    return $result;
  }


  /**
   * Deletes existing inventory_listings from the vendor_inventory table. N Queries
   * 
   * @param VendorInventory[] $listings_to_delete the list of VendorInventory objects to be deleted from the database
   * 
   * @return mysqli_result[]|bool[] the query result
   */
  static public function delete_changes($listings_to_delete, $inventory_images_by_product_id = []) {
    // Checking for image array
    $has_images = false;
    if(count($inventory_images_by_product_id) > 0) {
      $has_images = true;
    }

    $result_array = [];

    foreach($listings_to_delete as $inventory_listing) {
      // Checking if this inventory listing has any images to delete
      if($has_images) {
        if(array_key_exists($inventory_listing->inv_product_id, $inventory_images_by_product_id)) { 
          $result_array[] = $inventory_images_by_product_id[$inventory_listing->inv_product_id]->delete;
        }
      }

      // Deleting the inventory listing itself
      $result_array[] = $inventory_listing->delete();
    }

    return $result_array;
  }

  /**
   * Removes a row from the vendor_inventory table based on this inventory listing's inventory_id. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function delete() {
    $sql = "DELETE FROM " . static::$table_name . " ";
    $sql .= "WHERE inventory_id='" . self::$database->escape_string($this->inventory_id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;

    // After deleting, the instance of the object will still
    // exist, even though the database record does not.
    // This can be useful, as in:
    //   echo $user->first_name . " was deleted.";
    // but, for example, we can't call $user->update() after
    // calling $user->delete().
  }



  // VENDOR RENDERING FUNCTIONS =====================================================

  /**
   * Sorts a list of VendorInventory objects with populated product attributes into an associative array keyed with category_names.
   * 
   * @param VendorInventory[] $vendor_inventory_array an unsorted list of VendorInventory objects
   * 
   * @return VendorInventory[][] an associative array, [category_name][an associative array of inventory listings Keys:inventory_ids. Values:VendorInventory Objects.]
   */
  static public function sort_into_categories($vendor_inventory_array) {
    $sorted_inventory_array = [];

    foreach($vendor_inventory_array as $inventory_listing){
      $sorted_inventory_array[$inventory_listing->product->category_name][$inventory_listing->inventory_id] = $inventory_listing;
    }

    ksort($sorted_inventory_array);
    return $sorted_inventory_array;
  }

  /**
   * Sorts an unorganized list of VendorInventory objects into an associative array with the inventory_id as keys to each VendorInventory object.
   * 
   * @param VendorInventory[] $vendor_inventory_array an unsorted list of VendorInventory objects
   * 
   * @return VendorInventory[] an associative array of inventory listings Keys:inventory_ids. Values:VendorInventory Objects.
   */
  static public function sort_by_listing_id($vendor_inventory_array) {
    $sorted_by_id = [];

    foreach($vendor_inventory_array as $inventory_listing){
      $sorted_by_id[$inventory_listing->inventory_id] = $inventory_listing;
    }

    return $sorted_by_id;
  }

  /**
   * Prints each product listing as a row in an HTML table, with each product category dividing up the table.
   * 
   * @param VendorInventory[][] $sorted_inventory_array an associative array from the static sort_into_categories() function
   * @param Image[] $inventory_images_by_product_id an associative array of images with keys of their product ids
   */
  static public function create_products_table($sorted_inventory_array, $inventory_images_by_product_id = []) {
    // Checking for image array
    $has_images = false;
    if(count($inventory_images_by_product_id) > 0) {
      $has_images = true;
    }

    echo "<table>";
    echo "<tr>";
    if($has_images) {
      echo "<th>Product Image</th>";
    }
    echo "<th>Product Name</th>";
    echo "<th>Listed Price</th>";
    echo "<th>In Stock</th>";
    echo "</tr>";

    // Loop for categories
    foreach($sorted_inventory_array as $category => $products){
      echo "<tr>";
      echo '<td class="product-category" colspan="'. (($has_images) ? 4 : 3) .'">' . $category . '</td>';
      echo "</tr>";

      // Loop for each listing
      foreach($products as $inventory_listing) {
        echo "<tr>";

        // The image column, if it exists
        if($has_images) {
          echo "<td>";
          if(array_key_exists($inventory_listing->inv_product_id, $inventory_images_by_product_id)) {
            $inventory_images_by_product_id[$inventory_listing->inv_product_id]->print_image(200, 200);
          }
          echo "</td>";
        }

        echo "<td>" . $inventory_listing->product->product_name . "</td>";
        echo "<td>$" . $inventory_listing->listing_price . "</td>";
        echo "<td>" . ($inventory_listing->in_stock ? "Yes" : "No") . "</td>";
        echo "</tr>";
      } // End loop for each listing
    } // End loop for categories
    echo "</table>";
  } // End create_products_table()


  /**
   * Used in vendor_inventory/edit.php. Prints a table of form inputs for editing several of a vendor's inventory at once.
   * 
   * @param VendorInventory[][] $sorted_inventory_array an associative array from the static sort_into_categories() function
   * @param Image[] $inventory_images_by_product_id an associative array of images with keys of their product ids
   */
  static public function create_edit_vendor_inventory_table($sorted_inventory_array, $inventory_images_by_product_id = []) {
    // Starting the table
    echo "<table>";
    echo "<tr>";
    echo "<th>Product Image</th>";
    echo "<th>Product Name</th>";
    echo "<th>Listed Price</th>";
    echo "<th>In Stock</th>";
    echo "<th>Mark Listing for Deletion</th>";
    echo "</tr>";

    // Loop for categories
    foreach($sorted_inventory_array as $category => $products){
      echo "<tr>";
      echo '<td class="product-category" colspan="5">' . $category . '</td>';
      echo "</tr>";

      // Loop for each listing
      foreach($products as $inventory_listing) {
        echo "<tr>";

        // The image column
        echo "<td>";

        // See if this product id has an image
        if(array_key_exists($inventory_listing->inv_product_id, $inventory_images_by_product_id)) {
          $inventory_images_by_product_id[$inventory_listing->inv_product_id]->print_image(200, 200);
          echo '<br><label for="delete-' . $inventory_listing->inv_product_id . '">Mark Image for Deletion: </label>';
          echo '<input type="checkbox" id="delete-' . $inventory_listing->inv_product_id . '" name="delete_image[' . $inventory_listing->inv_product_id . ']">';
        } else {
          echo '<label for="upload-image-' . $inventory_listing->inv_product_id . '">Upload Image: </label>';
          echo '<input type="file" id="upload-image-' . $inventory_listing->inv_product_id . '" name="' . $inventory_listing->inv_product_id . '">';
        }

        echo "</td>";
        

        // The Product name
        echo "<td>" . $inventory_listing->product->product_name . "</td>";

        // The Listing Price field
        echo "<td>";
        echo '$<input type="number" name="inventory[' . $inventory_listing->inventory_id . '][listing_price]" value="' . $inventory_listing->listing_price . '" min="0" step="0.01" required>';
        echo "</td>";

        // The In Stock field
        echo "<td>";
        $in_stock_string = ($inventory_listing->in_stock > 0) ? ' checked' : '';
        echo '<input type="checkbox" name="inventory[' . $inventory_listing->inventory_id . '][in_stock]"' . $in_stock_string . '>';
        echo "</td>";

        // The deletion field
        echo "<td>";
        echo '<input type="checkbox" name="inventory[' . $inventory_listing->inventory_id . '][delete]">';
        echo "</td>";

        echo "</tr>";
      } // End loop for each listing
    } // End loop for categories
    echo "</table>";
  }

  /**
   * Filters a list of VendorInventory objects to only include vendors that are showing up on a given date. N Queries
   * 
   * @param VendorInventory[] $vendor_inventory_array the list of VendorInventory objects to be filtered
   * @param CalendarDate $given_date the date to filter vendor inventory listings for
   * 
   * @return VendorInventory[] the list of VendorInventory objects now filtered by the given date
   */
  static public function filter_vendors_by_date($vendor_inventory_array, $given_date){
    $filtered_vendor_list = [];

    // $next_market_day = CalendarDate::get_next_market_day();

    foreach($vendor_inventory_array as $inventory_listing){
      if($inventory_listing->vendor->is_coming_on_date($given_date)){
        $filtered_vendor_list[] = $inventory_listing;
      }
    }

    return $filtered_vendor_list;
  }

  /**
   * Prints each vendor listing as a row in an HTML table for a products/show.php page
   * 
   * @param VendorInventory[] $vendor_list a list of VendorInventory objects that includes the vendors to list in the table
   */
  static public function create_vendor_table($vendor_list){
    echo "<table>";
    echo "<tr>";
    echo "<th>Vendor Name</th>";
    echo "<th>Link to Vendor</th>";
    echo "<th>Listed Price</th>";
    echo "<th>In Stock</th>";
    echo "</tr>";

    foreach($vendor_list as $inventory_listing){
      echo "<tr>";
        echo "<td>" . $inventory_listing->vendor->vendor_display_name . "</td>";
        echo '<td><a href="' . url_for('/vendors/show.php?id=' . $inventory_listing->vendor->vendor_id) . '">View Details</a></td>';
        echo "<td>$" . $inventory_listing->listing_price . "</td>";
        echo "<td>" . ($inventory_listing->in_stock ? "Yes" : "No") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
  }

  /**
   * Takes in an array of VendorInventory objects and the arguments from the /vendor_inventory/edit form and returns an array of VendorInventory objects to update.
   * 
   * @param VendorInventory[] $vendor_inventory_array the list of VendorInventory objects to be filtered
   * @param mixed $form_values the form results of $_POST['inventory'] in /vendor_inventory/edit
   * 
   * @return VendorInventory[] the VendorInventory objects that don't match the forms, in an associative array with Keys:inventory_ids. Values: VendorInventory objects
   */
  static public function get_listing_changes($vendor_inventory_array, $form_values) {
    $sorted_by_id = static::sort_by_listing_id($vendor_inventory_array);
    $changed_listings = [];

    // Going through every id value of the form_values and comparing them to the ids in the vendor_inventory_array
    foreach($form_values as $inventory_id => $changes_array) {
      // Checking if the VendorInventory is marked for deletion.
      if(array_key_exists('delete', $changes_array)){
        continue;
      } 
      // Checking if the listing price changed
      elseif($sorted_by_id[$inventory_id]->listing_price != $changes_array['listing_price']) {
        $changed_listings[$inventory_id] = $sorted_by_id[$inventory_id];
        continue;
      }
      // Checking if the in_stock bool changed
      elseif(($sorted_by_id[$inventory_id]->in_stock > 0) != (array_key_exists('in_stock', $changes_array))) {
        $changed_listings[$inventory_id] = $sorted_by_id[$inventory_id];
        continue;
      }
    } // End foreach 

    // Setting the values of the Vendor Inventory objects
    foreach($changed_listings as $inventory_id => $inventory_object) {
      $inventory_object->listing_price = $form_values[$inventory_id]['listing_price'];
      $inventory_object->in_stock = (array_key_exists('in_stock', $form_values[$inventory_id])) ? 1 : 0;
    }

    // Returning the array of changed objects
    return $changed_listings;
  }

  /**
   * Takes in an array of VendorInventory objects and the arguments from the /vendor_inventory/edit form and returns an array of VendorInventory objects to add.
   * 
   * @param VendorInventory[] $vendor_inventory_array the list of VendorInventory objects to be filtered
   * @param mixed $form_values the form results of $_POST['inventory'] in /vendor_inventory/edit
   * 
   * @return VendorInventory[] the VendorInventory objects that have been marked as ready for deletion, in an associative array with Keys:inventory_ids. Values: VendorInventory objects
   */
  static public function get_listing_deletions($vendor_inventory_array, $form_values) {
    $sorted_by_id = static::sort_by_listing_id($vendor_inventory_array);
    $listings_to_delete = [];

    // Going through every id value of the form_values and looking for the delete value
    foreach($form_values as $inventory_id => $changes_array) {
      // Checking if the VendorInventory is marked for deletion.
      if(array_key_exists('delete', $changes_array)){
        $listings_to_delete[$inventory_id] = $sorted_by_id[$inventory_id];
      } 
    }

    return $listings_to_delete;
  }

}

?>
