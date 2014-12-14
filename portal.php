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

if (isset($_COOKIE['auth'])){

  $sql = "SELECT * FROM `users` WHERE `cookie` LIKE '" . $auth . "'";

  echo("cookie found");

} else {

  $sql = "SELECT * FROM `users` WHERE `name` LIKE '" . $mc_username . "'";
  $result = mysqli_query($con, $sql);
  $row = mysqli_fetch_assoc($result);

  echo $row['pass'];

  if ( hash('sha256' , $mc_pass) == $row['pass']){
    $c_value =  hash('sha256', time());
    //on success
    setcookie('auth', $c_value);
    echo("success");
  }

  echo ("no cookie");
}

?>
