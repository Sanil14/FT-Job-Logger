<?php
include("./api/v1/database.php");
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
   header("Location: page-login?redirect=vtc-jobs");
}
$id = $_SESSION['userid'];
$invalid_err = "";
$s = "SELECT Username,Email,DOB,Country,About,Preferences,Permission FROM `user_profile` WHERE SteamID='$id'";
$q = mysqli_query($conn, $s);
$userstats = mysqli_fetch_array($q);

$profilepic = "avatars/" . $id . ".png";
if (!file_exists($profilepic)) {
   $profilepic = "avatars/default.png";
}
$username = $userstats["Username"];

$pref = json_decode($userstats["Preferences"]);

$jobs = explode(",", $_GET["jobs"]);
$index = array_search($_GET["id"], $jobs);
$nextdisable = false;
$prevdisable = false;
$previousurl = "";
$nexturl = "";

if (sizeof($jobs) == $index + 1) {
   $nextdisable = true;
} else {
   $nexturl = "job-details?id=" . ($jobs[$index + 1]) . "&jobs=" . $_GET["jobs"];
}

if ($index < 1) {
   $prevdisable = true;
} else {
   $previousurl = "job-details?id=" . ($jobs[$index - 1]) . "&jobs=" . $_GET["jobs"];
}

?>

