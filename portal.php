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

  //find the value of the latest known cookie
  $sql = "SELECT * FROM `users` WHERE `cookie` LIKE '" . $auth . "'";
  $result = mysqli_query($con, $sql);
  $row = mysqli_fetch_assoc($result);

  if ( $auth == $row['cookie']){
    //you are already logged in.  Whatever homie.
  } else {
    //do this if cookie doesnt match
    setcookie('auth', '0');
    //kick back to login
    header('/');
  }

} else {

  $sql = "SELECT * FROM `users` WHERE `name` LIKE '" . $mc_username . "'";
  $result = mysqli_query($con, $sql);
  $row = mysqli_fetch_assoc($result);

  if ( hash('sha256' , $mc_pass) == $row['pass']){
    $c_value =  hash('sha256', time());
    //on success
    setcookie('auth', $c_value);
    //set cookie in SQL DB
    $sql = "UPDATE `webauth`.`users` SET `cookie` = \'text2\' WHERE `users`.`index` = 2;";
    //do other things
    echo("success");
  } else {
    //do this if login fails
    header('/');
  }
}

?>
