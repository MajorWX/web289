<?php require_once('../../private/initialize.php'); ?>
<?php 

  $id = h($_GET['id']);

  // Making sure there is a get value for the id
  if(!isset($_GET['id'])) {
    $session->message('Failed to load page, no vendor_id provided.');
    redirect_to(url_for('index.php'));
  }

  // Checking to make sure only users logged in as this vendor can access this page, unless they are an admin
  if($id != $session->active_vendor_id && !$session->is_admin_logged_in()){
    $session->message('You must be logged in as this vendor to edit them.');
    redirect_to(url_for('index.php'));
  }
  

  // Find the vendor using id
  $vendor = Vendor::populate_full($id);

  // If the vendor object hasn't been made, redirect
  if(!$vendor) {
    $session->message('Could not find a vendor with a vendor_id of ' . $id);
    redirect_to(url_for('index.php'));
  }

  // Checking for Post Request
  if(is_post_request()) {

    // Create record using post parameters
    $args = $_POST['vendor'];
    $vendor->merge_attributes($args);
    $result = $vendor->save();

    if($result) {
      $session->message('Modified vendor: "' . $vendor->vendor_display_name . '" successfully.');
      redirect_to(url_for('/vendors/user_view.php?id=' . $id));
    } else {
      // show errors
    }

  } else {
    // Display form
  }
  
  

  
?>

<?php $page_title = 'Edit Vendor'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->
  <script src="<?php echo url_for('/js/vendor_add_phones.js'); ?>" defer></script>

  <main>
    <a href="<?php echo url_for('/vendors/user_view.php?id=' . h(u($id)));?>">Back to Vendor User Page</a>

    <h2>Edit Vendor: <?php echo $vendor->vendor_display_name; ?></h2>

    <?php echo display_errors($vendor->errors); ?>

    <form action="<?php echo url_for('/vendors/edit.php?id=' . h(u($id))); ?>" method="post">

      <?php include('form_fields.php'); ?>

      <dl>
        <dt>Existing Phones</dt>
        <?php 
          foreach($vendor->phone_numbers as $phone_id => $phone_attributes){
            // Phone Number
            echo '<dd>';
            echo '<span>Phone Number: </span>';
            echo '<input type="text" name="vendor[phone_numbers][' . $phone_id . "][phone_number]" . '" value="' . Vendor::phone_to_string($phone_attributes['phone_number']) . '">';

            // Phone Type
            echo ' <span>Phone Type: </span>';
            echo '<select name="vendor[phone_numbers][' . $phone_id . "][phone_type]" . '">';
            echo '<option value="">Select a phone type:</option>';

            $phone_type = $phone_attributes['phone_type'];

            echo '<option value="home"';
            if($phone_type == 'home'){
              echo ' selected';
            }
            echo '>Home</option>';

            echo '<option value="mobile"';
            if($phone_type == 'mobile'){
              echo ' selected';
            }
            echo '>Mobile</option>';

            echo '<option value="work"';
            if($phone_type == 'work'){
              echo ' selected';
            }
            echo '>Work</option>';

            echo '</select>';

            // Deletion
            echo '</dd>';
          }
        ?>

        
      </dl>

      <dl class="new-phones">
        <dt>New Phones</dt>
        <a>Click to add a phone.</a>
      </dl>

      <input type="submit" value="Edit Vendor">
    </form>
  </main>


<!-- End HTML -->

<?php include(SHARED_PATH . '/public_footer.php'); ?>
