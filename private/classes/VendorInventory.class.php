<?php

class VendorInventory extends DatabaseObject {

  static protected $table_name = 'vendor_inventory';
  static protected $db_columns = ['inventory_id', 'inv_vendor_id', 'inv_product_id', 'listing_price', 'in_stock'];

  public $inventory_id;
  public $inv_vendor_id;
  public $inv_product_id;
  public $listing_price;
  public $in_stock;

  public $vendor;
  public $product;

  // SQL FUNCTIONS =====================================================

  public function populate_product(){
    $this->product = Product::find_by_id($this->inv_product_id);
  }

  public function populate_vendor(){
    $this->vendor = Vendor::find_by_id($this->inv_vendor_id);
  }

  static public function find_by_vendor($vendor_id){

    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE inv_vendor_id = " . $vendor_id . ";";

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

  static public function find_by_product($product_id){

    $sql = "SELECT * FROM " . static::$table_name . " ";
    $sql .= "WHERE inv_product_id = " . $product_id . ";";

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



  // VENDOR RENDERING FUNCTIONS =====================================================


  static public function sort_into_categories($vendor_inventory_array){
    $sorted_inventory_array = [];

    foreach($vendor_inventory_array as $inventory_listing){
      $sorted_inventory_array[$inventory_listing->product->category_name][] = $inventory_listing;
    }

    return $sorted_inventory_array;
  }

  static public function create_products_table($sorted_inventory_array){

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

  static public function create_vendor_table($filtered_vendor_list){
    echo "<table>";
    echo "<tr>";
    echo "<th>Vendor Name</th>";
    echo "<th>Link to Vendor</th>";
    echo "<th>Listed Price</th>";
    echo "<th>In Stock</th>";
    echo "</tr>";

    foreach($filtered_vendor_list as $inventory_listing){
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
