<?php
header('Content-Type:text/html; charset=iso-8859-1');
include("./api/v1/database.php");
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
   header("Location: page-login?redirect=user-manager");
}
$id = $_SESSION['userid'];

$s = "SELECT Username,Permission FROM `user_profile` WHERE UserID='$id'";
$q = mysqli_query($conn, $s);
$userstats = mysqli_fetch_array($q);

$profilepic = "avatars/" . $id . ".png";
if (!file_exists($profilepic)) {
   $profilepic = "avatars/default.png";
}
$username = $userstats["Username"];

if ($userstats["Permission"] != "Admin") {
   header("Location: index");
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

?>
<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Falcon Trucking Dashboard with fully automated Jobs and user statistics">
   <meta name="author" content="Falcon Trucking">
   <link rel="shortcut icon" href="assets/images/favicon.ico">
   <title>Falcon Trucking Dashboard - Users Manager</title>
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
                     <h4 class="page-title">Manage Users</h4>
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
                        <form class="form-horizontal">

                           <div class="row">
                              <label class="col-xl-1 col-form-label">User Search</label>
                              <div class="col-xl-8 m-b-20">
                                 <input type="text" class="form-control searchtext" placeholder="Search the user to find easily">
                              </div>
                              <div class="col-xl-3">
                                 <div class="user-img">
                                    <button class="form-control btn btn-primary waves-effect waves-light mb-2 searchbtn">Search</button>
                                 </div>
                              </div>
                           </div>

                        </form>
                     </div>
                  </div>
               </div>

               <div class="row drivers">
               </div>

            </div>
            <!-- end row -->
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
         $(".searchbtn").click(function(e) {
            e.preventDefault();
            if ($(".drivers div").length > 0) {
               $(".drivers div").remove();
            }
            $.ajax({
               type: "get",
               url: "getUser.php",
               data: {
                  name: $(".searchtext").val()
               },
               success: function(resp) {
                  $(".drivers").append(resp);
               }
            })
         })

         $(document).on("click", ".editbtn", function() {
            let parent = $(this).parent().parent().parent().get(0).dataset.userid;
            window.location.href = "user-edit?id=" + parent;
         })

         //Warning Message
         $("body").on("click", ".deletebtn", function() {
            let id = $(this).parent().parent().parent().get(0).dataset.userid;
            if (!id || id < 1 || id == undefined) return;
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
                  url: "deleteUser.php",
                  data: {
                     userid: id
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
            }).catch(swal.noop);
         });
      })
   </script>

</body>

</html>