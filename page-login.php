<?php
  include("./api/v1/database.php");
  session_start();
  $url = array_key_exists("redirect",$_GET) ? $_GET["redirect"] : "index";
  if(isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == true) {
    header("Location: ".$url);
  }
  $invalid_err = "";
  
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $sql = "SELECT Password,UserID FROM `user_profile` WHERE Username='$username'";
    $newquery = mysqli_query($conn,$sql);
    $r = mysqli_fetch_array($newquery);
    if(!$r) {
      $invalid_err = '<div id="errorDiv" class="alert alert-danger">
      <strong>Oh snap!</strong> Invalid credentials!
    </div>';
  } else {
      if ($_POST['password'] == $r["Password"]) {
        $_SESSION['logged_in'] = true;
        $_SESSION['userid'] = $r["UserID"];
        header("Location: ".$url);
      } else {
        $invalid_err = '<div id="errorDiv" class="alert alert-danger">
        <strong>Oh snap!</strong> Invalid credentials!
        </div>';
    }
  }
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

        <title>Falcon Trucking - Login to Dashboard</title>

        <!-- App css -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/style.css" rel="stylesheet" type="text/css" />

        <script src="assets/js/modernizr.min.js"></script>

    </head>

    <body>

        <div class="account-pages"></div>
        <div class="clearfix"></div>
        <div class="wrapper-page">
            <div class="text-center">
                <a class="logo"><span><img  alt="Falcon Logo"  src="assets/images/logo.png" title="Goto Main Dashboard"></span></a>
                <h5 class="text-muted m-t-0 font-600">Login to Falcon Trucking Dashboard</h5>
            </div>
        	<div class="m-t-40 card-box">
                <div class="text-center">
                    <h4 class="text-uppercase font-bold m-b-0">Sign In</h4>
                </div>
                <div class="p-20">
                    <form class="form-horizontal m-t-20" action="page-login.php?redirect=<?php echo $url;?>" method="post">

                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" type="text" required="" placeholder="Username" name="username">
                            </div>
                        </div>
                        <?php echo $invalid_err?>

                        <div class="form-group">
                            <div class="col-xs-12">
                                <input class="form-control" type="password" required="" placeholder="Password" name="password">
                            </div>
                        </div>

                        <div class="form-group ">
                            <div class="col-xs-12">
                                <div class="checkbox checkbox-custom">
                                    <input id="checkbox-signup" type="checkbox">
                                    <label for="checkbox-signup">
                                        Remember me
                                    </label>
                                </div>

                            </div>
                        </div>

                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-success btn-bordred btn-block waves-effect waves-light" type="submit" name="loginButton">Log In</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <!-- end card-box-->

            
        </div>
        <!-- end wrapper page -->



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

        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

        <script>if (window.module) module = window.module;</script>

        <script>
          $(document).ready(function() {
            if($("#errorDiv").length) {
              setTimeout(function() {
                $("#errorDiv").fadeOut('slow');
              },5000)
            }
          })
        </script>
	
	</body>
</html>