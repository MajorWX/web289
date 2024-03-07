<?php

if(!isset($product)) {
  redirect_to(url_for('products.php'));
}
$products = Product::find_all_products();
$sorted_product_array = Product::sort_into_categories($products);



?>

<dl>

  <dt>Product Name</dt>
  <dd>
    <input type="text" name="product[product_name]" value="<?php echo $product->product_name?>" list="product-suggestions">
      <datalist id="product-suggestions">
        <!-- Populate each <option></option> with php-->
          <?php 
            Product::create_datalist($sorted_product_array);
          ?>
      </datalist>
  </dd>

  <dt>Category</dt>
  <dd>
    <input type="text" name="product[category_name]" value="<?php echo $product->category_name?>" list="category-suggestions">
    <datalist id="category-suggestions">
      <?php  Product::create_category_datalist(); ?>
    </datalist>
  </dd>

  <!-- Add listing price and  -->

</dl>