<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Falcon Trucking Dashboard with fully automated Jobs and user statistics">
   <meta name="author" content="Falcon Trucking">
   <link rel="shortcut icon" href="assets/images/favicon.ico">
   <title>Falcon Trucking Dashboard - Job Details</title>
   <!-- DataTables -->
   <link href="assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
   <link href="assets/plugins/datatables/butDefault Exampletons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
   <!-- Responsive datatable examples -->
   <link href="assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
   <!-- Multi Item Selection examples -->
   <link href="assets/plugins/datatables/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
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
                     <h4 class="page-title">Full Job Details</h4>
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
                  <div class="col-12">
                     <div class="card-box table-responsive">
                        <div class="clearfix">
                           <h4 class="m-t-0 col-md-3 header-title float-left">Full Job details</h4>
                           <div class="btn-group m-b-10 col-md-3 float-right">
                              <button type="button" class="btn btn-info waves-effect" onclick="window.location='<?php echo $previousurl ?>';" <?php if ($prevdisable) {
                                                                                                                                                   echo "disabled";
                                                                                                                                                } ?>>Previous Job</button>
                              <button type="button" class="btn btn-info waves-effect" onclick="window.location='<?php echo $nexturl ?>';" <?php if ($nextdisable) {
                                                                                                                                             echo "disabled";
                                                                                                                                          } ?>>Next Job</button>
                           </div>
                        </div>
                        <p class="text-muted font-14 m-b-30">
                           Click Next to see older jobs, Previous for Newer Jobs.
                        </p>
                        <table id="datatable-jobs" class="table table-striped table-bordered" cellspacing="0" width="100%">
                           <thead>
                              <tr>
                                 <th>Icon</th>
                                 <th>Feature</th>
                                 <th>Details</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              $id = $_GET["id"];
                              $p = "SELECT * FROM `user_jobs` WHERE JobID='$id'";
                              $g = mysqli_query($conn, $p);
                              $jobdetails = mysqli_fetch_array($g);
                              $uid = $jobdetails["SteamID"];
                              $s = "SELECT Username FROM `user_profile` WHERE SteamID='$uid'";
                              $q = mysqli_query($conn, $s);
                              $userdetails = mysqli_fetch_array($q);

                              $startTime = $jobdetails["realTimeStarted"];
                              $endTime = $jobdetails["realTimeEnded"];
                              if ($jobdetails["LateFine"] == true) {
                                 $late = "Late";
                              } else {
                                 $late = "On Time";
                              }

                              $money = $jobdetails["Income"];
                              $timezone = $pref->Timezone;
                              $fuel = $pref->Fuel;
                              $distance = $pref->Distance;
                              $mass = $pref->Mass;

                              if ($fuel == "Gallons") {
                                 $petrol = round(round($jobdetails["Fuel"]) / 3.785);
                                 $funits = " gal";
                              } else {
                                 $petrol = round($jobdetails["Fuel"]);
                                 $funits = " Litres";
                              }

                              if ($distance == "M") {
                                 $length = round($jobdetails["Odometer"] / 1.609, 2);
                                 $dunits = " Miles";
                                 $speed = round($jobdetails["TopSpeed"] / 1.609, 2);
                                 $sunits = " mph";
                              } else {
                                 $length = round($jobdetails["Odometer"], 2);
                                 $dunits = " KM";
                                 $speed = round($jobdetails["TopSpeed"], 2);
                                 $sunits = " km/h";
                              }

                              if ($mass == "lbs") {
                                 $weight = round($jobdetails["CargoMass"] * 2.205, 2);
                                 $munits = " lbs";
                              } else {
                                 $weight = round($jobdetails["CargoMass"], 2);
                                 $munits = " kg";
                              }

                              if ($timezone == "GMT") {
                                 $d = gmdate('r', $startTime);
                                 $f = gmdate('r', $endTime);
                                 $from = "UTC";
                                 $to = "GMT";
                                 $start = date_create($d, new DateTimeZone($from))
                                    ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
                                 $end = date_create($f, new DateTimeZone($from))
                                    ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
                              } else if ($timezone == "CST") {
                                 $d = gmdate('r', $startTime);
                                 $f = gmdate('r', $endTime);
                                 $from = "UTC";
                                 $to = "CDT";
                                 $start = date_create($d, new DateTimeZone($from))
                                    ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
                                 $end = date_create($f, new DateTimeZone($from))
                                    ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
                              }
                              ?>
                              <tr>
                                 <td><i class="fa fa-truck fa-2x "></i></td>
                                 <td>Game Mode</td>
                                 <td><?php echo $jobdetails["isMultiplayer"] == true ? "Multiplayer" : "Singleplayer"; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa fa-id-card fa-2x "></i></td>
                                 <td>Job ID</td>
                                 <td><?php echo $jobdetails["JobID"]; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-user fa-2x"></i></td>
                                 <td>User Name</td>
                                 <td><?php echo $userdetails["Username"]; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa fa-gamepad fa-2x "></i></td>
                                 <td>Game</td>
                                 <td><?php echo strtoupper($jobdetails["GameType"]); ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa fa-fort-awesome fa-2x "></i></td>
                                 <td>Source City</td>
                                 <td><?php echo $jobdetails["SourceCity"]; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa fa-toggle-on fa-2x "></i></td>
                                 <td>Source Company</td>
                                 <td><?php echo $jobdetails["SourceCompany"]; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa  fa-building fa-2x "></i></td>
                                 <td>Destination City</td>
                                 <td><?php echo $jobdetails["DestinationCity"]; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa  fa-toggle-off fa-2x "></i></td>
                                 <td>Destination Company</td>
                                 <td><?php echo $jobdetails["DestinationCompany"]; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-truck-loading fa-2x"></i></td>
                                 <td>Cargo Name</td>
                                 <td><?php echo $jobdetails["CargoName"]; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-weight-hanging fa-2x"></i></td>
                                 <td>Cargo Weight</td>
                                 <td><?php echo $weight . $munits; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-exclamation-triangle fa-2x"></i></td>
                                 <td>Truck Damage</td>
                                 <td><?php echo round($jobdetails["OverallDamage"] * 100, 2) . "%" ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-square fa-2x"></i></td>
                                 <td>Trailer Damage</td>
                                 <td><?php echo round($jobdetails["trailerDamage"] * 100, 2) . "%" ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-truck fa-2x"></i></td>
                                 <td>Truck Used</td>
                                 <td><?php echo $jobdetails["TruckBrand"] . " " . $jobdetails["TruckModel"]; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa fa-road fa-2x"></i></td>
                                 <td>Distance Driven</td>
                                 <td><?php echo $length . $dunits; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa fa-fire fa-2x"></i></td>
                                 <td>Fuel Consumed</td>
                                 <td><?php echo $petrol . $funits ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-coins fa-2x"></i></td>
                                 <td>Job Income</td>
                                 <td>€ <?php echo $money; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa fa-clock-o fa-2x"></i></td>
                                 <td>Time Started</td>
                                 <td><?php echo $start . " " . $timezone; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fa fa-history fa-2x"></i></td>
                                 <td>Time Ended</td>
                                 <td><?php echo $end . " " . $timezone; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-hourglass-half fa-2x"></i></td>
                                 <td>On Time/Late</td>
                                 <td><?php echo $late; ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-tachometer-alt fa-2x"></i></td>
                                 <td>Speeding Count</td>
                                 <td><?php echo $jobdetails["SpeedingCount"] . " Time" . ($jobdetails["SpeedingCount"] == 1 ? "" : "s") ?></td>
                              </tr>
                              <tr>
                                 <td><i class="fas fa-car-crash fa-2x"></i></td>
                                 <td>Collision Count</td>
                                 <td><?php echo $jobdetails["CollisionCount"] . " Time" . ($jobdetails["CollisionCount"] == 1 ? "" : "s") ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
               <!-- end row -->
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
   <!-- Required datatable js -->
   <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
   <script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
   <!-- Buttons examples -->
   <script src="assets/plugins/datatables/dataTables.buttons.min.js"></script>
   <script src="assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
   <script src="assets/plugins/datatables/jszip.min.js"></script>
   <script src="assets/plugins/datatables/pdfmake.min.js"></script>
   <script src="assets/plugins/datatables/vfs_fonts.js"></script>
   <script src="assets/plugins/datatables/buttons.html5.min.js"></script>
   <script src="assets/plugins/datatables/buttons.print.min.js"></script>
   <!-- Key Tables -->
   <script src="assets/plugins/datatables/dataTables.keyTable.min.js"></script>
   <!-- Responsive examples -->
   <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
   <script src="assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
   <!-- Selection table -->
   <script src="assets/plugins/datatables/dataTables.select.min.js"></script>
   <!-- App js -->
   <script src="assets/js/jquery.core.js"></script>
   <script src="assets/js/jquery.app.js"></script>
   <script>
      if (window.module) module = window.module;
   </script>
   <script type="text/javascript">
      $(document).ready(function() {

         function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
               vars[key] = value;
            });
            return vars;
         }

         if (isNaN((getUrlVars()["id"])) || getUrlVars()["id"] === undefined) {
            window.location.href = 'page-404';
         }

         console.log(getUrlVars()["id"])

         //Buttons examples
         var table = $('#datatable-jobs').DataTable({
            "paging": false,
            "ordering": false,
            "info": false,
            "searching": false,
            "columnDefs": [{
               "width": "5%",
               "targets": 0
            }]
         });
      });
   </script>
</body>

</html>