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
  $tab = isset($_GET['tab']) ? $_GET['tab'] : NULL;

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
    margin-left: -5;
    margin-right: 0;
  }
  .proftable > td {
    padding: 10px 10px;
  }
  /*body {
    background: #CFEBC8;
  }*/
  </style>
</head>
<body>
  <div class='row'>
    <div class='col-sm-12' style='padding-top:15px; padding-bottom:15px; background-color: #7FAEA6;'>
      <div class='row' style='height:100px;'>
        <div class='col-sm-1' style="min-width:140px;">
          <img src='avatars/<?php echo $player;?>'/>
        </div>
        <div class='col-sm-7'>
          <h2>Hello there, <?php echo $player;?><small> [<?php echo $isStaff ? "Staff" : "Member";?>]</small></h2>
          <h4>UUID: <?php echo $uuid;?></h4>
        </div>
      </div>
    </div>
  </div>
  <div class='row'>
    <div class='col-sm-1' style ="padding-top:22px;min-width: 140px;background-color: #7FAEA6;height: 84%;">
      <ul class="nav nav-pills">
        <li role="presentation"><a href="?tab=list">Member List</a></li>
        <li role="presentation"><a href="?tab=profile">Profile</a></li>
        <li role="presentation"><a href="?tab=mail">Mail<span class="badge">4</span></a></li>
        <li role="presentation"><a href="?tab=banlog">BanLog</a></li>
        <?php if($isStaff){echo"<li role='presentation'><a href='?tab=staff'>Staff</a></li>";}?>
        <li role="presentation"><a href="?tab=exit">Sign Out</a></li>
      </ul>
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
        //Member List tab
        include 'list.php';
      break;

      case 'banlog':
        //banlog tab
        include 'banlog.php';
      break;

      case 'staff':
        //staff tab
        if ($isStaff){
          //do the things;
        } else echo "Nice Try.";
      break;
    }
  ?>
</body>
</html>
<?php mysqli_close($con);?>
