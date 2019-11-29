<?php
include("./api/v1/database.php");
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
   header("Location: page-login?redirect=vtc-jobs");
}
$id = $_SESSION['userid'];
$invalid_err = "";
$s = "SELECT Username,Email,DOB,Country,About,Preferences,Permission FROM `user_profile` WHERE UserID='$id'";
$q = mysqli_query($conn, $s);
$userstats = mysqli_fetch_array($q);

$profilepic = "avatars/" . $id . ".png";
if (!file_exists($profilepic)) {
   $profilepic = "avatars/default.png";
}
$username = $userstats["Username"];

$pref = json_decode($userstats["Preferences"]);
$timezone = $pref->Timezone;

?>
<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Falcon Trucking Dashboard with fully automated Jobs and user statistics">
   <meta name="author" content="Falcon Trucking">
   <link rel="shortcut icon" href="assets/images/favicon.ico">
   <title>Falcon Trucking Dashboard - VTC Jobs</title>
   <!-- DataTables -->
   <link href="assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
   <link href="assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
                     <button class="button-menu-mobile open-left"> <i class="mdi mdi-menu"></i> </button>
                  </li>
                  <li class="list-inline-item">
                     <h4 class="page-title">VTC Jobs</h4>
                  </li>
               </ul>
               <nav class="navbar-custom">
                  <ul class="list-unstyled topbar-right-menu float-right mb-0">
                     <li>
                        <!-- Notification -->
                        <div class="notification-box">
                           <ul class="list-inline mb-0">
                              <li>
                                 <a href="javascript:void(0);" class="right-bar-toggle"> <i class="mdi mdi-bell-outline noti-icon"></i> </a>
                                 <div class="noti-dot"> <span class="dot"></span> <span class="pulse"></span> </div>
                              </li>
                           </ul>
                        </div>
                        <!-- End Notification bar -->
                     </li>
                     <li class="hide-phone">
                        <form class="app-search">
                           <input type="text" placeholder="Search..." class="form-control">
                           <button type="submit"><i class="fa fa-search"></i></button>
                        </form>
                     </li>
                  </ul>
               </nav>
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
                        <h4 class="m-t-0 header-title">Overall Jobs</h4>
                        <p class="text-muted font-14 m-b-30"> Shows all Jobs of all Members of the VTC </p>
                        <div class="form-group row col-md-4">
                           <label class="col-md-4 col-form-label">Jobs Filter</label>
                           <div class="col-md-8">
                              <select class="form-control gametype">
                                 <option>All</option>
                                 <option>ETS2</option>
                                 <option>ATS</option>
                              </select>
                           </div>
                        </div>
                        <table border="0" cellspacing="50" cellpadding="10">
                           <!--<tbody><tr>
                               <td>Jobs Filter</td>
                               <td><select class="form-control gametype">
                                    <option>All</option>
                                    <option>ETS2</option>
                                    <option>ATS</option>
                                 </select></td>
                              </tr>
                            </tbody>!-->
                        </table>
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap jobsSort" cellspacing="0" width="100%">
                           <thead>
                              <tr>
                                 <th>Job ID</th>
                                 <th>User Name</th>
                                 <th>Source</th>
                                 <th>Destination</th>
                                 <th aria-sort="descending">Date &amp; Time (<?php echo $timezone ?>)</th>
                                 <th>More Details</th>
                              </tr>
                           </thead>
                           <tbody id="filterJobs">
                              <!--
                                 <?php
                                 $s = "SELECT user_jobs.JobID,user_jobs.SourceCity,user_jobs.DestinationCity,user_jobs.Dated,user_jobs.GameType,user_profile.Username FROM `user_jobs` INNER JOIN `user_profile` ON user_jobs.UserID = user_profile.UserID";
                                 $q = mysqli_query($conn, $s);
                                 while ($jobdata = mysqli_fetch_array($q)) {
                                    $time = $jobdata["Dated"];
                                    //$when = new DateTime("@$time");
                                    //$date = $when->format("d/m/Y H:i:s");

                                    if ($timezone == "GMT") {
                                       $d = gmdate('r', $time);
                                       $from = "UTC";
                                       $to = "GMT";
                                       $date = date_create($d, new DateTimeZone($from))
                                          ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
                                    } else if ($timezone == "CST") {
                                       $d = gmdate('r', $time);
                                       $from = "UTC";
                                       $to = "CDT";
                                       $date = date_create($d, new DateTimeZone($from))
                                          ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
                                    }
                                    ?>
                                 <tr gametype="<?php echo $jobdata["GameType"] ?>">
                                    <td><?php echo $jobdata["JobID"] ?></td>
                                    <td><?php echo $jobdata["Username"] ?></td>
                                    <td><?php echo $jobdata["SourceCity"] ?></td>
                                    <td><?php echo $jobdata["DestinationCity"] ?></td>
                                    <td><?php echo $date ?></td>
                                    <td><a href="<?php echo "job-details?id=" . $jobdata["JobID"] ?>" class="btn btn-custom w-lg">Details</a></td>
                                 </tr>
                                 <?php
                                 }
                                 ?>!-->
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
               <!-- end row -->
               <div class="row">
                  <div class="col-12">
                     <div class="card-box table-responsive">
                        <h4 class="m-t-0 header-title">Current Month Leader Boards </h4>
                        <p class="text-muted font-14 m-b-30"> Shows Current Month Performance of every Driver<br />
                           Current month is: <strong><?php echo date("F") ?></strong>
                        </p>
                        <table id="DOM-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                           <thead class="thead-light">
                              <tr>
                                 <th>Position</th>
                                 <th>Name</th>
                                 <th>Total Jobs</th>
                                 <th>Total Distance (KM)</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              $firstday = date("m/01/Y");
                              $lastday = date("m/t/Y");
                              $epochfirst = strtotime($firstday);
                              $epochlast = strtotime($lastday);
                              $s = "SELECT user_profile.Username,user_jobs.UserID, SUM(user_jobs.Odometer) AS Odometer, COUNT(user_jobs.JobID) AS Jobs FROM `user_jobs` LEFT JOIN user_profile ON user_profile.UserID=user_jobs.UserID WHERE Dated BETWEEN '$epochfirst' AND '$epochlast' GROUP BY user_jobs.UserID ORDER BY Odometer DESC";
                              $q = mysqli_query($conn, $s);
                              while ($dom = mysqli_fetch_array($q)) {
                                 if ($dom["Jobs"] < 1) {
                                    continue;
                                 }
                                 ?>
                                 <tr>
                                    <td><span class="positionNum">0</span></td>
                                    <td><?php echo $dom["Username"] ?></td>
                                    <td><?php echo $dom["Jobs"] ?></td>
                                    <td><?php echo round($dom["Odometer"]) ?></td>
                                 </tr>
                              <?php
                              }
                              ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
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
         <a href="javascript:void(0);" class="right-bar-toggle"> <i class="mdi mdi-close-circle-outline"></i> </a>
         <h4 class="">Notifications</h4>
         <div class="notification-list nicescroll">
            <ul class="list-group list-no-border user-list">
               <li class="list-group-item">
                  <a href="#" class="user-list-item">
                     <div class="avatar"> <img src="assets/images/users/avatar-2.jpg" alt=""> </div>
                     <div class="user-desc"> <span class="name">Michael Zenaty</span> <span class="desc">There are new settings available</span> <span class="time">2 hours ago</span> </div>
                  </a>
               </li>
               <li class="list-group-item">
                  <a href="#" class="user-list-item">
                     <div class="icon bg-info"> <i class="mdi mdi-account"></i> </div>
                     <div class="user-desc"> <span class="name">New Signup</span> <span class="desc">There are new settings available</span> <span class="time">5 hours ago</span> </div>
                  </a>
               </li>
               <li class="list-group-item">
                  <a href="#" class="user-list-item">
                     <div class="icon bg-pink"> <i class="mdi mdi-comment"></i> </div>
                     <div class="user-desc"> <span class="name">New Message received</span> <span class="desc">There are new settings available</span> <span class="time">1 day ago</span> </div>
                  </a>
               </li>
               <li class="list-group-item active">
                  <a href="#" class="user-list-item">
                     <div class="avatar"> <img src="assets/images/users/avatar-3.jpg" alt=""> </div>
                     <div class="user-desc"> <span class="name">James Anderson</span> <span class="desc">There are new settings available</span> <span class="time">2 days ago</span> </div>
                  </a>
               </li>
               <li class="list-group-item active">
                  <a href="#" class="user-list-item">
                     <div class="icon bg-warning"> <i class="mdi mdi-settings"></i> </div>
                     <div class="user-desc"> <span class="name">Settings</span> <span class="desc">There are new settings available</span> <span class="time">1 day ago</span> </div>
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
   <!-- App js -->
   <script src="assets/js/jquery.core.js"></script>
   <script src="assets/js/jquery.app.js"></script>
   <script>
      if (window.module) module = window.module;
   </script>
   <script type="text/javascript">
      $(document).ready(function() {

         // Responsive Datatable
         var datatable = $('#responsive-datatable').DataTable({
            "lengthMenu": [10, 25, 50, 75, 100],
            "order": [
               [4, "desc"]
            ],
            "bProcessing": true,
            "serverSide": true,
            "responsive": true,
            "ajax": {
               "url": "./serverside/response-vtc.php",
               "type": "post",
               "data": function(d) {
                  d.timezone = "<?php echo $timezone ?>";
                  d.jobfilter = $('.gametype').val();
               }
            },
            "columnDefs": [{
               "targets": -1,
               "data": null,
               "defaultContent": "<a class='btn btn-custom w-lg'>Details</a>"
            }],
            "fnCreatedRow": function(nRow, aData, iDataIndex) {
               $(nRow).attr("gametype", aData[5]);
            }
         });

         $('.gametype').change(function() {
            datatable.ajax.reload(null, false);
         })

         $("#responsive-datatable tbody").on('click', 'a', function() {
            var data = datatable.row($(this).parents('tr')).data();
            var jobids = datatable.column(0).data().toArray();
            window.location.href = "job-details?id=" + data[0] + "&jobs=" + jobids;
         })

         var DOM = $('#DOM-datatable').DataTable({
            "lengthMenu": [
               [10, 25, 50, 100, -1],
               [10, 25, 50, 100, "All"]
            ],
            "order": [
               [3, "desc"]
            ],
            "columnDefs": [{
               targets: "_all",
               orderable: false
            }],
            "createdRow": function(row, data, dataIndex) {
               if (dataIndex == 0) {
                  name = data[1];
                  $(row).children()[1].innerHTML = name + ' <sup title="Current Driver of the Month"><i class="fas fa-crown fa-xs"></i></sup>'
                  $(row).addClass("text-warning");
               } else if (dataIndex == 1) {
                  $(row).addClass("text-info");
                  $(row).css("opacity", 0.8);
               } else if (dataIndex == 2) {
                  $(row).addClass("text-info");
                  $(row).css("opacity", 0.5);
               }
            }
         });

         DOM.on('search.dt', function() {
            DOM.column(0).nodes().each(function(cell, i) {
               cell.innerHTML = i + 1;
            });
         }).draw();

      });
   </script>
</body>

</html>