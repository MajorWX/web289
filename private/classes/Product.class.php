<?php

class Product extends DatabaseObject {

  static protected $table_name = 'products';
  static protected $db_columns = ['product_id', 'prd_category_id', 'product_name'];

  public $product_id;
  public $prd_category_id;
  public $product_name;

  public $category_name;
  public $inventory_listings = [];

  // SQL Functions ==========================================================

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

    return static::find_by_sql($sql);
  }


  static public function sort_into_categories($product_array){
    $sorted_product_array = [];

    foreach($product_array as $product){
      $sorted_product_array[$product->category_name][] = $product;
    }

    return $sorted_product_array;
  }


}




?>
