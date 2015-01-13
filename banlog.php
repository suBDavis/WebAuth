<?php
  $ban_update = "Insert New";
  $ban_staff = $player;
  if ($isStaff){
    if (isset($_GET['entry'])){
      $sql = "SELECT * FROM `webauth`.`banlog` WHERE `banlog`.`index` = '".$_GET['entry']."';";
      $result = mysqli_query($con, $sql);
      $row = mysqli_fetch_assoc($result);
      $ban_name = $row['name'];
      $ban_offense = $row['offense'];
      $ban_staff = $row['staff'];
      $ban_action = $row['action'];
      $ban_update = "Update";
      $ban_index = $row['index'];
    }
    if (isset($_POST['ban_name'])){
    	if ($_GET['type'] == "Update"){
	      $sql = "UPDATE `webauth`.`banlog` SET `name` = '".$_POST['ban_name']."' , `staff` = '".$_POST['ban_by']."' , `offense` = '".$_POST['ban_offense']."' , `action` = '".$_POST['ban_action']."' WHERE `banlog`.`index` = '".$_POST['ban_index']."';";
	  	  $result = mysqli_query($con, $sql);
	      $url = $root_dir."/portal.php?tab=banlog";
	      //header( "Location: " . $url ) ;
	  } else{
	      $sql = "INSERT INTO `webauth`.`banlog` (`name`, `staff`, `offense`,`action`) VALUES ('".$_POST['ban_name']."' ,'".$_POST['ban_by']."' ,'".$_POST['ban_offense']."' ,'".$_POST['ban_action']."');";
	      $result = mysqli_query($con, $sql);
	      $url = $root_dir."/portal.php?tab=banlog";
	      header( "Location: " . $url ) ;
	  }
    }
  }
?>
<div class='col-sm-10' style='padding-top:15px;'>
  <?php if ($isStaff){ echo "
  <div class='row'>
    <div class='col-sm-4'>
      <form role='form' action='?tab=banlog&type=".$ban_update."' method='post'>
        <div class='input-group'>
          <span class='input-group-addon'>Name</span>
          <input type='text' class='form-control' name='ban_name' value='".$ban_name."'>
        </div>
        <div class='input-group'>
          <span class='input-group-addon'>Offense</span>
          <textarea rows='5' type='text' class='form-control' name='ban_offense'>".$ban_offense."</textarea>
        </div>
      </div>
      <div class='col-sm-4'>
        <div class='input-group'>
          <span class='input-group-addon'>Staff</span>
          <input type='text' class='form-control' name='ban_by' value='".$ban_staff."'>
        </div>
        <div class='input-group'>
          <span class='input-group-addon'>Action</span>
          <input type='text' class='form-control' name='ban_action' value='".$ban_action."'>
        </div>
        <input style='display:none;' type='text' name='ban_index' value='".$ban_index."'>
        <input type='submit' value='".$ban_update."' class='btn btn-default' style='margin-top:20px;'/>
        <a class='btn btn-default' style='margin-top:20px;' href='?tab=banlog'>Clear</a>
        <h4>Search: [CTRL] + F</h4>
        </form>
      </div>
    </div>
    "; }?>
  <div class='row'>
    <div class='col-sm-12' style='padding-top:15px;'>
      <table class='table table-striped'>
        <col width='10%'>
        <col width='10%'>
        <col width='60%'>
        <col width='10%'>
        <col width='10%'>
        <tr>
          <th>Name</th>
          <th>Staff Issuer</th>
          <th>Offense</th>
          <th>Action</th>
          <th>Time</th>
          <?php echo $isStaff ? "<th>Edit</th>" : ""; ?>
        </tr>
        <?php
        $sql = "SELECT * FROM `banlog` ORDER BY `timestamp` DESC";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result)){
          $trclass;
        switch($row['action']){
          case 'ban':
            $trclass= "class='danger'";
            break;
          case 'warning':
            $trclass= "class='warning'";
            break;
          case 'pardon':
            $trclass= "class='success'";
            break;
          default:
            $trclass= "";
            break;
        }
        echo
          "<tr ".$trclass.">
            <td>".$row['name']."</td>
            <td>".$row['staff']."</td>
            <td>".$row['offense']."</td>
            <td>".$row['action']."</td>
            <td>".$row['timestamp']."</td>";
            echo $isStaff ? "<td><a class='btn btn-warning' href='?tab=banlog&entry=".$row['index']."'>Edit</a></td>" : "";
        echo"</tr>";
        }
        ?>
      </table>
    </div>
  </div>
</div>
