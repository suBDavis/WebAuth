<div class='row'>
  <div class='col-sm-10 col-sm-offset-1' style='padding-top:20px;'>
    <div class='row'>
      <div class='col-sm-5' style='margin-left:15px; margin-right:15px;'>
        <form role='form' action='?tab=profile' method='post'>
          <div class='input-group'>
            <span class='input-group-addon'>Email Address</span>
            <input type='text' class='form-control' id='prof_email' name='prof_email' placeholder="<?php echo $email; ?>">
            <span class='input-group-btn'>
              <input type='submit' value='Update' class='btn btn-default'/>
            </span>
          </div>
        </form>

        <form role='form' action='?tab=profile' method='post'>
          <div class='input-group'>
            <span class='input-group-addon'>Skype Name</span>
            <input type='text' class='form-control' id='prof_skype' name='prof_skype' placeholder="<?php echo $skype; ?>">
            <span class='input-group-btn'>
              <input type='submit' value='Update' class='btn btn-default'/>
            </span>
          </div>
        </form>
        <h3>Privacy Info</h3>
        <p>Your Skype name WILL be displayed to OTHER registered users.</p>
        <p>Your Eamail WILL NOT be displayed, but staff may contact you.</p>
        <p>Your birthday WIL NOT be displayed, and you will get a special gift in-game on your birthday.</p>
      </div>
      <div class='col-sm-5'>
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
</div>
