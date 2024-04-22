<?php require_once('../../private/initialize.php'); 

require_admin_login();
$users = User::find_all();

?>


<?php $page_title = 'User List'; ?>
<?php include(SHARED_PATH . '/public_header.php'); ?>


<!-- Begin HTML -->


  <main>

    <h2>User List</h2>
      <table>
        <tr>
          <th>User ID</th>
          <th>Display Name</th>
          <th>Email</th>
          <th>Role</th>
          <th></th>
          <th></th>
          <th></th>
        </tr>

      <?php foreach($users as $user) {?>
          <tr>
            <td><?php echo $user->user_id; ?></td>
            <td><?php echo $user->display_name; ?></td>
            <td><?php echo $user->email; ?></td>
            <td><?php echo $user->role_to_string(); ?></td>
            <td><a href="<?php echo url_for('/users/show.php?id=' . $user->user_id); ?>">View</a></td>
            <td><a href="<?php echo url_for('/users/edit.php?id=' . $user->user_id); ?>">Edit</a></td>
            <td><a href="<?php echo url_for('/users/delete.php?id=' . $user->user_id); ?>">Delete</a></td>
          </tr>
      <?php } ?>
      </table>
  </main>


<!-- End HTML -->


<?php include(SHARED_PATH . '/public_footer.php'); ?>
