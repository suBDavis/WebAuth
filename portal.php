<?php

include 'creds.php';
include 'sql.php';

$con = mysqli_connect("localhost",$sql_user,$sql_password,$sql_db);
// Check connection
if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();}

//after auth, these are the variables that I can use on the page.
  $player;
  $uuid;
  $isStaff;
  $email;
  $skype;
  $tab = $_GET['tab'];

if (isset($_COOKIE['auth'])){
  doAuth($_COOKIE["auth"]);
} else if (isset($_POST["mc_username"])) {
  doNewAuth($_POST["mc_username"], $_POST["mc_pass"]);
} else {
  //do this if user attempts to access the page without cookie or login form
  unsetCookie();
  //kick back to login
  kickback();
}

?>
<html>
<head>
  <link href='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css' rel='stylesheet'>
  <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
  <script src='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js'></script>
  <style>
  .nav-pills > li > a {
    font-size: 14px;
    color: #37404e;
    border-radius: 10px 10px 10px 10px;
    padding: 15px;
  }
  /*.nav-pills {
    text-align: right;
  }*/
  .input-group {
    margin-top:20px;
  }
  .row{
    margin-left: 0;
    margin-right: 0;
  }
  .proftable > td {
    padding: 10px 10px;
  }
  </style>
</head>
<body>
  <div class='row'>
    <div class='col-sm-12' style='padding-top: 10px; padding-left: 30px;padding-bottom:15px;background-color: #D6CA72;'>
      <div class='row'>
        <div class='col-sm-1 hidden-sm hidden-md'>
        </div>
        <div class='col-sm-1' style="min-width: 110px;">
          <img src='avatars/<?php echo $player;?>'/>
        </div>
        <div class='col-sm-5'>
          <h2>Hello there, <?php echo $player;?><small> [<?php echo $isStaff ? "Staff" : "Member";?>]</small></h2>
          <h4>UUID: <?php echo $uuid;?></h4>
        </div>
        <div class='col-sm-5' style ="padding-top:22px; align-items: left;">
          <ul class="nav nav-pills" style='align-items:left;'>
            <li role="presentation"><a href="?tab=list">Member List</a></li>
            <li role="presentation"><a href="?tab=profile">Profile</a></li>
            <li role="presentation"><a href="?tab=mail">Mail<span class="badge">4</span></a></li>
            <li role="presentation"><a href="?tab=banlog">BanLog</a></li>
            <?php if($isStaff){echo"<li role='presentation'><a href='?tab=staff'>Staff</a></li>";}?>
            <li role="presentation"><a href="?tab=exit">Sign Out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <?php
    //do SQL for getting user profile
    switch ($tab){
      case 'exit':
        unsetCookie();
        kickback();
      break;
      case 'profile':
        //include the HTML page with variables rendered.
        include 'profile.php';

        if(isset($_POST['prof_email'])){
          if($_POST['prof_email'] == ""){}
            else{
              $sql = "UPDATE `webauth`.`users` SET `email` = '".$_POST['prof_email']."' WHERE `users`.`uuid` = '".$uuid."';";
              $result = mysqli_query($con, $sql);
              $url = $root_dir."/portal.php?tab=profile";
              header( "Location: " . $url ) ;
            }
          }
        if(isset($_POST['prof_skype'])){
          if($_POST['prof_skype'] == ""){}
            else{
              $sql = "UPDATE `webauth`.`users` SET `skype` = '".$_POST['prof_skype']."' WHERE `users`.`uuid` = '".$uuid."';";
              $result = mysqli_query($con, $sql);
              $url = $root_dir."/portal.php?tab=profile";
              header( "Location: " . $url ) ;
            }
          }
      break;
      case 'list':
        echo "
          <div class='row'>
          <div class='col-sm-10 col-sm-offset-1' style='padding-top:20px;'>
          <div class='row'>
          <div class='col-sm-8 col-sm-offset-2'>
          <table class='table table-striped'>
          <tr>
          <th>Avatar</th>
          <th>Name</th>
          <th>Skype</th>
          </tr>";
        //do loop for filling table
        $sql = "SELECT * FROM `users` WHERE `active` = '1'";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_array($result)){
          echo
          "<tr>
            <td><img width='50' height = '50' src='avatars/".$row['name']."'/></td>
            <td>".$row['name']."</td>
            <td>".$row['skype']."</td>
          </tr>";
        }
        echo "
          </table>
          </div>
          </div>
          </div>
          </div>
        ";
      break;
      case 'banlog':
      echo "
      <div class='row'>
        <div class='col-sm-10 col-sm-offset-1' style='padding-top:20px;'>
          <div class='row'>
            <div class='col-sm-10 col-sm-offset-1'>
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
                </tr>";
                //do loop for filling table
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
                      <td>".$row['timestamp']."</td>
                    </tr>";
                }
              echo "
              </table>
            </div>
          </div>
        </div>
      </div>
      ";
      break;
      case 'staff':
        if ($isStaff){
          echo "
          <div class='row'>
          <div class='col-sm-10 col-sm-offset-1' style='padding-top:20px;'>
          <div class='row'>
          <div class='col-sm-10 col-sm-offset-1'>
          <table class='table table-striped'>
          <tr>
          <th>Name</th>
          <th width='110'>Staff Issuer</th>
          <th>Offense</th>
          <th>Action</th>
          <th>Time</th>
          </tr>";
          //do loop for filling table
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
            <td>".$row['timestamp']."</td>
            </tr>";
          }
          echo "
          </table>
          </div>
          </div>
          </div>
          </div>
          ";
        } else echo "Nice Try.";
      break;
    }
  ?>
</body>
</html>
<?php mysqli_close($con);?>
