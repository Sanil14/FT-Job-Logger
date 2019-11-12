<?php
   include("./api/v1/database.php");
   session_start();
   $id = $_SESSION["userid"];
   $settings = $_POST["settings"];
   $q = "UPDATE `user_profile` SET Preferences='$settings' WHERE UserID='$id'";
   if($conn->query($q) == TRUE) {
     echo 1;
   } else {
     echo 0;
   }
?>