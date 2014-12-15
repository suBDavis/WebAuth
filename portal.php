<?php

include 'creds.php';

$con = mysqli_connect("localhost",$sql_user,$sql_password,$sql_db);
// Check connection
if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();}

//do login info here.
//On success, set a random cookie and store it in the user table
//On failure, deflect back to the index page.
$auth = $_COOKIE["auth"];
$mc_username = $_POST["mc_username"];
$mc_pass = $_POST["mc_pass"];

//after auth, these are the variables that I can use on the page.
$player;
$uuid;
$isStaff;

if (isset($_COOKIE['auth'])){

  //find the value of the latest known cookie
  $sql = "SELECT * FROM `users` WHERE `cookie` LIKE '" . $auth . "'";
  $result = mysqli_query($con, $sql);
  $row = mysqli_fetch_assoc($result);
  if (mysqli_num_rows($result) == 1){

      //you are already logged in.  Whatever homie.
      $player = $row['name'];
      $uuid = getUUID($player);
      $isStaff = $row['other'] == 1 ? TRUE : FALSE;

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
      $uuid = getUUID($mc_username);
      $player = $mc_username;
      $isStaff = $row['other'] == 1 ? TRUE : FALSE;
      echo $uuid;
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

mysqli_close($con);


function getUUID($username){
  $curl = curl_init();
  // Set some options - we are passing in a useragent too here
  curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => "https://api.mojang.com/users/profiles/minecraft/" . $username,
    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
  ));
  // Send the request & save response to $resp
  $resp = curl_exec($curl);
  // Close request to clear up some resources
  curl_close($curl);

  $json_a = json_decode($resp,true);
  return $json_a['id'];
}
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
  </style>
</head>
<body>
  <div class='row'>
    <div class='col-md-12' style='padding-top: 10px; padding-left: 30px;background-color: #D6CA72;'>
      <div class='row'>
        <div class='col-md-6 col-md-offset-1'>
          <h2>Hello there, <?php echo $player;?><small> Status: <?php echo $isStaff ? "Staff" : "Member";?></small></h2>
          <h4>UUID: <?php echo $uuid;?></h4>
        </div>
        <div class='col-md-4' style ="padding-top:30px;">
          <ul class="nav nav-pills">
            <li role="presentation"><a href="#">Member List</a></li>
            <li role="presentation"><a href="#">Profile</a></li>
            <li role="presentation"><a href="#">Messages</a></li>
            <li role="presentation"><a href="#">BanLog</a></li>
          </ul>
        </div>
      </div>
      <div class='row'>
        <div class='col-md-10 col-md-offset-1' style='padding-top: .5cm; padding-left: .5cm;'>

        </div>
      </div>
    </div>
  </div>
</body>
</html>
