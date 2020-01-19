<?php
/* Database connection start */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "falconit_dashboard";

$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
mysqli_set_charset($conn, "utf8");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

?>