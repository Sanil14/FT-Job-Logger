<?php
include("./api/v1/database.php");
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
   header("Location: page-login?redirect=user-edit");
}

$userid = $_SESSION['userid'];
$p = "SELECT Username,Permission FROM `user_profile` WHERE SteamID='$userid'";
$r = mysqli_query($conn, $p);
$userstats = mysqli_fetch_array($r);

$profilepic = "avatars/" . $userid . ".png";
if (!file_exists($profilepic)) {
   $profilepic = "avatars/default.png";
}
$username = $userstats["Username"];

if ($userstats["Permission"] != "Admin") {
   header("Location: index");
}

$id = $_GET['id'];
$invalid_err = "";
$s = "SELECT Username,Email,DOB,Country,About,Permission,JoinDate,Roles FROM `user_profile` WHERE SteamID='$id'";
$q = mysqli_query($conn, $s);
$stats = mysqli_fetch_array($q);

$usern = $stats["Username"];
if ($stats["Permission"] == "Admin") {
   header("Location: user-manager");
}

$roles = explode(", ", $stats["Roles"]);

$old_date = $stats["DOB"] != "0000-00-00" ? date('d/m/Y', strtotime($stats["DOB"])) : "00/00/0000";
$joindate = date('d/m/Y', strtotime($stats["JoinDate"]));

