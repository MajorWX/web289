<?php

class Product extends DatabaseObject {

  static protected $table_name = 'products';
  static protected $db_columns = ['product_id', 'prd_category_id', 'product_name'];

  public $product_id;
  public $prd_category_id;
  public $product_name;

  public $category_name;
  public $inventory_listings = [];

  // SQL Functions ====================================================

  // static public function find_by_sql($sql) {
  //   $result = self::$database->query($sql);
  //   if(!$result) {
  //     exit("Database query failed.");
  //   }

  //   // results into objects
  //   $object_array = [];

  //   // Reading each row
  //   while($row = $result->fetch_assoc()) {
  //     // Make a new Product object
  //     $object = new static;

  //     // Reading each cell
  //     foreach($row as $property => $value) {
  //       if(property_exists($object, $property)) {
  //         $object->$property = $value;
  //       }
  //     } // End foreach for cells

  //     $object_array[] = $object;
  //   } // End while for rows

  //   $result->free();

  //   return $object_array;
  // }

  static public function find_all_categories() {
    $sql = "SELECT * FROM product_categories";

    $result = self::$database->query($sql);
    if(!$result) {
      exit("Database query failed.");
    }
    // Storing Results
    $category_list = [];
    // Reading each row
    while($record = $result->fetch_assoc()) {
      $category_id = '';
      $category_name = '';
      // Reading each cell
      foreach($record as $property => $value) {
        if($property === 'category_id'){
          $category_id = $value;
        } else if($property === 'category_name'){
          $category_name = $value;
        }
      } // End foreach for cells
      $category_list[$category_id] = $category_name;
    }// End while for rows

    return $category_list;
  } // End find_all_categories()

  static public function find_all_products() {
    $sql = "SELECT p.product_id, p.prd_category_id, c.category_name, p.product_name ";
    $sql .= "FROM products p, product_categories c ";
    $sql .= "WHERE p.prd_category_id = c.category_id;";

    return static::find_by_sql($sql);
  }

  static public function find_by_id($product_id) {
    $sql = "SELECT p.product_id, p.prd_category_id, c.category_name, p.product_name ";
    $sql .= "FROM products p, product_categories c ";
    $sql .= "WHERE p.prd_category_id = c.category_id ";
    $sql .= "AND p.product_id = " . $product_id . ";";

    return static::find_by_sql($sql)[0];
  }

  public function populate_listings(){
    $this->inventory_listings = VendorInventory::find_by_product($this->product_id);
  }

  public function filter_listings_by_date($given_date){
    $unfiltered_listings = $this->inventory_listings;
    $filtered_listings = VendorInventory::filter_vendors_by_date($unfiltered_listings, $given_date);
    $this->inventory_listings = $filtered_listings;
  }

  static public function sort_into_categories($product_array){
    $sorted_product_array = [];

    foreach($product_array as $product){
      $sorted_product_array[$product->category_name][] = $product;
    }

    return $sorted_product_array;
  }



  // RENDERING FUNCTIONS ==============================================

  static public function get_categories_list($sorted_product_array){
    $category_list = [];

    foreach($sorted_product_array as $category_name => $products){
      $category_list[$category_name] = count($products);
    }

    return $category_list;
  }

  static public function create_product_list($sorted_product_array){

    foreach($sorted_product_array as $category_name => $products){
      echo "<div>";
      echo "<h4>" . $category_name . "</h4>";
      foreach($products as $product){
        echo '<a href="' . url_for('/products/show.php') . '?id=' . $product->product_id . '">';
        echo "<div>";
        echo "<p>" . $product->product_name . "</p>";
        echo "<p>" . count($product->inventory_listings) . " listings</p>";
        echo "</div>"; 
        // IMAGE GOES HERE
        echo "</a>"; // End product
      } // End product loop
      echo "</div>"; // End category div
    } // End category loop
  } // End create_product_list()

  static public function create_datalist($sorted_product_array){
    foreach($sorted_product_array as $category_name => $products){
      foreach($products as $product){
        echo '<option value="' . $product->product_name . '"></option>';
      }
    }
  }

  static public function create_category_datalist(){
    $category_list = static::find_all_categories();

    foreach($category_list as $category_id => $category_name){
      echo '<option value="' . $category_name . '"></option>';
    }
  }
}

?>
