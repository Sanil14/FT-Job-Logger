<?php
  include("./api/v1/database.php");

  $email = $_POST["email"];
  $username = $_POST["uname"];
  $key = $_POST["key"];
  $joindate = date('Y-m-d',strtotime($_POST["date"]));

  $s = "SELECT * FROM `user_profile` WHERE Username='$username'";
  $q = mysqli_query($conn,$s);
  if(mysqli_fetch_array($q)) {
    echo "400";
  } else {
    $p = "INSERT INTO `user_profile` VALUES ('0', '$username', '$email', '123456', '', '', '','Driver','None','$joindate','$key', DEFAULT)";

    if (mysqli_query($conn,$p)) {
      echo "200";
    }
  }


?>