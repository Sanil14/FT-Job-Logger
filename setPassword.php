<?php
session_start();
include("./api/v1/database.php");
$password = $_GET['password'];
$id = $_GET['userid'];

$encrypted = openssl_encrypt($password,$cipher,$key,0,$iv);

$sql = "UPDATE `user_profile` SET Password='$encrypted' WHERE UserID='$id'";
$newquery = mysqli_query($conn, $sql);
if (mysqli_query($conn,$sql)) {
    echo 1;
} else {
    echo 0;
}
