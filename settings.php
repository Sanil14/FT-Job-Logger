<?php
   include("./api/v1/database.php");
   session_start();
   if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
     header("Location: page-login?redirect=settings");
   }
   $id = $_SESSION['userid'];
   $invalid_err = "";
   $s = "SELECT Username,Preferences,Permission FROM `user_profile` WHERE UserID='$id'";
   $q = mysqli_query($conn,$s);
   $userstats = mysqli_fetch_array($q);
   
   $profilepic = "avatars/".$id.".png";
   if(!file_exists($profilepic)) {
     $profilepic = "avatars/default.png";
   }
   $username = $userstats["Username"];
   $json = json_decode($userstats["Preferences"]);
   $currency = $json->Currency;
   $distance = $json->Distance;
   $fuel = $json->Fuel;
   $timezone = $json->Timezone;
   $mass = $json->Mass;
   
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
      <meta name="author" content="Coderthemes">
      <link rel="shortcut icon" href="assets/images/favicon.ico">
      <title>Falcon Trucking Dashboard - Settings</title>
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
                        <h4 class="page-title">User Settings</h4>
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
                           <h4 class="m-t-0 header-title">Regional Settings</h4>
                           <div class="row">
                              <div class="col-12">
                                 <div class="p-20">
                                    <form class="form-horizontal" role="form">
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Currency</label>
                                          <div class="col-10">
                                             <select class="form-control currency">
                                               <?php
                                                if($currency == "Euros") {
                                                  ?>
                                                <option value="Euros">Euros (€)</option>
                                                <option value="Dollars">Dollars ($)</option>
                                                <?php
                                                } else if($currency == "Dollars") {
                                                  ?>
                                                <option value="Dollars">Dollars ($)</option>
                                                <option value="Euros">Euros (€)</option>
                                                <?php
                                                }
                                               ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Distance Units</label>
                                          <div class="col-10">
                                             <select class="form-control distance">
                                             <?php
                                                if($distance == "KM") {
                                                  ?>
                                                <option value="KM">Kilometers (KM)</option>
                                                <option value="M">Miles (M)</option>
                                                <?php
                                                } else if($distance == "M") {
                                                  ?>
                                                <option value="M">Miles (M)</option>
                                                <option value="KM">Kilometers (KM)</option>
                                                <?php
                                                }
                                               ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Fuel Units</label>
                                          <div class="col-10">
                                             <select class="form-control fuel">
                                             <?php
                                                if($fuel == "Litres") {
                                                  ?>
                                                <option value="Litres">Litres (L)</option>
                                                <option value="Gallons">Gallons (gal)</option>
                                                <?php
                                                } else if($fuel == "Gallons") {
                                                  ?>
                                                <option value="Gallons">Gallons (gal)</option>
                                                <option value="Litres">Litres (L)</option>
                                                <?php
                                                }
                                               ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Mass Units</label>
                                          <div class="col-10">
                                             <select class="form-control mass">
                                             <?php
                                                if($mass == "kg") {
                                                  ?>
                                                <option value="kg">Kilograms (kg)</option>
                                                <option value="lbs">Pounds (lbs)</option>
                                                <?php
                                                } else if($mass == "lbs") {
                                                  ?>
                                                <option value="lbs">Pounds (lbs)</option>
                                                <option value="kg">Kilograms (kg)</option>
                                                <?php
                                                }
                                               ?>
                                             </select>
                                          </div>
                                       </div>
                                       <div class="form-group row">
                                          <label class="col-2 col-form-label">Time Zone</label>
                                          <div class="col-10">
                                             <select class="form-control timezone">
                                             <?php
                                                if($timezone == "GMT") {
                                                  ?>
                                                <option value="GMT">GMT</option>
                                                <option value="CST">CST</option>
                                                <?php
                                                } else if($timezone == "CST") {
                                                  ?>
                                                <option value="CST">CST</option>
                                                <option value="GMT">GMT</option>
                                                <?php
                                                }
                                               ?>
                                             </select>
                                          </div>
                                       </div>
                                       <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5 updateButt">Update Settings</button>
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
               Copyrights <i class="fa fa-copyright"></i> 2018<script>new Date().getFullYear()>2018&&document.write("-"+new Date().getFullYear());</script>, Falcon Trucking VTC. All Rights Reserved.
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
      <script>if (window.module) module = window.module;</script>
      <script>
         $(document).ready(function() {

          function removeDiv() {
            setTimeout(function() {
                console.log("Removed div")
                $("#notifyDev").fadeOut("slow")
                $("#notifyDiv").attr("class"," ")
                $("#notifyDiv").html("");
              },5000)
           }

           $(".updateButt").click(function() {
             let currency = $("select.currency").children("option:selected").val();
             let distance = $("select.distance").children("option:selected").val();
             let fuel = $("select.fuel").children("option:selected").val();
             let mass = $("select.mass").children("option:selected").val();
             let timezone = $("select.timezone").children("option:selected").val();
             let json = {
               "Currency": currency,
               "Distance": distance,
               "Fuel": fuel,
               "Mass": mass,
               "Timezone": timezone
             }
             let datatosend = "settings=" + JSON.stringify(json);
             $.ajax({
               url: 'updateSettings.php',
               type: 'POST',
               data: datatosend,
               async: true,
               success: function(response) {
                 console.log(response);
                 if(response == 1) {
                  $("#notifyDiv").attr("class","alert alert-success")
                  $("#notifyDiv").html(`Regional Settings are saved!`);
                  return removeDiv();
                 } else if (response == 0) {
                  $("#notifyDiv").attr("class","alert alert-danger")
                  $("#notifyDiv").html(`<strong>Oh snap!</strong> Something went wrong!`);
                  return removeDiv();
                 } else {
                  $("#notifyDiv").attr("class","alert alert-danger")
                  $("#notifyDiv").html(`<strong>Oh snap!</strong> Something went wrong!`);
                  return removeDiv();
                 }
               }
             })
           })

           })
         
      </script>
   </body>
</html>