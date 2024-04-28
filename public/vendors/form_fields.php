<?php
// prevents this code from being loaded directly in the browser
// or without first setting the necessary object
if(!isset($vendor)) {
  redirect_to(url_for('index.php'));
}
?>

<dl>
  <dt>Vendor Display Name*</dt>
  <dd>
    <label for="display-name">Vendor Display Name*: </label>
    <input type="text" id="display-name" name="vendor[vendor_display_name]" value="<?php echo h($vendor->vendor_display_name); ?>" required>
  </dd>
</dl>

<dl>
  <dt>Vendor Description Blurb</dt>
  <dd>
    <label for="vendor-desc">Vendor Description Blurb: </label>
    <textarea id="vendor-desc" name="vendor[vendor_desc]" rows="4" cols="50"><?php echo h($vendor->vendor_desc); ?></textarea>
  </dd>
</dl>

<dl>
  <dt>Street Address*</dt>
  <dd>
    <label for="address">Street Address*: </label>
    <input type="text" id="address" name="vendor[address]" value="<?php echo h($vendor->address); ?>" required>
  </dd>
</dl>

<dl>
  <dt>City*</dt>
  <dd>
    <label for="city">City*: </label>
    <input type="text" id="city" name="vendor[city]" value="<?php echo h($vendor->city); ?>" required>
  </dd>
</dl>

<dl>
  <dt>State*</dt>
  <dd>
    <label for="state">State*: </label>
    <select id="state" name="vendor[vd_state_id]" required>
    <option value="">Select a state:</option>
      <?php 
        $state_array = Vendor::get_state_array();

        
        foreach($state_array as $state_id => $state_name) {
          if($vendor->vd_state_id == $state_id){
            $selected_string = ' selected';
          } else {
            $selected_string = '';
          }
          echo '<option value="' . $state_id . '"' . $selected_string . '>' . $state_name . '</option>';
        }
      ?>
    </select>
  </dd>
</dl>

<dl>
  <dt>Zip Code*</dt>
  <dd>
    <label for="zip">Zip Code*: </label>
    <input type="text" id="zip" name="vendor[zip]" value="<?php echo h($vendor->zip); ?>" required>
  </dd>
</dl>
