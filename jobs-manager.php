<?php
include("./api/v1/database.php");
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
    header("Location: page-login?redirect=new-user");
}
$id = $_SESSION['userid'];
$invalid_err = "";
$s = "SELECT Username,Email,DOB,Country,About,Permission,Preferences FROM `user_profile` WHERE UserID='$id'";
$q = mysqli_query($conn, $s);
$userstats = mysqli_fetch_array($q);

$profilepic = "avatars/" . $id . ".png";
if (!file_exists($profilepic)) {
    $profilepic = "avatars/default.png";
}
$username = $userstats["Username"];
$pref = json_decode($userstats["Preferences"]);

if ($userstats["Permission"] != "Admin") {
    header("Location: index");
}

$r = "SELECT MAX(JobID) AS maxjob FROM `user_jobs`";
$maxjobid = mysqli_fetch_array(mysqli_query($conn, $r));


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Falcon Trucking Dashboard with fully automated Jobs and user statistics">
    <meta name="author" content="Falcon Trucking">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <title>Falcon Trucking Dashboard - Jobs Manager</title>
    <!-- X-editable css -->
    <link type="text/css" href="assets/plugins/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
    <link href="assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" />
    <link href="assets/plugins/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" />
    <link href="assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="assets/plugins/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <!-- Sweet Alert css -->
    <link href="assets/plugins/sweet-alert/sweetalert2.css" rel="stylesheet" type="text/css" />

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
                <a href="index.html" class="logo"><span><img src="assets/images/logo.png" alt="Falcon Logo" title="Goto Main Dashboard"></span><i class="mdi mdi-layers"></i></a>
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
                            <h4 class="page-title">Jobs Manager</h4>
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
                        <div class="col-12 card-box">
                            <div class="p-20">
                                <form class="form-horizontal" role="form">

                                    <div class="row">
                                        <label class="col-xl-1 col-form-label">Job Search</label>
                                        <div class="col-xl-8 m-b-20">
                                            <input type="text" class="form-control jobid" placeholder="Search using the Job ID to find a job | Latest Job ID: <?php echo $maxjobid["maxjob"] ?>">
                                        </div>
                                        <div class="col-xl-3">
                                            <div class="user-img">
                                                <button type="button" class="form-control btn btn-primary waves-effect waves-light mb-2 searchbutton">Search</button>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                            <div id="notifyDiv"></div>

                        </div>
                    </div>

                    <div class="row jobtable">
                    </div>
                    <!-- end row -->

                    <div class="row jobfulltable">
                    </div>
                </div>
                <!-- end row -->

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

    <!-- Plugins Js -->
    <script src="assets/plugins/switchery/switchery.min.js"></script>
    <script src="assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
    <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
    <script type="text/javascript" src="assets/plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
    <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>
    <script src="assets/plugins/moment/moment.js"></script>
    <script src="assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="assets/plugins/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    <script src="assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>

    <!-- Sweet Alert Js  -->
    <script src="assets/plugins/sweet-alert/sweetalert2.js"></script>

    <!-- App js -->
    <script src="assets/js/jquery.core.js"></script>
    <script src="assets/js/jquery.app.js"></script>
    <script>
        if (window.module) module = window.module;
    </script>

    <script>
        $(document).ready(function() {

            function removeDiv() {
                setTimeout(function() {
                    console.log("Removed div")
                    $("#notifyDev").fadeOut("slow")
                    $("#notifyDiv").attr("class", " ")
                    $("#notifyDiv").html("");
                }, 5000)
            }

            $(".searchbutton").click(function(e) {
                e.preventDefault();
                if ($(".jobtable div").length > 0) {
                    $(".jobtable div").remove();
                    if ($(".jobfulltable div").length > 0) {
                        $(".jobfulltable div").remove();
                    }
                }
                $.ajax({
                    type: "get",
                    url: "getJob.php",
                    data: {
                        full: 0,
                        timezone: "<?php echo $pref->Timezone ?>",
                        currency: "<?php echo $pref->Currency ?>",
                        distance: "<?php echo $pref->Distance ?>",
                        fuel: "<?php echo $pref->Fuel ?>",
                        mass: "<?php echo $pref->Mass ?>,",
                        jobid: $(".jobid").val()
                    },
                    success: function(resp) {
                        if (resp == 2) {
                            $("#notifyDiv").attr("class", "alert alert-danger")
                            $("#notifyDiv").html(`<strong>Oh snap!</strong> Jobs with that ID was not found`);
                            return removeDiv();
                        } else if (resp == 1) {
                            $("#notifyDiv").attr("class", "alert alert-danger")
                            $("#notifyDiv").html(`<strong>Oh snap!</strong> Multiple jobs found!`);
                            return removeDiv();
                        } else {
                            $(".jobtable").append(resp);
                        }
                    }
                })
            })

            $(document).on("click", ".moredetails", function(e) {
                var jobid = $(this).parent().parent().get(0).dataset.jobid;
                e.preventDefault();
                if ($(".jobfulltable div").length > 0) {
                    $(".jobfulltable div").remove();
                    return;
                }
                $.ajax({
                    type: "get",
                    url: "getJob.php",
                    data: {
                        full: 1,
                        timezone: "<?php echo $pref->Timezone ?>",
                        currency: "<?php echo $pref->Currency ?>",
                        distance: "<?php echo $pref->Distance ?>",
                        fuel: "<?php echo $pref->Fuel ?>",
                        mass: "<?php echo $pref->Mass ?>,",
                        jobid: jobid
                    },
                    success: function(resp) {
                        $(".jobfulltable").append(resp);
                    }
                })
            })

            $(document).on("click", ".deletejob", function(e) {
                var jobid = $(this).parent().parent().get(0).dataset.jobid;
                var userid = $(this).parent().parent().get(0).dataset.userid;
                e.preventDefault();
                if (!jobid || jobid < 1 || jobid == undefined) return;
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4fa7f3',
                    cancelButtonColor: '#d57171',
                    confirmButtonText: 'Yes',
                }).then(function() {
                    $.ajax({
                        type: "get",
                        url: "deleteJob.php",
                        data: {
                            jobid: jobid,
                            userid: userid
                        },
                        success: function(resp) {
                            if (resp == 1) {
                                swal(
                                    'Deleted!',
                                    'The user has been deleted.',
                                    'success'
                                )
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                console.log(resp);
                            }
                        }
                    })
                })
            })
        })
    </script>

</body>

</html>