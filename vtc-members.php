<?php
header('Content-Type:text/html; charset=iso-8859-1');
include("./api/v1/database.php");
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
   header("Location: page-login?redirect=vtc-members");
}
$id = $_SESSION['userid'];

$s = "SELECT Username,Permission FROM `user_profile` WHERE SteamID='$id'";
$q = mysqli_query($conn, $s);
$userstats = mysqli_fetch_array($q);

$profilepic = "avatars/" . $id . ".png";
if (!file_exists($profilepic)) {
   $profilepic = "avatars/default.png";
}
$username = $userstats["Username"];

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
      default:
      return "text-muted";
   break;
   }
}
$staff = array("Public Relations","HR", "HR Manager", "Admin", "Manager", "Support Manager", "Events Manager", "Trainee HR", "Support", "Trainee Support", "Events", "Trainee Events", "Trainee PR", "PR Manager");


?>
<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Falcon Trucking Dashboard with fully automated Jobs and user statistics">
   <meta name="author" content="Falcon Trucking">
   <link rel="shortcut icon" href="assets/images/favicon.ico">
   <title>Falcon Trucking Dashboard - Members</title>
   <!-- App css -->
   <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
   <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
   <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
   <script src="assets/js/modernizr.min.js"></script>
</head>

<body class="fixed-left">
   <!-- Begin page -->
   <div id="wrapper">
      <!-- Top Bar Start -->
      <div class="topbar">
         <!-- LOGO -->
         <div class="topbar-left">
            <a href="index" class="logo"><span><img src="assets/images/logo.png" alt="Falcon Logo" title="Goto Main Dashboard"></img></span><i class="mdi mdi-layers"></i></a>
         </div>
         <!-- Button mobile view to collapse sidebar menu -->
         <div class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
               <!-- Page title -->
               <ul class="nav navbar-nav list-inline navbar-left">
                  <li class="list-inline-item">
                     <button class="button-menu-mobile open-left">
                        <i class="mdi mdi-menu"></i>
                     </button>
                  </li>
                  <li class="list-inline-item">
                     <h4 class="page-title">VTC Members</h4>
                  </li>
               </ul>
               <!--<nav class="navbar-custom">
                  <ul class="list-unstyled topbar-right-menu float-right mb-0">
                     <li>
                        <!-- Notification
                        <div class="notification-box">
                           <ul class="list-inline mb-0">
                              <li>
                                 <a href="javascript:void(0);" class="right-bar-toggle"> <i class="mdi mdi-bell-outline noti-icon"></i> </a>
                                 <div class="noti-dot"> <span class="dot"></span> <span class="pulse"></span> </div>
                              </li>
                           </ul>
                        </div>
                        <!-- End Notification bar
                     </li>
                     <li class="hide-phone">
                        <form class="app-search">
                           <input type="text" placeholder="Search..." class="form-control">
                           <button type="submit"><i class="fa fa-search"></i></button>
                        </form>
                     </li>
                  </ul>
               </nav>-->
            </div>
            <!-- end container -->
         </div>
         <!-- end navbar -->
      </div>
      <!-- Top Bar End -->
      <?php include("left-sidebar.php") ?>
      <!-- ============================================================== -->
      <!-- Start right Content here -->
      <!-- ============================================================== -->
      <div class="content-page">
         <!-- Start content -->
         <div class="content">
            <div class="container-fluid">
               <div class="row">
                  <!-- Standard Alert -->
                  <div class="col-md-12">
                     <div class="alert alert-danger text-center">
                        <strong>Management</strong> Staff.
                     </div>
                  </div>
                  <!-- end col -Standard Alert -->
               </div>
               <div class="row staff">
                  <?php
                  $s = "SELECT SteamID,Username,About,DOB,Roles,JoinDate,Country,Permission FROM `user_profile`";
                  $q = mysqli_query($conn, $s);
                  while ($userstats = mysqli_fetch_array($q)) {
                     $arrayed = explode(",", $userstats["Roles"]);
                     if (empty(array_intersect($arrayed, $staff))) {
                        continue; // SKIP TO NEXT
                     }
                     $pfp = "avatars/" . $userstats["SteamID"] . ".png";
                     if (!file_exists($pfp)) {
                        $pfp = "avatars/default.png";
                     }
                     $roles = explode(", ", $userstats["Roles"]);
                     $primary = $roles[0];
                     $secondary = array_slice($roles, 1) == null ? ["None"] : array_slice($roles, 1);
                     $date = $userstats["DOB"] != "0000-00-00" ? date('d/m/Y', strtotime($userstats["DOB"])) : "Not Provided";
                     $joined = date('d/m/Y', strtotime($userstats["JoinDate"]));
                     $about = ($userstats["About"] != null) ? $userstats["About"] : "I am too lazy to add my bio";
                     ?>
                     <div class="col-xl-3 col-md-6 sortStaff" data-date="<?php echo $userstats["JoinDate"] ?>">
                        <div class="text-center card-box members">
                           <div>
                              <img <?php echo 'src="' . $pfp . '"' ?> title="<?php echo $userstats["Username"]; ?>" class="rounded-circle thumb-xl img-thumbnail m-b-10" alt="profile-image">
                              <p class="text-muted font-13 m-b-30">
                                 <?php echo $about ?>
                              </p>
                              <div class="text-left">
                                 <p class="text-muted font-13"><strong>Full Name :</strong> <span class="m-l-15"><?php echo $userstats["Username"]; ?></span></p>
                                 <p class="text-muted font-13"><strong>DOB:</strong> <span class="m-l-15"><?php echo $date ?></span></p>
                                 <p class="text-muted font-13"><strong>Joined on:</strong> <span class="m-l-15"><?php echo $joined; ?></span></p>
                                 <p class="text-muted font-13"><strong>Country :</strong> <span class="m-l-15"><?php echo $userstats["Country"]; ?></span></p>
                                 <p class="font-13"><strong>Primary Role :</strong><span class="m-l-15 <?php echo rankColor($primary) ?>"><?php echo $primary ?></span></p>
                                 <p class="font-13"><strong>Additional Roles :</strong><span class="m-l-15 <?php echo rankColor($secondary[0]) ?>"><?php echo implode(", ", $secondary) ?></span></p>
                              </div>
                           </div>
                        </div>
                     </div>
                  <?php
                  }
                  ?>
               </div>
               <!-- end row -->
               <div class="row">
                  <!-- Standard Alert -->
                  <div class="col-md-12">
                     <div class="alert alert-info text-center">
                        <strong>Drivers</strong>
                     </div>
                  </div>
                  <!-- end col -Standard Alert -->
               </div>
               <div class="row drivers">
                  <?php
                  $s = "SELECT SteamID,Username,About,DOB,Roles,JoinDate,Country,Permission FROM `user_profile`";
                  $q = mysqli_query($conn, $s);
                  while ($userstats = mysqli_fetch_array($q)) {
                     $arrayed = explode(",", $userstats["Roles"]);
                     if (!empty(array_intersect($arrayed, $staff))) {
                        continue; // SKIP TO NEXT
                     }
                     $pfp = "avatars/" . $userstats["SteamID"] . ".png";
                     if (!file_exists($pfp)) {
                        $pfp = "avatars/default.png";
                     }
                     $roles = explode(", ", $userstats["Roles"]);
                     $primary = $roles[0] == null ? "Driver" : $roles[0];
                     $secondary = array_slice($roles, 1) == null ? ["None"] : array_slice($roles, 1);
                     $date = $userstats["DOB"] != "0000-00-00" ? date('d/m/Y', strtotime($userstats["DOB"])) : "Not Provided";
                     $joined = date('d/m/Y', strtotime($userstats["JoinDate"]));
                     $about = ($userstats["About"] != null) ? $userstats["About"] : "I am too lazy to add my bio";
                     ?>
                     <div class="col-xl-3 col-md-6 sortDriver" data-date="<?php echo $userstats["JoinDate"] ?>">
                        <div class="text-center card-box members">
                           <div>
                              <img <?php echo 'src="' . $pfp . '"' ?> title="<?php echo $userstats["Username"]; ?>" class="rounded-circle thumb-xl img-thumbnail m-b-10" alt="profile-image">
                              <p class="text-muted font-13 m-b-30">
                                 <?php echo $about ?>
                              </p>
                              <div class="text-left">
                                 <p class="text-muted font-13"><strong>Full Name :</strong> <span class="m-l-15"><?php echo $userstats["Username"]; ?></span></p>
                                 <p class="text-muted font-13"><strong>DOB:</strong> <span class="m-l-15"><?php echo $date ?></span></p>
                                 <p class="text-muted font-13"><strong>Joined on:</strong> <span class="m-l-15"><?php echo $joined; ?></span></p>
                                 <p class="text-muted font-13"><strong>Country :</strong> <span class="m-l-15"><?php echo $userstats["Country"]; ?></span></p>
                                 <p class="font-13"><strong>Primary Role :</strong><span class="m-l-15 <?php echo rankColor($primary) ?>"><?php echo $primary ?></span></p>
                                 <p class="font-13"><strong>Additional Roles :</strong><span class="m-l-15 <?php echo rankColor($secondary[0]) ?>"><?php echo implode(", ", $secondary) ?></span></p>
                              </div>
                           </div>
                        </div>
                     </div>
                  <?php
                  }
                  ?>
               </div>
            </div>
            <!-- container -->
         </div>
         <!-- content -->
         <footer class="footer text-right">
            Copyrights <i class="fa fa-copyright"></i> 2018<script>
               new Date().getFullYear() > 2018 && document.write("-" + new Date().getFullYear());
            </script>, Falcon Trucking VTC. All Rights Reserved.
         </footer>
      </div>
      <!-- ============================================================== -->
      <!-- End Right content here -->
      <!-- ============================================================== -->
      <!-- Right Sidebar -->
      <div class="side-bar right-bar">
         <a href="javascript:void(0);" class="right-bar-toggle">
            <i class="mdi mdi-close-circle-outline"></i>
         </a>
         <h4 class="">Notifications</h4>
         <div class="notification-list nicescroll">
            <ul class="list-group list-no-border user-list">
               <li class="list-group-item">
                  <a href="#" class="user-list-item">
                     <div class="avatar">
                        <img src="assets/images/users/avatar-2.jpg" alt="">
                     </div>
                     <div class="user-desc">
                        <span class="name">Michael Zenaty</span>
                        <span class="desc">There are new settings available</span>
                        <span class="time">2 hours ago</span>
                     </div>
                  </a>
               </li>
               <li class="list-group-item">
                  <a href="#" class="user-list-item">
                     <div class="icon bg-info">
                        <i class="mdi mdi-account"></i>
                     </div>
                     <div class="user-desc">
                        <span class="name">New Signup</span>
                        <span class="desc">There are new settings available</span>
                        <span class="time">5 hours ago</span>
                     </div>
                  </a>
               </li>
               <li class="list-group-item">
                  <a href="#" class="user-list-item">
                     <div class="icon bg-pink">
                        <i class="mdi mdi-comment"></i>
                     </div>
                     <div class="user-desc">
                        <span class="name">New Message received</span>
                        <span class="desc">There are new settings available</span>
                        <span class="time">1 day ago</span>
                     </div>
                  </a>
               </li>
               <li class="list-group-item active">
                  <a href="#" class="user-list-item">
                     <div class="avatar">
                        <img src="assets/images/users/avatar-3.jpg" alt="">
                     </div>
                     <div class="user-desc">
                        <span class="name">James Anderson</span>
                        <span class="desc">There are new settings available</span>
                        <span class="time">2 days ago</span>
                     </div>
                  </a>
               </li>
               <li class="list-group-item active">
                  <a href="#" class="user-list-item">
                     <div class="icon bg-warning">
                        <i class="mdi mdi-settings"></i>
                     </div>
                     <div class="user-desc">
                        <span class="name">Settings</span>
                        <span class="desc">There are new settings available</span>
                        <span class="time">1 day ago</span>
                     </div>
                  </a>
               </li>
            </ul>
         </div>
      </div>
      <!-- /Right-bar -->
   </div>
   <!-- END wrapper -->
   <!-- jQuery  -->
   <script>
      if (typeof module === 'object') {
         window.module = module;
         module = undefined;
      }
   </script>
   <script src="assets/js/jquery.min.js"></script>
   <script src="assets/js/popper.min.js"></script>
   <script src="assets/js/bootstrap.min.js"></script>
   <script src="assets/js/detect.js"></script>
   <script src="assets/js/fastclick.js"></script>
   <script src="assets/js/jquery.blockUI.js"></script>
   <script src="assets/js/waves.js"></script>
   <script src="assets/js/jquery.nicescroll.js"></script>
   <script src="assets/js/jquery.slimscroll.js"></script>
   <script src="assets/js/jquery.scrollTo.min.js"></script>
   <!-- Counter Up  -->
   <script src="assets/plugins/waypoints/jquery.waypoints.min.js"></script>
   <script src="assets/plugins/counterup/jquery.counterup.min.js"></script>
   <!-- KNOB JS -->
   <!--[if IE]>
      <script type="text/javascript" src="assets/plugins/jquery-knob/excanvas.js"></script>
      <![endif]-->
   <script src="assets/plugins/jquery-knob/jquery.knob.js"></script>
   <!-- App js -->
   <script src="assets/js/jquery.core.js"></script>
   <script src="assets/js/jquery.app.js"></script>
   <script>
      if (window.module) module = window.module;
   </script>
   <script>
      $(document).ready(function() {
         function parseDate(input) {
            var parts = input.match(/(\d+)/g);
            // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
            return new Date(parts[0], parts[1] - 1, parts[2]); //     months are 0-based
         }

         function getSorted(selector, attrName) {
            return $($(selector).toArray().sort(function(a, b) {
               var aVal = parseDate(a.getAttribute(attrName)),
                  bVal = parseDate(b.getAttribute(attrName));
               return aVal - bVal;
            }));
         }
         let staff = getSorted(".sortStaff", "data-date")
         $(".staff").html(staff);
         let driver = getSorted(".sortDriver", "data-date")
         $(".drivers").html(driver);
      });
   </script>
</body>

</html>