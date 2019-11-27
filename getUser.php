<?php
include("./api/v1/database.php");
$name = $_GET["name"];
$s = "SELECT UserID,Username,About,DOB,Roles,JoinDate,Country,Permission FROM `user_profile` WHERE Username LIKE '$name%'";
$q = mysqli_query($conn, $s);
while ($userstats = mysqli_fetch_array($q)) {
   //print_r( $userstats);
   $arrayed = explode(",", $userstats["Roles"]);
   if ($userstats["Permission"] == "Admin") {
      continue;
   }
   $pfp = "avatars/" . $userstats["UserID"] . ".png";
   if (!file_exists($pfp)) {
      $pfp = "avatars/default.png";
   }
   $date = $userstats["DOB"] != "0000-00-00" ? date('d/m/Y', strtotime($userstats["DOB"])) : "Not Provided";
   $joined = date('d/m/Y', strtotime($userstats["JoinDate"]));
   $about = ($userstats["About"] != null) ? $userstats["About"] : "I am too lazy to add my bio";
   $page = $userstats["UserID"];

   echo "<div class='col-xl-3 col-md-6 sortDriver' data-date=" . $userstats["JoinDate"] . " data-userid='$page' >
   <div class='text-center card-box members'>
      <div>
         <img src='$pfp' title='" . $userstats["Username"] . "' class='rounded-circle thumb-xl img-thumbnail m-b-10' alt='profile-image'>
         <p class='text-muted font-13 m-b-30'>
            $about
         </p>
         <div class='text-left'>
            <p class='text-muted font-13'><strong>Full Name :</strong> <span class='m-l-15'>" . $userstats["Username"] . "</span></p>
            <p class='text-muted font-13'><strong>DOB:</strong> <span class='m-l-15'>$date</span></p>
            <p class='text-muted font-13'><strong>Joined on:</strong> <span class='m-l-15'>$joined</span></p>
            <p class='text-muted font-13'><strong>Country :</strong> <span class='m-l-15'>" . $userstats["Country"] . "</span></p>
            <p class='font-13'><strong>Role :</strong><span class='m-l-15  ". rankColor($userstats["Roles"]) . "'>" . $userstats["Roles"] . "</span></p>
         </div>
         <button type='button' class='btn btn-info btn-rounded w-md waves-effect waves-light m-b-5 editbtn'>Edit</button>
         <button type='button' class='btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5 deletebtn'> Delete
         </button>
      </div>
   </div>
</div>";
}

function rankColor($option)
{
   $output = ($option == "Owner") ? "text-danger" : (($option == "HR Manager" || $option == "HR" || $option == "HR Trainee") ? "text-primary" : (($option == "Events Manager" || $option == "Manager") ? "text-success" : (($option == "Public Relations") ? "text-third" : (($option == "Support" || $option == "Support Trainee") ? "text-success" : (($option == "French Lead") ? "text-third" : (($option == "DOM") ? "text-warning" : (($option == "Driver") ? "text-info" : "text-muted")))))));
   return $output;
}
