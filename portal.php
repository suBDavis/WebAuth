<?php

include 'creds.php';

$con = mysqli_connect("localhost",$sql_user,$sql_password,$sql_db);
// Check connection
if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();}

//do login info here.
//On success, set a random cookie and store it in the user table
//On failure, deflect back to the index page.

if (isset($_COOKIE['auth'])){

  $sql = "SELECT * FROM `users` WHERE `cookie` LIKE " . $_COOKIE['auth'];

  echo("cookie found");

} else {

  $sql = "SELECT * FROM `users` WHERE `name` LIKE " . $_POST['mc_username'];
  $result = $conn->query($sql);
  $row = $result->fetch_assoc()

  if ( hash('sha256' , $_POST['pass1']) == $row['pass']){
    $c_value =  hash('sha256', time());
    //on success
    setcookie('auth', $c_value);
    echo("success");
  }

  echo ("no cookie");
}

?>
