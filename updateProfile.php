<?php
include("./api/v1/database.php");

$birthday = strtr($_POST["dob"], '/', '-');
$date = date('Y-m-d', strtotime($birthday));
$query = "UPDATE `user_profile` SET DOB='$date'";

if (isset($_POST["email"])) {
  $email = $_POST["email"];
  $query .= ", Email='$email'";
}

if (isset($_POST["roles"])) {
  $roles = $_POST["roles"];
  $query .= ", Roles='$roles'";
}

if (isset($_POST["country"])) {
  $countryC = trim($_POST["country"]);
  $trimmedC = trim($countryC, "1234567890,!@#$%^&*()+;\|?='_<>[]{}:");
  $query .= ", Country='$trimmedC'";
}

if (isset($_POST["password"])) {
  $pass = $_POST["password"];
  $query .= ", Password='$pass'";
}

if (isset($_POST["about"])) {
  $about = $_POST["about"];
  $aboutA = trim($about);
  $trimmedA = trim($aboutA, "='");
  $query .= ", About='$trimmedA'";
}

if (isset($_POST["id"])) {
  $id = $_POST["id"];
} else {
  session_start();
  $id = $_SESSION["userid"];
}
$query .= " WHERE SteamID='$id'";
//$q = "UPDATE `user_profile` SET DOB='$date', Country='$trimmedC', About='$trimmedA' WHERE SteamID='$id'";
if ($conn->query($query) == TRUE) {
  echo 1;
} else {
  echo 0;
}
