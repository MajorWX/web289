<?php

class Product extends DatabaseObject {

  static protected $table_name = 'products';
  static protected $db_columns = ['product_id', 'prd_category_id', 'product_name'];

  /**
   * The product's id as it appears in the products table.
   */
  public $product_id;
  /**
   * The product's categories' id as it appears in the products table.
   */
  public $prd_category_id;
  /**
   * The product's name as it appears in the products table.
   */
  public $product_name;

  /**
   * The product's category as it appears in the product_categories table.
   */
  public $category_name;
  /**
   * A list of vendor_inventory listings that involve this product.
   */
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

  /**
   * Queries the product_categories table and gets a list of all categories and returns them as an associative array. 1 Query
   * 
   * @return string[] an associative array with keys of each category_id and values of each category_name
   */
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
    asort($category_list);
    return $category_list;
  } // End find_all_categories()

  /**
   * Queries the products and product_categories tables and returns a list of all Products. 1 Query
   * 
   * @return Product[] a list of all products, with populated category names.
   */
  static public function find_all_products() {
    $sql = "SELECT p.product_id, p.prd_category_id, c.category_name, p.product_name ";
    $sql .= "FROM products p, product_categories c ";
    $sql .= "WHERE p.prd_category_id = c.category_id;";

    return static::find_by_sql($sql);
  }

  /**
   * Queries the database and creates a Product object based on a given product_id. 1 Query
   * 
   * @param int $product_id the product's id as it appears in the products table.
   * 
   * @return Product|false the product object with the given id, if it exists.
   */
  static public function find_by_id($product_id) {
    $sql = "SELECT p.product_id, p.prd_category_id, c.category_name, p.product_name ";
    $sql .= "FROM products p, product_categories c ";
    $sql .= "WHERE p.prd_category_id = c.category_id ";
    $sql .= "AND p.product_id = '" . $product_id . "';";

    $result = static::find_by_sql($sql);
    if($result){
      return $result[0];
    } else {
      return false;
    }
  }

  /**
   * Populates this Product object's inventory_listings attribute. N+1 Queries
   */
  public function populate_listings(){
    $this->inventory_listings = VendorInventory::find_by_product($this->product_id);
  }

  /**
   * Filters this Product object's inventory_listings attribute to only include listings involving vendors marked as attending a given date. N Queries
   */
  public function filter_listings_by_date($given_date){
    if(!$this->inventory_listings) { return;}

    $unfiltered_listings = $this->inventory_listings;
    $filtered_listings = VendorInventory::filter_vendors_by_date($unfiltered_listings, $given_date);
    $this->inventory_listings = $filtered_listings;
  }

  /**
   * Populates a given list of Product objects' inventory_listings attributes to only include a given date. N+1 Queries
   * @param Product[] $product_array an unsorted list of Product objects, without populated inventory_listings
   * @param CalendarDate $given_date the given date to filter listings for
   * 
   * @return Product[] the populated list of Product objects
   */
  static public function populate_listings_by_date($product_array, $given_date){
    // Getting all vendor ids marked as attending this date
    $vendor_ids = $given_date->get_vendor_ids(); // +1 Query

    // If there are no vendors attending this date, don't populate the list
    if(!$vendor_ids){
      return $product_array;
    }

    // Creating a populated list to return
    $populated_products = [];

    // Going through each product and populating it
    foreach($product_array as $product) {
      $product->inventory_listings = VendorInventory::find_by_product_with_wl($product->product_id, $vendor_ids); // +N Queries
      $populated_products[] = $product;
    }

    return $populated_products;
  }

  /**
   * Creates a new category in the product_categories table. 1 Query
   * 
   * @param string $category_name the name of the category to be created.
   * 
   * @return mysqli_result|bool the query result
   */
  static public function create_category($category_name) {
    $category_name = h($category_name);

    $sql = "INSERT INTO product_categories (";
    $sql .= "category_name) VALUES ('";
    $sql .= $category_name . "')";

    $result = self::$database->query($sql);
    if($result) {
      return $result;
    } else {
      return false;
    }
  }

  /**
   * Stores this product in the products table.
   * 
   * @return mysqli_result|bool the query result
   */
  public function save() {
    // A new record will not have an ID yet
    if(isset($this->product_id)) {
      return $this->update();
    } else {
      return $this->create();
    }
  }

  /**
   * Updates an existing row in the products table with this product's information.
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
    $sql .= " WHERE product_id='" . self::$database->escape_string($this->product_id) . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }

  /**
   * Deletes this product from the products table, and any vendor inventory listings that use this product. 1 Query
   * 
   * @return mysqli_result|bool the query result
   */
  public function delete() {
    $sql = "DELETE FROM " . static::$table_name . " ";
    $sql .= "WHERE product_id='" . self::$database->escape_string($this->product_id) . "' ";
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


  // RENDERING FUNCTIONS ==============================================

  /**
   * Turns an unsorted list of Product objects and sorts them into an associative array keyed with category_names.
   * 
   * @param Product[] $product_array an unsorted list of Product objects
   * 
   * @return Product[][] an associative array, [category_name][simple list of Products with that category]
   */
  static public function sort_into_categories($product_array){
    $sorted_product_array = [];

    foreach($product_array as $product){
      $sorted_product_array[$product->category_name][] = $product;
    }

    ksort($sorted_product_array);
    return $sorted_product_array;
  }

  /**
   * Reads a sorted associative array of Product objects and creates an associative array of categories from them with the number of products in each category.
   * 
   * @param Product[][] $sorted_product_array an associative array from the static sort_into_categories() function
   * 
   * @return int[] an associative array Keys: category_names. Values: counts of how many products are in a given category
   */
  static public function get_categories_list($sorted_product_array){
    $category_list = [];

    foreach($sorted_product_array as $category_name => $products){
      $category_list[$category_name] = count($products);
    }

    return $category_list;
  }

  /**
   * Displays the products by printing HTML content to the page, displayed under the category headings.
   * 
   * @param Product[][] $sorted_product_array an associative array from the static sort_into_categories() function
   */
  static public function create_product_list($sorted_product_array){

    foreach($sorted_product_array as $category_name => $products){
      echo "<div>";
      echo "<h4>" . $category_name . "</h4>";
      foreach($products as $product){
        echo '<a href="' . url_for('/products/show.php' . '?id=' . $product->product_id) . '">';
        echo "<div>";
        echo "<p>" . $product->product_name . "</p>";
        $listing_count = ($product->inventory_listings) ? count($product->inventory_listings) : 0;
        echo "<p>" . $listing_count . " listings</p>";
        echo "</div>"; 
        // IMAGE GOES HERE
        echo "</a>"; // End product
      } // End product loop
      echo "</div>"; // End category div
    } // End category loop
  } // End create_product_list()

  /**
   * Prints all products as option tags for a datalist.
   * 
   * @param Product[][] $sorted_product_array an associative array from the static sort_into_categories() function
   */
  static public function create_datalist($sorted_product_array){
    foreach($sorted_product_array as $category_name => $products){
      foreach($products as $product){
        echo '<option value="' . $product->product_name . '"></option>';
      }
    }
  }

  /**
   * Prints all categories as option tags for a datalist. 1 Query
   */
  static public function create_category_datalist(){
    $category_list = static::find_all_categories();

    foreach($category_list as $category_id => $category_name){
      echo '<option value="'. $category_id .'">' . $category_name . '</option>';
    }
  }

  /**
   * Turns an unsorted list of Product objects and sorts them into an associative array keyed with product_name.
   * 
   * @param Product[] $product_array an unsorted list of Product objects
   * 
   * @return Product[] an array of Product objects with their keys equal to their product_name
   */
  static public function sort_by_product_name($product_array) {
    $sorted_by_name = [];

    // Going through each product and assigning it to an associative array with its product name as the key
    foreach($product_array as $product) {
      $sorted_by_name[$product->product_name] = $product;
    }

    // Returning the sorted associative array
    return $sorted_by_name;
  }


  /**
   * Displays the products by printing an HTML table to the page, with CRUD links included.
   * 
   * @param Product[][] $sorted_product_array an associative array from the static sort_into_categories() function
   */
  static public function create_admin_crud_table($sorted_product_array) {

    // Create initial table
    echo "<table>";
    echo "<tr>";
    echo "<th>Product Name</th>";
    echo "<th>Number of Listings</th>";
    // Show Product Details
    echo "<th></th>";
    // Edit Product
    echo "<th></th>";
    // Delete Product
    echo "<th></th>";
    echo "</tr>";

    // Loop for categories
    foreach($sorted_product_array as $category => $products) {
      echo "<tr>";
      echo '<td class="product-category" colspan="5">' . $category . '</td>';
      echo "</tr>";

      // Loop for each listing
      foreach($products as $product) {
        echo "<tr>";
        // Product Name
        echo "<td>" . $product->product_name . "</td>";
        // Number of Listings
        echo "<td>" . (($product->inventory_listings) ? count($product->inventory_listings) : 0) . "</td>";
        // Link to details
        echo '<td><a href="' . url_for('/products/show.php' . '?id=' . $product->product_id) . '">Details</a></td>';
        // Link to edit product
        echo '<td><a href="' . url_for('/products/edit.php' . '?id=' . $product->product_id) . '">Edit</a></td>';
        // Link to delete product
        echo '<td><a href="' . url_for('/products/delete.php' . '?id=' . $product->product_id) . '">Delete</a></td>';
        echo "</tr>";
      } // End loop for each product
    } // End loop for each category
    echo "</table>";
  } // End create_admin_crud_table()

  /**
   * Prints all categories as option tags for a datalist, uses the given product to select one of the categories. 1 Query
   * 
   * @param Product $product the existing product to select the category of
   */
  static public function create_category_datalist_edit($product){
    $category_list = static::find_all_categories();

    foreach($category_list as $category_id => $category_name){
      echo '<option value="'. $category_id . '"' .
      (($product->prd_category_id == $category_id) ? ' selected' : '')
      . '>' . $category_name . '</option>';
    }
  }



} // End Product Class

?>
