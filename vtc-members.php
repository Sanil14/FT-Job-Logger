<?php
   header('Content-Type:text/html; charset=iso-8859-1');
   include("./api/v1/database.php");
   session_start();
   if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
     header("Location: page-login?redirect=vtc-members");
   }
   $id = $_SESSION['userid'];
   
   $s = "SELECT Username,Permission FROM `user_profile` WHERE UserID='$id'";
   $q = mysqli_query($conn,$s);
   $userstats = mysqli_fetch_array($q);
   
   $profilepic = "avatars/".$id.".png";
   if(!file_exists($profilepic)) {
     $profilepic = "avatars/default.png";
   }
   $username = $userstats["Username"];

   function rankColor($option) {
     $output = ($option == "Owner") ? "text-danger" :
               (($option == "HR Manager" || $option == "HR" || $option == "HR Trainee") ? "text-primary" :
               (($option == "Events Manager" || $option == "Manager") ? "text-success" :
               (($option == "Public Relations") ? "text-third" :
               (($option == "Support" || $option == "Support Trainee") ? "text-success" :
               (($option == "French Lead") ? "text-third" :
               (($option == "DOM") ? "text-warning" :
               (($option == "Driver") ? "text-info" : 
                                        "text-muted")))))));
     return $output;
   }
   
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
      <meta name="author" content="Coderthemes">
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
                  <nav class="navbar-custom">
                     <ul class="list-unstyled topbar-right-menu float-right mb-0">
                        <li>
                           <!-- Notification -->
                           <div class="notification-box">
                              <ul class="list-inline mb-0">
                                 <li>
                                    <a href="javascript:void(0);" class="right-bar-toggle">
                                    <i class="mdi mdi-bell-outline noti-icon"></i>
                                    </a>
                                    <div class="noti-dot">
                                       <span class="dot"></span>
                                       <span class="pulse"></span>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                           <!-- End Notification bar -->
                        </li>
                        <li class="hide-phone">
                           <form class="app-search">
                              <input type="text" placeholder="Search..."
                                 class="form-control">
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
         <?php include("left-sidebar.php")?>
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
                    $s = "SELECT UserID,Username,About,DOB,Roles,JoinDate,Country,Permission FROM `user_profile`";
                    $q = mysqli_query($conn,$s);
                    while($userstats = mysqli_fetch_array($q))
                    {
                      if ($userstats["Roles"] == "Driver") {
                        continue;
                      }
                      $pfp = "avatars/".$userstats["UserID"].".png";
                      if(!file_exists($pfp)) {
                        $pfp = "avatars/default.png";
                      }
                      $date = $userstats["DOB"] != "0000-00-00" ? date('d/m/Y', strtotime($userstats["DOB"])) : "Not Provided";
                      $joined = date('d/m/Y', strtotime($userstats["JoinDate"]));
                      $about = ($userstats["About"] != null) ? $userstats["About"] : "I am too lazy to add my bio";
                    ?>
                     <div class="col-xl-3 col-md-6 sortStaff" data-date="<?php echo $userstats["JoinDate"]?>">
                        <div class="text-center card-box members">
                            <div>
                            <img <?php echo 'src="'.$pfp.'"'?> title="<?php echo $userstats["Username"]; ?>" class="rounded-circle thumb-xl img-thumbnail m-b-10" alt="profile-image">
                              <p class="text-muted font-13 m-b-30">
                                <?php echo $about?>
                              </p>
                              <div class="text-left">
                                 <p class="text-muted font-13"><strong>Full Name :</strong> <span class="m-l-15"><?php echo $userstats["Username"];?></span></p>
                                 <p class="text-muted font-13"><strong>DOB:</strong> <span class="m-l-15"><?php echo $date?></span></p>
                                 <p class="text-muted font-13"><strong>Joined on:</strong> <span class="m-l-15"><?php echo $joined;?></span></p>
                                 <p class="text-muted font-13"><strong>Country :</strong> <span class="m-l-15"><?php echo $userstats["Country"];?></span></p>
                                 <p class="font-13 <?php echo rankColor($userstats["Roles"])?>"><strong>Role :</strong><span class="m-l-15"><?php echo $userstats["Roles"];?></span></p>
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
                    $s = "SELECT UserID,Username,About,DOB,Roles,JoinDate,Country,Permission FROM `user_profile`";
                    $q = mysqli_query($conn,$s);
                    while($userstats = mysqli_fetch_array($q))
                    {
                      if ($userstats["Roles"] != "Driver") {
                        continue;
                      }
                      $pfp = "avatars/".$userstats["UserID"].".png";
                      if(!file_exists($pfp)) {
                        $pfp = "avatars/default.png";
                      }
                      $date = $userstats["DOB"] != "0000-00-00" ? date('d/m/Y', strtotime($userstats["DOB"])) : "Not Provided";
                      $joined = date('d/m/Y', strtotime($userstats["JoinDate"]));
                      $about = ($userstats["About"] != null) ? $userstats["About"] : "I am too lazy to add my bio";
                    ?>
                     <div class="col-xl-3 col-md-6 sortDriver" data-date="<?php echo $userstats["JoinDate"]?>">
                        <div class="text-center card-box members">
                            <div>
                              <img <?php echo 'src="'.$pfp.'"'?> title="<?php echo $userstats["Username"]; ?>" class="rounded-circle thumb-xl img-thumbnail m-b-10" alt="profile-image">
                              <p class="text-muted font-13 m-b-30">
                                <?php echo $about?>
                              </p>
                              <div class="text-left">
                                 <p class="text-muted font-13"><strong>Full Name :</strong> <span class="m-l-15"><?php echo $userstats["Username"];?></span></p>
                                 <p class="text-muted font-13"><strong>DOB:</strong> <span class="m-l-15"><?php echo $date?></span></p>
                                 <p class="text-muted font-13"><strong>Joined on:</strong> <span class="m-l-15"><?php echo $joined;?></span></p>
                                 <p class="text-muted font-13"><strong>Country :</strong> <span class="m-l-15"><?php echo $userstats["Country"];?></span></p>
                                 <p class="font-13 <?php echo rankColor($userstats["Roles"])?>"><strong>Role :</strong><span class="m-l-15"><?php echo $userstats["Roles"];?></span></p>
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
               Copyrights <i class="fa fa-copyright"></i> 2018<script>new Date().getFullYear()>2018&&document.write("-"+new Date().getFullYear());</script>, Falcon Trucking VTC. All Rights Reserved.
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
      <script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
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
      <script>if (window.module) module = window.module;</script>
      <script>
      $(document).ready(function() {
        console.log("HEllo")

        function parseDate(input) {
          var parts = input.match(/(\d+)/g);
          // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
          return new Date(parts[0], parts[1]-1, parts[2]); //     months are 0-based
          }

        function getSorted(selector, attrName) {
          return $($(selector).toArray().sort(function(a, b){
          var aVal = parseDate(a.getAttribute(attrName)),
              bVal = parseDate(b.getAttribute(attrName));
          return aVal - bVal;
          }));
          }
          let staff = getSorted(".sortStaff","data-date")
          $(".staff").html(staff);
          let driver = getSorted(".sortDriver","data-date")
          $(".drivers").html(driver);
    });
      </script>
   </body>
</html>