?>
<!DOCTYPE html>
<html>

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="description" content="Falcon Trucking Dashboard with fully automated Jobs and user statistics">
   <meta name="author" content="Falcon Trucking">
   <link rel="shortcut icon" href="assets/images/favicon.ico">
   <title>Falcon Trucking Dashboard - User Details Editor</title>
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
            <a href="index" class="logo"><span><img src="assets/images/logo.png" alt="Falcon Logo" title="Goto Main Dashboard"></span><i class="mdi mdi-layers"></i></a>
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
                     <h4 class="page-title">Edit Exsiting User Details</h4>
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
                  <div class="col-12">
                     <div class="card-box">
                        <h4 class="m-t-0 header-title">Update details of Current User</h4>
                        <p class="text-muted font-14 m-b-30"> Some of the fields can be edited by user. Please dont edit those fields unless user not able do it</p>
                        <div class="row">
                           <div class="col-12">
                              <div class="p-20">
                                 <form class="form-horizontal" role="form">
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Email</label>
                                       <div class="col-10">
                                          <input type="email" class="form-control email" value="<?php echo $stats["Email"]; ?>">
                                       </div>
                                    </div>
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Username</label>
                                       <div class="col-10">
                                          <input type="text" class="form-control uname" disabled value="<?php echo $usern; ?>">
                                          <p class="text-muted">This field can only be created and cannot be edited by anyone.</p>
                                       </div>
                                    </div>
                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Password</label>
                                       <div class="col-8">
                                          <input type="text" disabled class="form-control password" placeholder="Reset to default if user forgets password">
                                       </div>
                                       <div class="col-2">
                                          <div class="user-img">
                                             <button type="button" class="form-control btn btn-primary mb-2 resetpass">Reset</button>
                                          </div>
                                       </div>
                                    </div>

                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Joined</label>
                                       <div class="col-10">
                                          <input type="text" class="form-control joindate" disabled value="<?php echo $joindate ?>">
                                       </div>
                                    </div>

                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Date of Birth</label>
                                       <div class="col-10">
                                          <div class="input-group">
                                             <input type="text" class="form-control birthday" Value="<?php echo $old_date ?>" id="datepicker-autoclose">
                                             <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                             </div>
                                          </div>
                                          <!-- input-group -->
                                       </div>
                                    </div>

                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">About You</label>
                                       <div class="col-10">
                                          <textarea class="form-control about" name="about" maxlength="150" rows="1" placeholder="Max 150 charecters" spellcheck="false"><?php echo $stats["About"]; ?></textarea>
                                          <p class="text-muted">Only update this field. if user has unacceptable words in it.</p>
                                       </div>
                                    </div>

                                    <div class="form-group row">
                                       <label class="col-2 col-form-label">Updates Roles</label>
                                       <div class="col-10">
                                          <select class="select2 select2-multiple" multiple="multiple" multiple">
                                             <optgroup label="Upper Management Roles">
                                                <option value="Admin">Admin</option>
                                                <option value="Manager">Manager</option>
                                                <option value="HR Manager">HR Manager</option>
                                                <option value="Support Manager">Support Manager</option>
                                                <option value="Events Manager">Events Manager</option>
                                                <option value="PR Manager">PR Manager</option>
                                             </optgroup>
                                             <optgroup label="DOM Roles">
                                                <option value="Driver of the Month">Driver of the Month</option>
                                             </optgroup>
                                             <optgroup label="HR Roles">
                                                <option value="HR">HR</option>
                                                <option value="Trainee HR">Trainee HR</option>
                                             </optgroup>
                                             <optgroup label="Support Roles">
                                                <option value="Support">Support</option>
                                                <option value="Trainee Support">Trainee Support</option>
                                             </optgroup>
                                             <optgroup label="Public Relations">
                                                <option value="Public Relations">Public Relations</option>
                                                <option value="Trainee PR">Trainee PR</option>
                                             </optgroup>
                                             <optgroup label="Events Roles">
                                                <option value="Events">Events</option>
                                                <option value="Trainee Events">Trainee Events</option>
                                             </optgroup>
                                             <optgroup label="Driver Roles">
                                                <option value="Trainee">Trainee</option>
                                                <option value="Legendary Driver">Legendary Driver</option>
                                                <option value="Professional Driver">Professional Driver</option>
                                                <option value="Skilled Driver">Skilled Driver</option>
                                             </optgroup>
                                             <optgroup label="Additional Roles">
                                                <option value="Mouse Magician">Mouse Magician</option>
                                                <option value="Vasco da Gama">Vasco da Gama</option>
                                                <option value="Wheel on Wheels">Wheel on Wheels</option>
                                             </optgroup>
                                          </select>
                                       </div>
                                    </div>

                                    <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5 submitbtn">Update User Details</button>
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
   <script src="assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
   <script type="text/javascript" src="assets/plugins/multiselect/js/jquery.multi-select.js"></script>
   <script src="assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
   <script src="assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>
   <script src="assets/plugins/moment/moment.js"></script>
   <script src="assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
   <!-- App js -->
   <script src="assets/js/jquery.core.js"></script>
   <script src="assets/js/jquery.app.js"></script>
   <script>
      if (window.module) module = window.module;
   </script>
   <script>
      $(document).ready(function() {

         presetroles = <?php echo json_encode($roles); ?>
         console.log(presetroles);

         $(".select2").val(presetroles);
         $(".select2").trigger('change');

         function removeDiv() {
            setTimeout(function() {
               console.log("Removed div")
               $("#notifyDev").fadeOut("slow")
               $("#notifyDiv").attr("class", " ")
               $("#notifyDiv").html("");
            }, 5000)
         }

         $(".resetpass").click(function() {
            $(".password").val("123456");
         })

         $(".submitbtn").click(function() {
            var bday = $(".birthday").val();
            var aboutme = $(".about").val();
            var email = $(".email").val();
            var id = JSON.parse("<?php echo $id ?>")
            var pass = $(".password").val();

            var roles = $(".select2 option:selected").toArray().map(item => item.text);
            console.log(roles);

            if (!email) {
               $("#notifyDiv").attr("class", "alert alert-danger")
               $("#notifyDiv").html(`<strong>Oh snap!</strong> Email is missing`);
               return removeDiv();
            }
            let datatosend = {};
            if (email) datatosend.email = email;
            if (aboutme) datatosend.about = aboutme;
            if (bday) datatosend.dob = bday;
            if (pass) datatosend.password = pass;
            if (roles.length > 0) {
               datatosend.roles = roles.join(", ")
            } else {
               datatosend.roles = "";
            }
            datatosend.id = id;

            //var datatosend = "dob="+bday+"&country="+country+"&about="+aboutme;

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
            format: "dd/mm/yyyy",
            autoclose: true,
            todayHighlight: true
         });

      });
   </script>

   <script>
      $(document).ready(function() {

         // Select2
         $(".select2").select2({
            placeholder: {
               id: '-1',
               text: "Add roles to user..."
            }
         });
      });
   </script>

</body>

</html>