<?php
include("./api/v1/database.php");
$uid = $_GET["userid"];
$s = "DELETE FROM `user_profile` WHERE SteamID='$uid'";
$q = mysqli_query($conn, $s);
if ($q) {
   echo 1;
} else {
   echo mysqli_error($conn);
}
