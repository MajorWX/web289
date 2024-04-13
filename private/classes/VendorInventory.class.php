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
   */
  public $vendor;

  /**
   * The product object associated with this inventory listing.
   */
  public $product;

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
   * @param int[]  $vendor_ids t
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



  // VENDOR RENDERING FUNCTIONS =====================================================

  /**
   * Sorts a list of VendorInventory objects with populated product attributes into an associative array keyed with category_names.
   * 
   * @param VendorInventory[] $vendor_inventory_array an unsorted list of VendorInventory objects
   * 
   * @return VendorInventory[][] an associative array, [category_name][simple list of product listings with that category]
   */
  static public function sort_into_categories($vendor_inventory_array) {
    $sorted_inventory_array = [];

    foreach($vendor_inventory_array as $inventory_listing){
      $sorted_inventory_array[$inventory_listing->product->category_name][] = $inventory_listing;
    }

    return $sorted_inventory_array;
  }

  /**
   * Prints each product listing as a row in an HTML table, with each product category dividing up the table.
   * 
   * @param VendorInventory[][] $sorted_inventory_array an associative array from the static sort_into_categories() function
   */
  static public function create_products_table($sorted_inventory_array) {

    echo "<table>";
    echo "<tr>";
    echo "<th>Product Name</th>";
    echo "<th>Listed Price</th>";
    echo "<th>In Stock</th>";
    echo "</tr>";

    // Loop for categories
    foreach($sorted_inventory_array as $category => $products){
      echo "<tr>";
      echo '<td class="product-category" colspan="3">' . $category . '</td>';
      echo "</tr>";

      // Loop for each listing
      foreach($products as $inventory_listing){
        echo "<tr>";
        echo "<td>" . $inventory_listing->product->product_name . "</td>";
        echo "<td>$" . $inventory_listing->listing_price . "</td>";
        echo "<td>" . ($inventory_listing->in_stock ? "Yes" : "No") . "</td>";
        echo "</tr>";
      } // End loop for each listing
    } // End loop for categories
    echo "</table>";
  } // End create_products_table()

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

}

?>
