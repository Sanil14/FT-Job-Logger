<?php
include("./api/v1/database.php");
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
   header("Location: page-login?redirect=profile");
}
$id = $_SESSION['userid'];
$invalid_err = "";
$s = "SELECT Username,Email,DOB,Country,About,Permission FROM `user_profile` WHERE SteamID='$id'";
$q = mysqli_query($conn, $s);
$userstats = mysqli_fetch_array($q);

$profilepic = "avatars/" . $id . ".png";
if (!file_exists($profilepic)) {
   $profilepic = "avatars/default.png";
}
$username = $userstats["Username"];

$old_date = date('d/m/Y', strtotime($userstats["DOB"]));

?>
<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Falcon Trucking Dashboard with fully automated Jobs and user statistics">
   <meta name="author" content="Falcon Trucking">
   <link rel="shortcut icon" href="assets/images/favicon.ico">
   <title>Falcon Trucking Dashboard - User Profile</title>
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
                     <h4 class="page-title">Your Profile</h4>
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
                     <div class="card-box">
                        <h4 class="m-t-0 header-title">Your Profile</h4>
                        <div class="row">
                           <div class="col-12">
                              <div class="p-20">
                                 <form class="form-horizontal" method="post" action="profile.php" enctype="multipart/form-data">
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Your Picture</label>
                                       <div class="col-10">
                                          <div class="user-img upload-img">
                                             <img src="<?php echo $profilepic ?>" id="img" alt="user-img" title="Upload a picture to replace it" class="rounded-circle user-thum img-responsive upload-img">
                                          </div>
                                       </div>
                                    </div>
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Username</label>
                                       <div class="col-10">
                                          <p class="form-control-static" title="This cannot be changed"><?php echo $username; ?></p>
                                       </div>
                                    </div>
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Email</label>
                                       <div class="col-10">
                                          <p class="form-control-static" title="This cannot be changed"><?php echo $userstats["Email"]; ?></p>
                                       </div>
                                    </div>
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Birthday</label>
                                       <div class="col-10">
                                          <div class="input-group">
                                             <input type="text" class="form-control birthday" name="date" placeholder="dd/mm/yyyy" id="datepicker-autoclose" value="<?php echo $old_date; ?>">
                                             <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                             </div>
                                          </div>
                                          <!-- input-group -->
                                       </div>
                                    </div>
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Country</label>
                                       <div class="col-10">
                                          <input type="text" class="form-control country" name="country" placeholder="Enter your Country" value="<?php echo $userstats["Country"] ?>">
                                       </div>
                                    </div>
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Change Picture</label>
                                       <div class="col-8">
                                          <input type="file" name="fileToUpload" id="fileToUpload" class="form-control input" accept=".jpg, .png, .jpeg, .webp">
                                       </div>
                                       <div class="col-2">
                                          <div class="user-img">
                                             <button type="button" id="butUpload" class="form-control btn btn-primary mb-2">Upload</button>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">About You</label>
                                       <div class="col-10">
                                          <textarea class="form-control about" name="about" maxlength="150" rows="1" placeholder="Max 150 charecters"><?php echo $userstats["About"]; ?></textarea>
                                          <p class="text-muted">This section will be seen by everyone, so be creative and refrain from using swear words or abusing the feature.</p>
                                       </div>
                                    </div>
                                    <button type="button" name="saveProfile" id="submitBut" class="btn btn-info waves-effect waves-light w-lg m-b-5">Update Details</button>
                                 </form>
                              </div>
                           </div>
                        </div>
                        <div id="notifyDiv"></div>
                        <!-- end row -->
                     </div>
                     <!-- end card-box -->
                  </div>
                  <!-- end col -->
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
   <!-- App js -->
   <script src="assets/js/jquery.core.js"></script>
   <script src="assets/js/jquery.app.js"></script>
   <!-- XEditable Plugin -->
   <script src="assets/plugins/moment/moment.js"></script>
   <script type="text/javascript" src="assets/plugins/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
   <script type="text/javascript" src="assets/pages/jquery.xeditable.js"></script>
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

         $("#butUpload").click(function() {
            var fd = new FormData();
            var files = $("#fileToUpload")[0].files[0];
            fd.append('fileToUpload', files);

            $.ajax({
               url: 'upload.php',
               type: 'post',
               data: fd,
               contentType: false,
               processData: false,
               success: function(response) {
                  if (response != 0) {
                     console.log(response)
                     $("#img").attr("src", response);
                     $("#sidebar_img").attr("src", response);
                     $("#fileToUpload").val(null);
                  } else {
                     alert("File was not uploaded")
                  }
               }
            })
         })

         $("#submitBut").click(function() {
            var bday = $(".birthday").val();
            var country = $(".country").val();
            var aboutme = $(".about").val();

            if (!bday) {
               $("#notifyDiv").attr("class", "alert alert-danger")
               $("#notifyDiv").html(`<strong>Oh snap!</strong> Date is not added`);
               return removeDiv();
            }
            if (!country) {
               $("#notifyDiv").attr("class", "alert alert-danger")
               $("#notifyDiv").html(`<strong>Oh snap!</strong> Country is not added`);
               return removeDiv();
            }
            if (!aboutme) {
               $("#notifyDiv").attr("class", "alert alert-danger")
               $("#notifyDiv").html(`<strong>Oh snap!</strong> About me is not added`);
               return removeDiv();
            }

            var datatosend = "dob=" + bday + "&country=" + country + "&about=" + aboutme;

            $.ajax({
               url: 'updateProfile.php',
               type: 'POST',
               data: datatosend,
               async: true,
               success: function(response) {
                  console.log(response);
                  if (response == 1) {
                     $("#notifyDiv").attr("class", "alert alert-success")
                     $("#notifyDiv").html(`User Settings are saved!`);
                     return removeDiv();
                  } else if (response == 0) {
                     $("#notifyDiv").attr("class", "alert alert-danger")
                     $("#notifyDiv").html(`<strong>Oh snap!</strong> Something went wrong!`);
                     return removeDiv();
                  } else {
                     $("#notifyDiv").attr("class", "alert alert-danger")
                     $("#notifyDiv").html(`<strong>Oh snap!</strong> Something went wrong!`);
                     return removeDiv();
                  }
               }
            })
         })

         // Date Picker
         jQuery('#datepicker-autoclose').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
         });
      });
   </script>
</body>

</html>