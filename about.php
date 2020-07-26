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

    <title>Welcome to Falcon Trucking Dashboard - About VTC</title>

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
                            <h4 class="page-title">About Falcon Trucking VTC</h4>
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
                    </nav> -->
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
                        <div class="col-12">
                            <div class="card-box">
                                <div class="profile-info-name">
                                    <img src="assets/images/profile.jpg" class="img-thumbnail" alt="profile-image">

                                    <div class="profile-info-detail">
                                        <div class="profile-border-bottom">
                                            <h4 class="m-0">FALCON TRUCKING</h4>
                                            <p class="text-muted m-b-20">Trucking your imagination</p>
                                            <p style="text-align: justify;">Like all major enterprises, falcon trucking began its operations with a humble beginning. We established this VTC on 10th March 2018 with the hope of becoming the most friendly and professional community that we have now become.The drivers are provided with an opportunity to work with all the factors they might face in a real life company . We have implemented all the necessary VTC features along with more innovative ideas which is going to help the community for the present as well as future.When we started Falcon Trucking, we were only concentrating on ETS2, however on 20th August 2018 we took our first steps into ATS Division. Unlike other VTC's, our recruitment process is implemented systematical and we do not entertain any form of trolling.</p>
                                        </div>
                                        <div class="col-12">
                                            <div class="button-list m-t-20">
                                                <a href="https://www.facebook.com/falcontruckingvtc/" class="btn btn-facebook btn-sm waves-effect waves-light" target="_blank" title="Follow us on facebook">
                                                    <i class="fa fa-facebook"></i>
                                                </a>

                                                <a href="https://www.twitter.com/falcontrucking/" class="btn btn-sm btn-twitter waves-effect waves-light" target="_blank" title="Follow us on Twitter">
                                                    <i class="fa fa-twitter"></i>
                                                </a>

                                                <a href="https://www.facebook.com/falcontruckingvtc/" class="btn btn-sm btn-instagram waves-effect waves-light" target="_blank" title="Follow us on Instagram">
                                                    <i class="fa fa-instagram"></i>
                                                </a>

                                                <a href="https://www.youtube.com/falcontrucking" class="btn btn-sm btn-instagram waves-effect waves-light" target="_blank" title="Follow us on Youtube">
                                                    <i class="fa fa-youtube-play"></i>
                                                </a>

                                                <a href="https://truckersmp.com/vtc/97" class="btn btn-sm btn-instagram waves-effect waves-light" target="_blank" title="Our Truckers MP VTC page">
                                                    <i class="fa fa-truck"></i>
                                                </a>

                                                <a href="https://steamcommunity.com/groups/falcon-trucking" class="btn btn-sm btn-instagram waves-effect waves-light" target="_blank" title="Join our Steam group">
                                                    <i class="fa fa-steam-square"></i>
                                                </a>


                                            </div>

                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>


                                <div id="notifyDiv"></div>
                                <!-- end row -->
                            </div>
                            <!-- end card-box -->
                        </div>
                        <!-- end col -->
                    </div>


                    <div class="alert alert-info text-center">
                        <strong>VTC</strong> Timeline. We never stopped working and we never will do.
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="timeline">
                                <article class="timeline-item alt">
                                    <div class="text-right">
                                        <div class="time-show first">
                                            <a href="#" class="btn btn-custom w-lg">2020</a>
                                        </div>
                                    </div>
                                </article>
                                <article class="timeline-item alt">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow-alt"></span>
                                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-danger">June 23rd</h4>
                                                <p class="timeline-date text-muted"><small>Redesign of New Website</small></p>
                                                <p>We made new website with latest content, cross device compatible and Lovely theme. With this new thechnology, we can guarentee that we can provide better playtime and more frequent updates</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <article class="timeline-item ">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow"></span>
                                                <span class="timeline-icon bg-success"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-success">March 14th</h4>
                                                <p class="timeline-date text-muted"><small>Falcon Trucking's 2nd Anniversary</small></p>
                                                <p>Falcon Trucking Celebrated its 2nd Anniversary with more than 300+ Truckers of 20+ VTCs and Public.</p>

                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <article class="timeline-item alt">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow-alt"></span>
                                                <span class="timeline-icon bg-primary"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-primary">Jan 13th</h4>
                                                <p class="timeline-date text-muted"><small>Testing of New Logger</small></p>
                                                <p>After lot of testing locally, we have intregated new logger to dashboard and starting testing realtime with some driver those are called alpha testers.</p>
                                                <!--<div class="album">
                                                    <a href="#">
                                                        <img alt="" src="assets/images/small/img1.jpg">
                                                    </a>
                                                    <a href="#">
                                                        <img alt="" src="assets/images/small/img2.jpg">
                                                    </a>
                                                    <a href="#">
                                                        <img alt="" src="assets/images/small/img3.jpg">
                                                    </a>
                                                </div>-->
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
								<article class="timeline-item alt">
                                    <div class="text-right">
                                        <div class="time-show first">
                                            <a href="#" class="btn btn-custom w-lg">2019</a>
                                        </div>
                                    </div>
                                </article>
                                <article class="timeline-item">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow"></span>
                                                <span class="timeline-icon bg-purple"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-purple">Nov 9</h4>
                                                <p class="timeline-date text-muted"><small>Introduction to Promods Convoys</small></p>
                                                <p>With the Support of Promods with multiplayer, we started doing promods Convoys every 2nd week of month.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <article class="timeline-item alt">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow-alt"></span>
                                                <span class="timeline-icon bg-success"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-success">Aug 23</h4>
                                                <p class="timeline-date text-muted"><small>Premade official Mods for MP</small></p>
                                                <p>To avoid the hassle of Making official Truck and Trailer by watching videos, we made mods where players can just buy them and use it. That simple.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
								
								<article class="timeline-item">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow"></span>
                                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle"> </i></span>
                                                <h4 class="text-danger">May 21</h4>
                                                <p class="timeline-date text-muted"><small>Moved website to Better Hosting</small></p>
                                                <p>We have reached certain limit where our bots, website and dashboard demanded the upgrade of our Hosting. We added extra funds and moved finally.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
								<article class="timeline-item alt">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow-alt"></span>
                                                <span class="timeline-icon bg-primary"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-primary">March 9th</h4>
                                                <p class="timeline-date text-muted"><small>Our 1st Anniversary</small></p>
                                                <p>We faced lot ups and downs during our first years, We overcame all of that and successfully celebrated 1st anniversary. It was very proud moment for us.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>

                                <article class="timeline-item alt">
                                    <div class="text-right">
                                        <div class="time-show">
                                            <a href="#" class="btn btn-custom w-lg">2018</a>
                                        </div>
                                    </div>
                                </article>
                                <article class="timeline-item">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow"></span>
                                                <span class="timeline-icon bg-warning"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-warning">OCT 21</h4>
                                                <p class="timeline-date text-muted"><small>New Domain falconites.com</small></p>
                                                <p>We were using a free domain called falcontrucking.ml. We were not happy with it and falcontrucking domain was not available for purchase. Here at falcon Trucking we call every driver as a falconite. So we thought introducing that to our domain which we called it falconites.com</p>

                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <article class="timeline-item alt">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow-alt"></span>
                                                <span class="timeline-icon bg-primary"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-primary">Sep 16</h4>
                                                <p class="timeline-date text-muted"><small>Redesigned our logo</small></p>
                                                <p>We always wanted our logo to be conceptual and beautiful. After many sleepless nights of work, we finally introduced our current logo. It justifies both our name and purpose of the logo.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>

                                <article class="timeline-item">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow"></span>
                                                <span class="timeline-icon bg-success"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-success">Aug 20</h4>
                                                <p class="timeline-date text-muted"><small>Introduction to ATS Convoys</small></p>
                                                <p>When we started Falcon Trucking, we were only concentrating on ETS2, however, on 20th August 2018, we took our first steps into ATS Division.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <article class="timeline-item alt">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow-alt"></span>
                                                <span class="timeline-icon bg-warning"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-warning">March - August</h4>
                                                <p class="timeline-date text-muted"><small>Hard phase for us</small></p>
                                                <p>We launched this VTC, We created discord but we do need drivers too. This is the phase where we introduced lot of things like website. ETS2 and ATS paintjobs, Convoy system, Application form etc. We did advertise a lot, ingame, TMP Servers and all possible platforms. We did regular convoys, designed a beautiful website. finally people started noticing us and started joining us.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>

                                <article class="timeline-item">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow"></span>
                                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle"> </i></span>
                                                <h4 class="text-danger">March 10</h4>
                                                <p class="timeline-date text-muted"><small>Launch of Falcon Trucking</small></p>
                                                <p>With the passion of Virtual Trucking, We wanted to gather people just like us, We wanted to make a place where all drivers can meet up and driver together. With all the resorces we had that day, we proudly lauched Falcon Trucking</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>

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