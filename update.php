<?php

include 'creds.php';

$con = mysqli_connect("localhost",$sql_user,$sql_password,$sql_db);

if(isset($_POST['prof_email'])){
  if($_POST['prof_email']) = "")
    else{
    $sql = "UPDATE `webauth`.`users` SET `email` = '".$_POST['prof_email']."' WHERE `users`.`uuid` = '".$uuid."';";
    $result = mysqli_query($con, $sql);
  }
}
if(isset($_POST['prof_skype'])){
  if($_POST['prof_skype'] = "")
    else{
    $sql = "UPDATE `webauth`.`users` SET `skype` = '".$_POST['prof_skype']."' WHERE `users`.`uuid` = '".$uuid."';";
    $result = mysqli_query($con, $sql);
  }
}
header( "Location: portal.php?tab=".$_GET['tab'] ) ;
?>
