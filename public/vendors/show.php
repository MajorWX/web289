<?php require_once('../../private/initialize.php'); ?>

<?php 

$id = $_GET['id'];

$vendor = Vendor::populate_full($id);

?>


<?php $page_title = 'Vendor Details: ' . $vendor->vendor_display_name; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>



<!-- Begin HTML -->
<a href="<?php echo url_for('vendors.php')?>">Back to Vendor List</a>

  <main>
    <dl>
      <dt>Vendor Display Name</dt>
      <dd><?php echo $vendor->vendor_display_name?></dd>
      <dt>Description</dt>
      <dd><?php echo $vendor->vendor_desc?></dd>
      <dt>Contact Info</dt>
      <dd><?php echo $vendor->contact_info?></dd>
      <dt>Address</dt>
      <dd><?php echo $vendor->address?></dd>
      <dt>City</dt>
      <dd><?php echo $vendor->city?></dd>
      <dt>State</dt>
      <dd><?php echo $vendor->state?></dd>
      <dt>Phones</dt>
      
      <?php 
        foreach($vendor->phone_numbers as $phone){
          echo "<dd>" . ucwords($phone['phone_type']) . ": 
          (" . substr($phone['phone_number'], 0, 3) . ") " .
          substr($phone['phone_number'], 3, 3) . "-" .
          substr($phone['phone_number'], 6, 4) . "</dd>";
        }
      ?>

      <dt>Vendor Inventory</dt>
      <dd>
        <?php 
        $sorted_inventory_array = VendorInventory::sort_into_categories($vendor->vendor_inventory);
        VendorInventory::create_products_table($sorted_inventory_array);
        ?>
      </dd>

      <dt>Upcoming Market Days</dt>
      <dd><ul>
        <?php 
          foreach($vendor->listed_dates as $listed_date){
            echo "<li>" . $listed_date->print_date() . "</li>";
          }
        ?>
      </ul></dd>
    </dl>
  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
