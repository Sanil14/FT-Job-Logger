<?php
   include("./api/v1/database.php");
   session_start();
   if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
     header("Location: page-login?redirect=new-user");
   }
   $id = $_SESSION['userid'];
   $invalid_err = "";
   $s = "SELECT Username,Email,DOB,Country,About,Permission FROM `user_profile` WHERE UserID='$id'";
   $q = mysqli_query($conn,$s);
   $userstats = mysqli_fetch_array($q);
   
   $profilepic = "avatars/".$id.".png";
   if(!file_exists($profilepic)) {
     $profilepic = "avatars/default.png";
   }
   $username = $userstats["Username"];
   
   if($userstats["Permission"] != "Admin") {
     header("Location: index");
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
      <title>Falcon Trucking Dashboard</title>
      <!-- X-editable css -->
      <link type="text/css" href="assets/plugins/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet">
      <link href="assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet" />
      <link href="assets/plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
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
                        <h4 class="page-title">User Creation</h4>
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
                     <div class="col-12">
                        <div class="card-box">
                           <h4 class="m-t-0 header-title">Create New User</h4>
                           <div class="row">
                              <div class="col-12">
                                 <div class="p-20">
                                    <form class="form-horizontal" role="form">
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Email</label>
                                          <div class="col-10">
                                             <input type="email" class="form-control email" placeholder="Enter User Email">
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Username</label>
                                          <div class="col-10">
                                             <input type="text" class="form-control uname" placeholder="Create Unique Username">
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Password</label>
                                          <div class="col-10">
                                             <input type="text" disabled class="form-control" placeholder="Default Password" value="123456">
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Key</label>
                                          <div class="col-8">
                                             <input type="text" disabled class="form-control key" placeholder="Insert Key (one time usable)">
                                          </div>
                                          <div class="col-2">
                                             <div class="user-img">
                                                <button type="button" id="getKey" class="form-control btn btn-primary mb-2">Insert</button>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Join Date</label>
                                          <div class="col-10">
                                             <div class="input-group">
                                                <input type="text" class="form-control joindate" placeholder="dd/mm/yyyy" id="datepicker-autoclose">
                                                <div class="input-group-append">
                                                   <span class="input-group-text"><i class="ti-calendar"></i></span>
                                                </div>
                                             </div>
                                             <!-- input-group -->
                                          </div>
                                       </div>
                                       <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" id="addUserButt">Create User</button>
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
               Copyrights © 2018<script>new Date().getFullYear()>2018&&document.write("-"+new Date().getFullYear());</script>, Falcon Trucking VTC. All Rights Reserved.
            </footer>
         </div>
         <!-- ============================================================== -->
         <!-- End Right content here -->
         <!-- ============================================================== -->
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
      <!-- Plugins Js -->
      <script src="assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
      <!-- App js -->
      <script src="assets/js/jquery.core.js"></script>
      <script src="assets/js/jquery.app.js"></script>
      <script>if (window.module) module = window.module;</script>
      <script>
         jQuery(document).ready(function() {

          function removeDiv() {
            setTimeout(function() {
                console.log("Removed div")
                $("#notifyDev").fadeOut("slow")
                $("#notifyDiv").attr("class"," ")
                $("#notifyDiv").html("");
              },5000)
           }
         
          $("#getKey").click(function() {
            let strkey = randomString();

            let key = chunk(strkey,4).join("-");

            $(".key").val(key);
          })

          $("#addUserButt").click(function() {
            let email = $(".email").val(),
                uname = $(".uname").val(),
                key = $(".key").val(),
                date = $(".joindate").val();

            if(!email) {
               $("#notifyDiv").attr("class","alert alert-danger")
               $("#notifyDiv").html(`<strong>Oh snap!</strong> Email is not added`);
               return removeDiv();
               }
             if(!uname) {
               $("#notifyDiv").attr("class","alert alert-danger")
               $("#notifyDiv").html(`<strong>Oh snap!</strong> Username is not added`);
               return removeDiv();
               }
             if(!key) {
               $("#notifyDiv").attr("class","alert alert-danger")
               $("#notifyDiv").html(`<strong>Oh snap!</strong> Key is not created`);
               return removeDiv();
               }
            if(!date) {
               $("#notifyDiv").attr("class","alert alert-danger")
               $("#notifyDiv").html(`<strong>Oh snap!</strong> Date is not added`);
               return removeDiv();
               }

            let tosend = "email="+email+"&uname="+uname+"&key="+key+"&date="+date;
            console.log(tosend);

            $.ajax({
              url: 'addUser.php',
              type: "POST",
              data: tosend,
              async: true,
              success: function(res) {
                if(res == "200") {
                  $("#notifyDiv").attr("class","alert alert-success")
                  $("#notifyDiv").html(`User Settings are saved!`);
                  return removeDiv();
                } else if (res == "400") {
                  $("#notifyDiv").attr("class","alert alert-danger")
                  $("#notifyDiv").html(`<strong>Oh snap!</strong> Something went wrong!`);
                  return removeDiv();
                }
              }
            })
          })

          function chunk(str, n) {
            var ret = [];
            var i;
            var len;
            for(i = 0, len = str.length; i < len; i += n) {
              ret.push(str.substr(i, n))
            }
            return ret
          };

          function randomString() {
              var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
              var string_length = 16;
              var randomstring = '';
              for (var i=0; i<string_length; i++) {
                var rnum = Math.floor(Math.random() * chars.length);
                randomstring += chars.substring(rnum,rnum+1);
              }
              return randomstring;
            }
         // Date Picker
         jQuery('#datepicker-autoclose').datepicker({
             format: "dd/mm/yyyy",
             autoclose: true,
             todayHighlight: true
         });
         
         });
         
      </script>
   </body>
</html>