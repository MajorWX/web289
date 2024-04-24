<?php require_once('../../private/initialize.php');

require_admin_login();

$images = Image::find_all();


?>

<?php $page_title = 'Image List'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>

<!-- Begin HTML -->

<main>
  <h2></h2>


</main>

<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
