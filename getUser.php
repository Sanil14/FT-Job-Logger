<?php
include("./api/v1/database.php");
$name = $_GET["name"];
$s = "SELECT SteamID,Username,About,DOB,Roles,JoinDate,Country,Permission FROM `user_profile` WHERE Username LIKE '$name%'";
$q = mysqli_query($conn, $s);
while ($userstats = mysqli_fetch_array($q)) {
   //print_r( $userstats);
   $arrayed = explode(",", $userstats["Roles"]);
   if ($userstats["Permission"] == "Admin") {
      continue;
   }
   $pfp = "avatars/" . $userstats["SteamID"] . ".png";
   if (!file_exists($pfp)) {
      $pfp = "avatars/default.png";
   }
   $date = $userstats["DOB"] != "0000-00-00" ? date('d/m/Y', strtotime($userstats["DOB"])) : "Not Provided";
   $joined = date('d/m/Y', strtotime($userstats["JoinDate"]));
   $about = ($userstats["About"] != null) ? $userstats["About"] : "I am too lazy to add my bio";
   $page = $userstats["SteamID"];
   $roles = explode(", ", $userstats["Roles"]);
   $default = $userstats["Roles"] == null ? "Driver" : $userstats["Roles"];
   $primary = $roles[0] == null ? "Driver" : $roles[0];

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
            <p class='font-13'><strong>Role :</strong><span class='m-l-15  ". rankColor($primary) . "'>$default</span></p>
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
   switch ($option) {
      case "Admin":
         return "text-danger";
      break;
      case "Manager":
         return "text-success";
      break;
      case "HR Manager":
         return "text-purple";
      break;
      case "HR":
         return "text-purple";
      break;
      case "Trainee HR":
         return "text-purple";
      break;
      case "Events Manager":
         return "text-success";
      break;
      case "Events":
         return "text-success";
      break;
      case "Trainee Events":
         return "text-success";
      break;
      case "PR Manager":
         return "text-warning";
      break;
      case "Public Relations":
         return "text-warning";
      break;
      case "Trainee PR":
         return "text-warning";
      break;
      case "Support Manager":
         return "text-warning";
      break;
      case "Support":
         return "text-warning";
      break;
      case "Trainee Support":
         return "text-warning";
      break;
      case "Driver of the Month":
         return "text-warning";
      break;
      case "Driver":
         return "text-primary";
      break;
      case "Trainee":
         return "text-primary";
      break;
      case "Skilled Driver":
         return "text-info";
      break;
      case "Professional Driver":
         return "text-info";
      break;
      case "Legendary Driver":
         return "text-success";
      break;
      case "Mouse Magician":
         return "text-pink";
      break;
      case "Vasco da Gama":
         return "text-pink";
      break;
      case "Wheel on Wheels":
         return "text-pink";
      break;
   break;
   }
}
