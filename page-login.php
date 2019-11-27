<?php
include("./api/v1/database.php");
session_start();
$url = array_key_exists("redirect", $_GET) ? $_GET["redirect"] : "index";
if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == true) {
        header("Location: " . $url);
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
            <a class="logo"><span><img alt="Falcon Logo" src="assets/images/logo.png" title="Goto Main Dashboard"></span></a>
            <h5 class="text-muted m-t-0 font-600">Login to Falcon Trucking Dashboard</h5>
        </div>
        <div class="m-t-40 card-box">
            <div class="p-20">
                <form class="form-horizontal m-t-20 login" method="post">
                    <div class="text-center m-b-30">
                        <h4 class="text-uppercase font-bold m-b-0">Sign In</h4>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control uname" type="text" required="" placeholder="Username">
                        </div>
                    </div>
                    <div id="notifyDiv"></div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control loginpass" type="password" required="" placeholder="Password">
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
                            <button class="btn btn-success btn-bordred btn-block waves-effect waves-light loginButton">Log In</button>
                        </div>
                    </div>

                </form>
                <!-- Hide on Normal and show if needs password reset-->

                <form class="pwd-change form-horizontal m-t-20" action="" method="post">
                    <div class="text-center m-b-30">
                        <h4 class="text-uppercase font-bold m-b-0">Change Password</h4>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control pass1" type="password" required="" placeholder="Change Password">
                        </div>
                    </div>
                    <div id="notifyMessage"></div>

                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control pass2" type="password" required="" placeholder="Confirm Password">
                        </div>
                    </div>


                    <div class="form-group text-center m-t-30">
                        <div class="col-xs-12">
                            <button class="btn btn-success btn-bordred btn-block waves-effect waves-light resetpass" type="submit">Change Password</button>
                        </div>
                    </div>

                </form>
                <!-- Hide on Normal and show if needs password reset-->

            </div>
        </div>
        <!-- end card-box-->


    </div>
    <!-- end wrapper page -->



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

    <!-- App js -->
    <script src="assets/js/jquery.core.js"></script>
    <script src="assets/js/jquery.app.js"></script>

    <script>
        if (window.module) module = window.module;
    </script>

    <script>
        $(document).ready(function() {

            let uid;


            function removeDiv(div) {
                setTimeout(function() {
                    $(`${div}`).fadeOut("slow")
                    $(`${div}`).attr("class", " ")
                    $(`${div}`).attr("style", " ")
                    $(`${div}`).html("");
                }, 5000)
            }

            $(".loginButton").click(function(e) {
                e.preventDefault();
                var username = $(".uname").val();
                var pass = $(".loginpass").val();

                if (!username || !pass) {
                    $("#notifyDiv").attr("class", "alert alert-danger");
                    $("#notifyDiv").html('<strong>Oh snap!</strong> Values Missing!')
                    removeDiv("#notifyDiv");
                    return;
                }

                $.ajax({
                    type: "get",
                    url: "passwordCheck.php",
                    data: {
                        username: username,
                        password: pass
                    },
                    success: function(resp) {
                        if (resp == 0) {
                            $("#notifyDiv").attr("class", "alert alert-danger");
                            $("#notifyDiv").html('<strong>Oh snap!</strong> Invalid Credentials!')
                            removeDiv("#notifyDiv");
                        } else if (resp == 1) {
                            window.location.replace("<?php echo $url ?>");
                        } else if (resp > 0 && resp < 200) {
                            uid = resp;
                            $(".pwd-change").show();
                            $(".login").hide();
                        }
                    }
                })
            })

            $(".resetpass").click(function(e) {
                e.preventDefault();
                var pass = $(".pass1").val();
                var confirm = $(".pass2").val();

                if (pass != confirm) {
                    $("#notifyMessage").show();
                    $("#notifyMessage").attr("class", "alert alert-danger");
                    $("#notifyMessage").html('<strong>Oh snap!</strong> Passwords dont match!')
                    removeDiv("#notifyMessage");
                }
                $.ajax({
                    type: "get",
                    url: "setPassword.php",
                    data: {
                        password: pass,
                        userid: uid
                    },
                    success: function(resp) {
                        if (resp == 0) {
                            $("#notifyMessage").show();
                            $("#notifyMessage").attr("class", "alert alert-danger");
                            $("#notifyMessage").html('<strong>Oh snap!</strong> Unknown Error')
                            removeDiv("#notifyMessage");
                        } else if (resp == 1) {
                            window.location.replace("<?php echo $url ?>");
                        }
                    }
                })

            })
        })
    </script>

</body>

</html>