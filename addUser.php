<?php
  include("./api/v1/database.php");


  $email = $_GET["email"];
  $username = $_GET["username"];
  $steamid = $_GET["steamid"];
  $date = strtr($_GET["date"],'/','-');
  $joindate = date('Y-m-d',strtotime($date));

  $s = "SELECT * FROM `user_profile` WHERE Username='$username'";
  $q = mysqli_query($conn,$s);
  if(mysqli_fetch_array($q)) {
    echo "401";
  } else {
    $p = "INSERT INTO `user_profile` VALUES ('$steamid', '$username', '$email', '123456', '', '', '2000-02-29','Trainee','None','$joindate',DEFAULT)";

    if (mysqli_query($conn,$p)) {
      echo "200";
    } else {
      echo "400";
    }
  }
?>