<?php
session_start();
include("./api/v1/database.php");
$username = $_GET['username'];
$sql = "SELECT Password,UserID FROM `user_profile` WHERE Username='$username'";
$newquery = mysqli_query($conn, $sql);
$r = mysqli_fetch_array($newquery);
if (!$r) {
    echo 0;
} else {
    if ($_GET['password'] == "123456" && $r["Password"] == "123456") {
        echo $r["UserID"];
    } else {
        $decrypted = openssl_decrypt($r["Password"], $cipher, $key, 0, $iv);
        if ($_GET['password'] == $decrypted) {
            $_SESSION['logged_in'] = true;
            $_SESSION['userid'] = $r["UserID"];
            echo 1;
        } else {
            echo 0;
        }
    }
}
