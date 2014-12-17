<?php

include 'creds.php';

$con = mysqli_connect("localhost",$sql_user,$sql_password,$sql_db);
// Check connection
if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();}

//On success, set a random cookie and store it in the user table
//On failure, deflect back to the index page.
function doAuth($cookie){
  //these are the globals I need
  global $player, $uuid, $isStaff, $email, $skype, $con;
  //find the value of the latest known cookie
  $sql = "SELECT * FROM `users` WHERE `cookie` LIKE '" . $cookie . "'";
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
}
function doNewAuth($mc_username, $mc_pass){
  //globals I need
  global $player, $uuid, $isStaff, $con;
  //for new users and useres without the auth cookie
  $sql = "SELECT * FROM `users` WHERE `name` LIKE '" . $mc_username . "'";
  $result = mysqli_query($con, $sql);
  $row = mysqli_fetch_assoc($result);
  if (isset($row['name'])){
    if ( hash('sha256' , $mc_pass) == $row['pass']){
      $c_value =  hash('sha256', time() + rand());
      //on success
      setcookie('auth', $c_value);
      //assign globals back to portal
      global $player, $uuid, $isStaff;
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
      echo "this";
      //kickback();
      }
  } else {
    //do this if there is no known user in the database.
    //kickback();
    echo "that";
    }
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
