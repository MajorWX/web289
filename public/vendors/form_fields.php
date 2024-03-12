<?php
// prevents this code from being loaded directly in the browser
// or without first setting the necessary object
if(!isset($vendor)) {
  redirect_to(url_for('index.php'));
}
?>

<dl>
  <dt>Vendor Display Name</dt>
  <dd><input type="text" name="vendor[vendor_display_name]" value="<?php echo h($vendor->vendor_display_name); ?>" required/></dd>
</dl>

<dl>
  <dt>Vendor Description Blurb</dt>
  <dd><input type="text" name="vendor[vendor_desc]" value="<?php echo h($vendor->vendor_desc); ?>" /></dd>
</dl>

<dl>
  <dt>Address</dt>
  <dd><input type="text" name="vendor[address]" value="<?php echo h($vendor->address); ?>" /></dd>
</dl>

<dl>
  <dt>City</dt>
  <dd><input type="text" name="vendor[city]" value="<?php echo h($vendor->city); ?>" /></dd>
</dl>

<dl>
  <dt>State</dt>
  <dd>
    <select name="vendor[vd_state_id]">
    <option value="">Select a state:</option>
      <?php 
        $state_array = Vendor::get_state_array();

        foreach($state_array as $state_id => $state_name) {
          if($vendor->vd_state_id === $state_id){
            $selected_string = ' selected';
          } else {
            $selected_string = '';
          }
          echo '<option value="' . $state_id .  $selected_string . '">' . $state_name . '</option>';
        }
      ?>
    </select>
  </dd>
</dl>
