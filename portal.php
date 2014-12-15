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
  if (mysql_num_rows($result)>0){
    if ( hash('sha256' , $mc_pass) == $row['pass']){
      $c_value =  hash('sha256', time());
      //on success
      setcookie('auth', $c_value);
      //get the UUID
      $uuid = getUUID($mc_username);
      //set cookie in SQL DB
      $sql = "UPDATE `webauth`.`users` SET `cookie` = '".$c_value."' WHERE `users`.`uuid` = '".$uuid."';";
      //do other things
      echo("success");
    } else {
      //do this if login fails
      header('/');
    }
  } else{echo "You are not in the database; Register by running /webauth <password> ";}
}
function getUUID($username){
  $curl = curl_init();
  // Set some options - we are passing in a useragent too here
  curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://api.mojang.com/users/profiles/minecraft/subdavis',
    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
  ));
  // Send the request & save response to $resp
  $resp = curl_exec($curl);

  echo $resp;
  // Close request to clear up some resources
  curl_close($curl);

  $json_a = json_decode($resp,true);
  return $json_a[name];
}

?>
