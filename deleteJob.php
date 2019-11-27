<?php
include("./api/v1/database.php");
$jobid = $_GET["jobid"];
$UID = $_GET["userid"];

$s = "SELECT Odometer, Fuel, Income, GameType FROM `user_jobs` WHERE JobID='$jobid'";
$q = mysqli_query($conn, $s);
if ($q) {
    $jobdata = mysqli_fetch_array($q);
    $distance = $jobdata["Odometer"];
    $fuel = $jobdata["Income"];
    $income = $jobdata["Fuel"];
    if ($jobdata["GameType"] == "ats") {
        $stats = "UPDATE `user_stats` SET TotalKM=TotalKM-$distance, TotalJobs=TotalJobs-1, TotalIncome=TotalIncome-$income, TotalFuel=TotalFuel-$fuel, AtsKM=AtsKM-$distance, AtsJobs=AtsJobs-1, AtsIncome=AtsIncome+$income, AtsFuel=AtsFuel-$fuel WHERE UserID='$UID'";
    } else {
        $stats = "UPDATE `user_stats` SET TotalKM=TotalKM-$distance, TotalJobs=TotalJobs-1, TotalIncome=TotalIncome-$income, TotalFuel=TotalFuel-$fuel, Ets2KM=Ets2KM-$distance, Ets2Jobs=Ets2Jobs-1, Ets2Income=Ets2Income-$income, Ets2Fuel=Ets2Fuel-$fuel WHERE UserID='$UID'";
    }
    if (mysqli_query($conn, $stats)) {
        $vtc = "UPDATE `vtc_stats` SET TotalKM=TotalKM-$distance, TotalJobs=TotalJobs-1,TotalIncome=TotalIncome-$income, TotalFuel=TotalFuel-$fuel";
        if (mysqli_query($conn, $vtc)) {
            $job = "DELETE FROM `user_jobs` WHERE JobID='$jobid'";
            if (mysqli_query($conn, $job)) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }
}
