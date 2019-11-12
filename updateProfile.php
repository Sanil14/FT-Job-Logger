<?php
   include("./api/v1/database.php");
   session_start();
   $id = $_SESSION["userid"];
   $birthday = $_POST["dob"];
   $country = $_POST["country"];
   $about = $_POST["about"];
   $aboutA = trim($about);
   $countryC = trim($country);
   $trimmedA = trim($aboutA, "='");
   $trimmedC = trim($countryC, "='1234567890\",!@#$%^&*()+;\|?_<>[]{}:");
   $date = date('Y/m/d', strtotime($birthday));
   $q = "UPDATE `user_profile` SET DOB='$date', Country='$trimmedC', About='$trimmedA' WHERE UserID='$id'";
   if($conn->query($q) == TRUE) {
     echo 1;
   } else {
     echo 0;
   }
?>