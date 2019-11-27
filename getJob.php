<?php
include("./api/v1/database.php");
$jobid = $_GET["jobid"];
$full = $_GET["full"];
$timezone = $_GET["timezone"];
$currency = $_GET["currency"];
$distance = $_GET["distance"];
$fuel = $_GET["fuel"];
$mass = $_GET["mass"];

if ($full == 0) {
    $s = "SELECT user_jobs.JobID,user_jobs.SourceCity,user_jobs.DestinationCity,user_jobs.Dated,user_jobs.GameType,user_profile.Username,user_profile.UserID FROM `user_jobs` INNER JOIN `user_profile` ON user_jobs.UserID = user_profile.UserID WHERE user_jobs.JobID='$jobid'";
    $q = mysqli_query($conn, $s);
    $rows = mysqli_num_rows($q);
    $job = mysqli_fetch_array($q);
    if ($rows > 1) {
        echo 1;
        return;
    }
    if ($rows < 1) {
        echo 2;
        return;
    }
    $uid = $job["UserID"];
    $source = $job["SourceCity"];
    $dest = $job["DestinationCity"];
    $username = $job["Username"];
    $time = $job["Dated"];

    if ($timezone == "GMT") {
        $d = gmdate('r', $time);
        $from = "UTC";
        $to = "GMT";
        $date = date_create($d, new DateTimeZone($from))
            ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
    } else if ($timezone == "CST") {
        $d = gmdate('r', $time);
        $from = "UTC";
        $to = "CDT";
        $date = date_create($d, new DateTimeZone($from))
            ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
    }

    echo "<div class='col-12'>
    <div class='card-box table-responsive'>
    <h4 class='m-t-0 header-title'>Searched Job</h4>
    <p class='text-muted font-14 m-b-30'>
        Make sure to verify before deleting the job
    </p>

    <table id='key-table' class='table table-bordered'>
        <thead>
            <tr>
                <th>Job ID</th>
                <th>Username</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Date & Time (GMT)</th>
                <th>Job Details</th>
                <th>Delete Job</th>
            </tr>
        </thead>

        <tbody>
            <tr data-jobid='$jobid' data-userid='$uid'>
                <td>$jobid</td>
                <td>$username</td>
                <td>$source</td>
                <td>$dest</td>
                <td>$date</td>
                <td><a class='btn btn-info waves-effect moredetails'>Details</a></td>
                <td><a class='btn btn-danger waves-effect deletejob'>Delete</a></td>
            </tr>

        </tbody>
    </table>
    </div>
    </div>";
} else {
    $p = "SELECT * FROM `user_jobs` WHERE JobID='$jobid'";
    $g = mysqli_query($conn, $p);
    $jobdetails = mysqli_fetch_array($g);
    $uid = $jobdetails["UserID"];
    $s = "SELECT Username FROM `user_profile` WHERE UserID='$uid'";
    $q = mysqli_query($conn, $s);
    $userdetails = mysqli_fetch_array($q);

    $startTime = $jobdetails["realTimeStarted"];
    $endTime = $jobdetails["realTimeEnded"];
    if ($jobdetails["LateFine"] == true) {
        $late = "Late";
    } else {
        $late = "On Time";
    }

    if ($fuel == "Gallons") {
        $petrol = round(round($jobdetails["Fuel"]) / 3.785);
        $funits = " gal";
    } else {
        $petrol = round($jobdetails["Fuel"]);
        $funits = " Litres";
    }

    if ($distance == "M") {
        $length = round($jobdetails["Odometer"] / 1.609, 2);
        $dunits = " Miles";
        $speed = round($jobdetails["TopSpeed"] / 1.609, 2);
        $sunits = " mph";
    } else {
        $length = round($jobdetails["Odometer"], 2);
        $dunits = " KM";
        $speed = round($jobdetails["TopSpeed"], 2);
        $sunits = " km/h";
    }

    if ($mass == "lbs") {
        $weight = round($jobdetails["CargoMass"] * 2.205, 2);
        $munits = " lbs";
    } else {
        $weight = round($jobdetails["CargoMass"], 2);
        $munits = " kg";
    }

    if ($timezone == "GMT") {
        $d = gmdate('r', $startTime);
        $f = gmdate('r', $endTime);
        $from = "UTC";
        $to = "GMT";
        $start = date_create($d, new DateTimeZone($from))
            ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
        $end = date_create($f, new DateTimeZone($from))
            ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
    } else if ($timezone == "CST") {
        $d = gmdate('r', $startTime);
        $f = gmdate('r', $endTime);
        $from = "UTC";
        $to = "CDT";
        $start = date_create($d, new DateTimeZone($from))
            ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
        $end = date_create($f, new DateTimeZone($from))
            ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
    }
    $mp = $jobdetails["isMultiplayer"] == true ? "Multiplayer" : "Singleplayer"; 
    $username = $userdetails["Username"];
    $type = strtoupper($jobdetails["GameType"]);
    $sourcecity = $jobdetails["SourceCity"];
    $sourcecom = $jobdetails["SourceCompany"];
    $destcity = $jobdetails["DestinationCity"];
    $destcom = $jobdetails["DestinationCompany"];
    $cargo = $jobdetails["CargoName"];
    $damage = round($jobdetails["OverallDamage"]*100,2)."%";
    $truck = $jobdetails["TruckBrand"] . " " . $jobdetails["TruckModel"];
    $starttime = $start . " " . $timezone;
    $endtime = $end . " " . $timezone;
    $speeding = $jobdetails["SpeedingCount"]." Time".($jobdetails["SpeedingCount"] == 1 ? "" : "s");
    $collision = $jobdetails["CollisionCount"]." Time".($jobdetails["CollisionCount"] == 1 ? "" : "s");
    
    echo "<div class='col-12'>

    <div class='card-box table-responsive'>
        <div class='clearfix'>
            <h4 class='m-t-0 col-md-3 header-title float-left'>Full Job details</h4>

        </div>
        <table id='datatable-jobs' class='table table-striped table-bordered dataTable no-footer' cellspacing='0' width='100%' role='grid' style='width: 100%;'>
            <thead>
                <tr role='row'>
                    <th class='sorting_disabled' rowspan='1' colspan='1'>Icon</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1'>Feature</th>
                    <th class='sorting_disabled' rowspan='1' colspan='1'>Details</th>
                </tr>
            </thead>
            <tbody>

                <tr role='row' class='odd'>
                    <td><i class='fa fa-truck fa-2x '></i></td>
                    <td>Game Mode</td>
                    <td>$mp</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fa fa-id-card fa-2x '></i></td>
                    <td>Job ID</td>
                    <td>$jobid</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fa fa-user fa-2x'></i></td>
                    <td>User Name</td>
                    <td>$username</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fa fa-gamepad fa-2x '></i></td>
                    <td>Game</td>
                    <td>$type</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fa fa-fort-awesome fa-2x '></i></td>
                    <td>Source City</td>
                    <td>$sourcecity</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fa fa-toggle-on fa-2x '></i></td>
                    <td>Source Company</td>
                    <td>$sourcecom</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fa  fa-building fa-2x '></i></td>
                    <td>Destination City</td>
                    <td>$destcity</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fa  fa-toggle-off fa-2x '></i></td>
                    <td>Destination Company</td>
                    <td>$destcom</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fas fa-truck-loading fa-2x'></i></td>
                    <td>Cargo Name</td>
                    <td>$cargo</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fas fa-weight-hanging fa-2x'></i></td>
                    <td>Cargo Weight</td>
                    <td>$weight.$munits</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fa fa-exclamation-triangle fa-2x'></i></td>
                    <td>Cargo Damage</td>
                    <td>$damage</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fa fa-truck fa-2x'></i></td>
                    <td>Truck Used</td>
                    <td>$truck</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fa fa-road fa-2x'></i></td>
                    <td>Distance Driven</td>
                    <td>$length.$dunits</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fa fa-fire fa-2x'></i></td>
                    <td>Fuel Consumed</td>
                    <td>$petrol.$funits</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fa fa-clock-o fa-2x'></i></td>
                    <td>Time Started</td>
                    <td>$starttime</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fa fa-history fa-2x'></i></td>
                    <td>Time Ended</td>
                    <td>$endtime</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fa  fa-angle-double-right fa-2x'></i></td>
                    <td>Top Speed</td>
                    <td>$speed.$sunits</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fas fa-hourglass-half fa-2x'></i></td>
                    <td>On Time/Late</td>
                    <td>$late</td>
                </tr>
                <tr role='row' class='odd'>
                    <td><i class='fas fa-tachometer-alt fa-2x'></i></td>
                    <td>Speeding Count</td>
                    <td>$speeding</td>
                </tr>
                <tr role='row' class='even'>
                    <td><i class='fas fa-car-crash fa-2x'></i></td>
                    <td>Collision Count</td>
                    <td>$collision</td>
                </tr>
            </tbody>
        </table>

        <div class='btn-group m-b-10 col-md-2 float-right' data-jobid='$jobid' data-userid='$uid'>
            <button type='button' class='btn btn-danger waves-effect deletejob'>Delete Job</button>
        </div>

    </div>";
}
