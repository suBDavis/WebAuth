<div class='col-sm-10' style='padding-top:20px;'>
  <table class='table table-striped'>
    <tr>
      <th>Avatar</th>
      <th>Name</th>
      <th>Skype</th>
    </tr>
      <?php
      $sql = "SELECT * FROM `users` WHERE `active` = '1'";
      $result = mysqli_query($con, $sql);
      while ($row = mysqli_fetch_array($result)){
        echo
        "<tr>
        <td><img width='40' height = '40' src='avatars/".$row['name']."'/></td>
        <td>".$row['name']."</td>
        <td>".$row['skype']."</td>
        </tr>";
      }
      ?>
  </table>
</div>
