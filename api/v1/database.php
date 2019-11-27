<?php
  $host = "localhost";
  $username = "root";
  $password = "";
  $database = "falconit_dashboard";

  $cipher = "AES-128-CTR";
  $key = "prasadhfalconit14";
  $iv = "1234567891011121";

  $conn = new mysqli($host, $username,$password,$database);
  mysqli_set_charset($conn, "utf8");

  if($conn->connect_error) {
    header("Location: page-404.html");
  }
?>