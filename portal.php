<?php

include 'creds.php';

$con = mysqli_connect("localhost",$sql_user,$sql_password,$sql_db);
// Check connection
if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();}

//do login info here.
//On success, set a random cookie and store it in the user table
//On failure, deflect back to the index page.
$auth = $_COOKIE["auth"];
if (isset($_POST['mc_pass'])){
  $mc_username = $_POST["mc_username"];
  $mc_pass = $_POST["mc_pass"];
}
//after auth, these are the variables that I can use on the page.
$player;
$uuid;
$isStaff;
$email;
$skype;

if (isset($_COOKIE['auth'])){

  //find the value of the latest known cookie
  $sql = "SELECT * FROM `users` WHERE `cookie` LIKE '" . $auth . "'";
  $result = mysqli_query($con, $sql);
  $row = mysqli_fetch_assoc($result);
  if (mysqli_num_rows($result) == 1){

      //you are already logged in.  Whatever homie.
      $player = $row['name'];
      $uuid = $row['uuid'];
      $isStaff = $row['other'] == 1 ? TRUE : FALSE;
      $email = $row['email'];
      $skype = $row['skype'];

  } else {
    unsetCookie();
    kickBack();
  }
} else if (isset($mc_username)) {

  $sql = "SELECT * FROM `users` WHERE `name` LIKE '" . $mc_username . "'";
  $result = mysqli_query($con, $sql);
  $row = mysqli_fetch_assoc($result);
  if (isset($row['name'])){
    if ( hash('sha256' , $mc_pass) == $row['pass']){
      $c_value =  hash('sha256', time() + rand());
      //on success
      setcookie('auth', $c_value);
      //get the UUID
      $uuid = $row['uuid'];
      $player = $row['name'];
      $isStaff = $row['other'] == 1 ? TRUE : FALSE;
      //save avitar on the webserver.  Update database with link
      $url1 = "https://mctoolbox.net/avatar/".$row['name']."/100";
      $img1 = "avatars/".$row['name'];
      file_put_contents($img1, file_get_contents($url1));
      //set cookie in SQL DB
      $sql = "UPDATE `webauth`.`users` SET `cookie` = '".$c_value."' WHERE `users`.`uuid` = '".$uuid."';";
      $result = mysqli_query($con, $sql);
      //do other things?

    } else {
      //do this if login fails
      kickback();
    }
  } else{ kickback(); }
} else {
  //do this if cookie doesnt match
  unsetCookie();
  //kick back to login
  kickback();
}

switch($_GET['tab']){
  case 'exit':
    unsetCookie();
    kickback();
    break;
  default:
    $tab = $_GET['tab'];
}

// function getUUID($username){
//
//   $curl = curl_init();
//   // Set some options - we are passing in a useragent too here
//   curl_setopt_array($curl, array(
//     CURLOPT_RETURNTRANSFER => 1,
//     CURLOPT_URL => "https://api.mojang.com/users/profiles/minecraft/" . $username,
//     CURLOPT_USERAGENT => 'Codular Sample cURL Request'
//   ));
//   // Send the request & save response to $resp
//   $resp = curl_exec($curl);
//   // Close request to clear up some resources
//   curl_close($curl);
//
//   $json_a = json_decode($resp,true);
//   return $json_a['id'];
//
// }
function unsetCookie(){
  unset($_COOKIE['auth']);
  setcookie('auth', '', time() - 3600); // empty value and old timestamp
}
function kickback(){
  include 'creds.php';
  header("Location: " . $root_dir );
  die();
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
        <div class='col-sm-4'>
          <h2>Hello there, <?php echo $player;?><small> [<?php echo $isStaff ? "Staff" : "Member";?>]</small></h2>
          <h4>UUID: <?php echo $uuid;?></h4>
        </div>
        <div class='col-sm-5' style ="padding-top:22px;">
          <ul class="nav nav-pills">
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
      case 'profile':
        echo "
        <div class='row'>
        <div class='col-sm-10 col-sm-offset-1' style='padding-top:20px;'>
        <div class='row'>
        <div class='col-sm-5' style='margin-left:15px; margin-right:15px;'>
        <form role='form' action='?tab=profile' method='post'>
        <div class='input-group'>
        <span class='input-group-addon'>Email Address</span>
        <input type='text' class='form-control' id='prof_email' name='prof_email' placeholder=".$email.">
        <span class='input-group-btn'>
        <input type='submit' value='Update' class='btn btn-default'/>
        </span>
        </div>
        </form>

        <form role='form' action='?tab=profile' method='post'>
        <div class='input-group'>
        <span class='input-group-addon'>Skype Name</span>
        <input type='text' class='form-control' id='prof_skype' name='prof_skype' placeholder=".$skype.">
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
        <img src='http://achievecraft.com/tools/skin.php?name=".$player."&size=256'/>
        </td>
        <td valign='top'>

        </td></tr>
        </table>
        </div>
        </div>
        </div>
        </div>
        ";

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
              <col width='50%'>
              <col width='10%'>
              <col width='20%'>
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
