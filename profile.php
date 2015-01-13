
  <div class='col-sm-10' style='padding-top:20px;'>
    <div class='row'>
      <div class='col-sm-5' style='margin-left:15px; margin-right:15px;min-width: 400px;'>
        <form role='form' action='?tab=profile' method='post'>
          <div class='input-group'>
            <span class='input-group-addon'>Email Address</span>
            <input type='text' class='form-control' id='prof_email' name='prof_email' value="<?php echo $email; ?>">
            <span class='input-group-btn'>
              <input type='submit' value='Update' class='btn btn-default'/>
            </span>
          </div>
        </form>

        <form role='form' action='?tab=profile' method='post'>
          <div class='input-group'>
            <span class='input-group-addon'>Skype Name</span>
            <input type='text' class='form-control' id='prof_skype' name='prof_skype' value="<?php echo $skype; ?>">
            <span class='input-group-btn'>
              <input type='submit' value='Update' class='btn btn-default'/>
            </span>
          </div>
        </form>
        <h3>Privacy Info</h3>
        <p>Your Skype name WILL be displayed to OTHER registered users.</p>
        <p>Your Eamail WILL NOT be displayed, but staff may contact you.</p>
        <p>Your birthday WIL NOT be displayed, and you will get a special gift in-game on your birthday.</p>
        <?php
          if (isset($_GET['update'])){
            echo "<img src='".$root_dir."/images/success.png'/>";
          }
        ?>
      </div>
      <div class='col-sm-1' style='min-width:100px;'>
        <table class='proftable'>
          <tr><td valign='top'>
            <img src='http://achievecraft.com/tools/skin.php?name=<?php echo $player; ?>&size=256'/>
          </td>
          <td valign='top'>

          </td></tr>
        </table>
      </div>
    </div>
  </div>

<?php
  if(isset($_POST['prof_email'])){
      $sql = "UPDATE `webauth`.`users` SET `email` = '".$_POST['prof_email']."' WHERE `users`.`uuid` = '".$uuid."';";
      $result = mysqli_query($con, $sql);
      $url = $root_dir."/portal.php?tab=profile&update=success";
      header( "Location: " . $url ) ;
  }
  if(isset($_POST['prof_skype'])){
      $sql = "UPDATE `webauth`.`users` SET `skype` = '".$_POST['prof_skype']."' WHERE `users`.`uuid` = '".$uuid."';";
      $result = mysqli_query($con, $sql);
      $url = $root_dir."/portal.php?tab=profile&update=success";
      header( "Location: " . $url ) ;
  }

?>