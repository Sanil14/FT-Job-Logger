<?php
include("./api/v1/database.php");
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
    header("Location: page-login");
}
$id = $_SESSION['userid'];

$s = "SELECT user_profile.Username,user_profile.Permission,user_stats.TotalKM,user_stats.TotalJobs,user_stats.TotalIncome,user_stats.TotalFuel FROM `user_profile` INNER JOIN `user_stats` ON user_profile.SteamID = user_stats.SteamID WHERE user_profile.SteamID='$id'";
$v = "SELECT * FROM `vtc_stats`";
$q = mysqli_query($conn, $s);
$userstats = mysqli_fetch_array($q);
$p = mysqli_query($conn, $v);
$vtcstats = mysqli_fetch_array($p);

$profilepic = "avatars/" . $id . ".png";
if (!file_exists($profilepic)) {
    $profilepic = "avatars/default.png";
}
$username = $userstats["Username"];
$totalKM = $userstats["TotalKM"];
$totalJobs = $userstats["TotalJobs"];
$totalIncome = $userstats["TotalIncome"];
$totalFuel = $userstats["TotalFuel"];

$vtotalKM = $vtcstats["TotalKM"];
$vtotalJobs = $vtcstats["TotalJobs"];
$vtotalIncome = $vtcstats["TotalIncome"];
$vtotalFuel = $vtcstats["TotalFuel"];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Falcon Trucking Dashboard with fully automated Jobs and user statistics">
    <meta name="author" content="Falcon Trucking">

    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <title>Welcome to Falcon Trucking Dashboard</title>

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="assets/plugins/morris/morris.css">

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
                            <h4 class="page-title">User Dashboard</h4>
                        </li>
                    </ul>

                    <!--<nav class="navbar-custom">

                        <ul class="list-unstyled topbar-right-menu float-right mb-0">

                            <li>
                                <!-- Notification
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
                </div><!-- end container -->
            </div><!-- end navbar -->
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
                        <div class="col-md-12">
                            <div class="card-box widget-user alert alert-info">
                                <div class="text-center">
                                    <h2 class="text-custom">YOUR STATS</h2>
                                    <h5>YOUR OVERALL PERFORMANCE IN THIS VTC</h5>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-3 col-md-6">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-custom" data-plugin="counterup"><?php echo number_format($totalKM) ?></h2>
                                    <h5><span>KM</span>&nbsp;Travelled</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-pink" data-plugin="counterup"><?php echo number_format($totalJobs) ?></h2>
                                    <h5>Total Jobs</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-warning" data-plugin="counterup"><?php echo number_format($totalIncome) ?></h2>
                                    <h5>Income&nbsp;<span>(Euros)</span></h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-info" data-plugin="counterup"><?php echo number_format($totalFuel) ?></h2>
                                    <h5>Fuel Consumed&nbsp;<span>(Ltrs)</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- end row -->


                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-box widget-user alert alert-info">
                                <div class="text-center">
                                    <h2 class="text-custom">VTC STATS</h2>
                                    <h5>OVERALL PERFORMANCE OF THIS VTC</h5>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-3 col-md-6">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-custom" data-plugin="counterup"><?php echo number_format($vtotalKM) ?></h2>
                                    <h5><span>KM</span>&nbsp;Travelled</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-custom" data-plugin="counterup"><?php echo number_format($vtotalJobs) ?></h2>
                                    <h5>Total Jobs</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-custom" data-plugin="counterup"><?php echo number_format($vtotalIncome) ?></h2>
                                    <h5>Income&nbsp;<span>(Euros)</span></h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6">
                            <div class="card-box widget-user">
                                <div class="text-center">
                                    <h2 class="text-custom" data-plugin="counterup"><?php echo number_format($vtotalFuel) ?></h2>
                                    <h5>Fuel Consumed&nbsp;<span>(Ltrs)</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- end row -->

                </div> <!-- container -->

            </div> <!-- content -->

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
    <script src="assets/plugins/counterup/jquery.counterup.min.js"></script>
    <script src="assets/plugins/waypoints/jquery.waypoints.min.js"></script>
    <!-- KNOB JS -->
    <!--[if IE]>
        <script type="text/javascript" src="assets/plugins/jquery-knob/excanvas.js"></script>
        <![endif]-->
    <script src="assets/plugins/jquery-knob/jquery.knob.js"></script>

    <!--Morris Chart-->
    <script src="assets/plugins/morris/morris.min.js"></script>
    <script src="assets/plugins/raphael/raphael-min.js"></script>

    <!-- Dashboard init -->
    <script src="assets/pages/jquery.dashboard.js"></script>

    <!-- App js -->
    <script src="assets/js/jquery.core.js"></script>
    <script src="assets/js/jquery.app.js"></script>
    <script>
        if (window.module) module = window.module;
    </script>

</body>

</html>