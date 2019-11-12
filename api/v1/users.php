<?php
  header('Content-Type: application/json');
  include("tokencheck.php");
  include("database.php");

  if (!isset($_GET["login"])) {
    $data = array(
      "status"=>"400",
      "error"=>"login not found"
    );
    echo json_encode($data);
    exit();
  }

  $key = $_GET["login"];

  $s = "SELECT Username,loginKey,UserID FROM `user_profile` WHERE loginKey='$key'";
  $q = mysqli_query($conn,$s);
  $rows = mysqli_num_rows($q);
  if ($rows == 0) {
    $data = array(
      "status"=>"404",
      "error"=>"user not found"
    );
    echo json_encode($data);
    exit();
  }
  $info = mysqli_fetch_array($q);

  $username = $info["Username"];
  $loginKey = $info["loginKey"];
  $uid = $info["UserID"];

  $data = array(
    "status" => "202",
    "userid" => $uid,
    "username" => $username,
    "token" => $loginKey
  );

  echo json_encode($data);
  exit();

?>