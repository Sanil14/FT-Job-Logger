<?php
header('Content-type: application/json');
include("tokencheck.php");
include("database.php");
include("./DiscordWebhooks/Client.php");
include("./DiscordWebhooks/Embed.php");

use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;

if (!isset($_GET["data"])) {
  $error = array(
    "status" => "400",
    "error" => "data not found"
  );
  echo json_encode($error);
  exit();
}

$dataset = urldecode($_GET["data"]);
$arrayed = explode(",", $dataset);
if (!$arrayed[1] || !is_numeric($arrayed[1])) {
  $error = array(
    "status" => "400",
    "error" => "User is invalid"
  );
  echo json_encode($error);
  exit();
}
//echo json_encode($arrayed);
if (sizeof($arrayed) != 21) {
  $error = array(
    "status" => "400",
    "error" => "Values missing"
  );
  echo json_encode($error);
  exit();
}
$mp = $arrayed[0];
$UID = UIDisValid($conn, $arrayed[1]) ? $arrayed[1] : outputError();
$gameid = ($arrayed[2] == "ets2" || $arrayed[2] == "ats") ? $arrayed[2] : $arrayed[2];
$sourcecity = gettype($arrayed[3]) == "string" ? $arrayed[3] : outputError();
$sourcecom = gettype($arrayed[4]) == "string" ? $arrayed[4] : outputError();
$destcity = gettype($arrayed[5]) == "string" ? $arrayed[5] : outputError();
$destcom = gettype($arrayed[6]) == "string" ? $arrayed[6] : outputError();
$odometer = is_numeric($arrayed[7]) ? $arrayed[7] : outputError();
$fueld =  is_numeric($arrayed[8]) ? $arrayed[8] : outputError();
$money =  is_numeric($arrayed[9]) ? $arrayed[9] : outputError();
$cargoname = gettype($arrayed[10]) == "string" ? $arrayed[10] : outputError();
$cargomass =  is_numeric($arrayed[11]) ? $arrayed[11] : outputError();
$fee = $arrayed[12];
$timestarted = is_numeric($arrayed[13]) ? ($arrayed[13] / 1000) : outputError();
$timeended = is_numeric($arrayed[14]) ? ($arrayed[14] / 1000) : outputError();
$topspeedms = is_numeric($arrayed[15]) ? $arrayed[15] : outputError();
$speedingcount = is_numeric($arrayed[16]) ? $arrayed[16] : outputError();
$collisioncount = is_numeric($arrayed[17]) ? $arrayed[17] : outputError();
$damage = is_numeric($arrayed[18]) ? $arrayed[18] : outputError();
$truckmake = gettype($arrayed[19]) == "string" ? $arrayed[19] : outputError();
$truckmodel = gettype($arrayed[20]) == "string" ? $arrayed[20] : outputError();
$date = time();

$topspeedkmh = round($topspeedms * 3.6);
$fuel = ceil($fueld);

if ($gameid == "ats") {
  $income = $money * 0.89;
  $distance = $odometer * 1.609;
} else {
  $income = $money;
  $distance = $odometer;
}

$q = "INSERT INTO `user_jobs` VALUES ('0', '$UID', '$sourcecity','$sourcecom','$destcity','$destcom','$distance','$fuel','$income','$cargomass','$cargoname','$fee','$mp','$timestarted','$timeended','$topspeedkmh','$speedingcount','$collisioncount','$damage','$gameid','$truckmake','$truckmodel','$date')";
//echo "$jobID, $UID, $sourcecity,$sourcecom,$destcity,$destcom,$distance,$fuel,$income,$cargomass,$cargoname,$fee,$timestarted,$timeended,$topspeedkmh,$speedingcount,$collisioncount,$damage,$gameid,$truckmake,$truckmodel,$date";
if (mysqli_query($conn, $q)) {
  $statsrow = checkStatsRow($conn, $UID);
  $stats = "";
  if ($statsrow) {
    if ($gameid == "ats") {
      $stats = "UPDATE `user_stats` SET TotalKM=TotalKM+$distance, TotalJobs=TotalJobs+1, TotalIncome=TotalIncome+$income, TotalFuel=TotalFuel+$fuel, AtsKM=AtsKM+$distance, AtsJobs=AtsJobs+1, AtsIncome=AtsIncome+$income, AtsFuel=AtsFuel+$fuel, LastJobDate=$date WHERE UserID='$UID'";
    } else {
      $stats = "UPDATE `user_stats` SET TotalKM=TotalKM+$distance, TotalJobs=TotalJobs+1, TotalIncome=TotalIncome+$income, TotalFuel=TotalFuel+$fuel, Ets2KM=Ets2KM+$distance, Ets2Jobs=Ets2Jobs+1, Ets2Income=Ets2Income+$income, Ets2Fuel=Ets2Fuel+$fuel, LastJobDate=$date WHERE UserID='$UID'";
    }
  }
  if (!$statsrow) {
    if ($gameid == "ats") {
      $stats = "INSERT INTO `user_stats` VALUES ($UID, $distance, 1, $income, $fuel, $distance, $income, 1, $fuel, 0, 0, 0, 0, $date)";
    } else {
      $stats = "INSERT INTO `user_stats` VALUES ($UID, $distance, 1, $income, $fuel,0,0,0,0, $distance, $income, 1, $fuel,$date)";
    }
  }
  if (mysqli_query($conn, $stats)) {
    $vtc = "UPDATE `vtc_stats` SET TotalKM=TotalKM+$distance, TotalJobs=TotalJobs+1,TotalIncome=TotalIncome+$income, TotalFuel=TotalFuel+$fuel";

    if (mysqli_query($conn, $vtc)) {
      discordWebhook($conn, $UID, $gameid, $sourcecity, $destcity, $sourcecom, $destcom, $odometer, $cargoname, $fuel);
      $output = array(
        "status" => "202",
        "error" => "job is logged"
      );
      echo json_encode($output);
      exit();
    } else {
      $error = array(
        "status" => "400",
        "error" => mysqli_error($conn)
      );
      echo json_encode($error);
      exit();
    }
  } else {
    $error = array(
      "status" => "400",
      "error" => mysqli_error($conn)
    );
    echo json_encode($error);
    exit();
  }
} else {
  $error = array(
    "status" => "400",
    "error" => mysqli_error($conn)
  );
  echo json_encode($error);
  exit();
}

function outputError()
{
  $error = array(
    "status" => "400",
    "error" => "Bad Request"
  );
  echo json_encode($error);
  exit();
}

function UIDisValid($conn, $uid)
{
  $s = "SELECT Username FROM `user_profile` WHERE UserID='$uid'";
  $q = mysqli_query($conn, $s);
  $rows = mysqli_num_rows($q);
  if ($rows == 0) {
    $error = array(
      "status" => "404",
      "error" => "User not found"
    );
    echo json_encode($error);
    return false;
    exit();
  } else {
    return true;
  }
}

function checkStatsRow($conn, $uid)
{
  $s = "SELECT * FROM `user_stats` WHERE UserID='$uid'";
  $q = mysqli_query($conn, $s);
  $rows = mysqli_num_rows($q);
  return ($rows != 0);
}

function discordWebhook($conn, $UID, $gameid, $fromcity, $tocity, $fromcom, $tocom, $distance, $cargo, $fuel)
{

  //$s = "SELECT user_profile.Username,user_jobs.JobID FROM `user_profile` INNER JOIN `user_jobs` ON user_profile.UserID = user_jobs.UserID WHERE user_profile.UserID='$UID' ORDER BY user_jobs.JobID DESC LIMIT 1";
  $s = "SELECT user_profile.Username,MAX(user_jobs.JobID) AS JobID FROM user_jobs LEFT JOIN user_profile ON user_profile.UserID = user_jobs.UserID WHERE user_profile.UserID = '$UID'";
  $q = mysqli_query($conn, $s);
  $info = mysqli_fetch_array($q);
  //echo implode(", ", $info);
  $jobid = $info["JobID"];

  /*$profilepic = "https://www.dashboard.falconites.com/avatars/".$UID.".png";

    if(!does_url_exists($profilepic)) {
      $profilepic = "https://www.dashboard.falconites.com/avatars/default.png";
    }

    echo $profilepic;*/
  $webhook = new Client("https://discordapp.com/api/webhooks/571415166797873234/8WURUoDjp64oJEpDLgW0YBoQCsgJbrJKopOE9W7YKGtPFfGHi1OL69TLkyUnt0SzUi1X");
  $embed = new Embed();

  $embed->color("0x3498db");
  $embed->title($info["Username"] . " completed a job!", "https://www.dashboard.falconites.com/vtc-members");
  $embed->description("Links: [Job Page](https://www.dashboard.falconites.com/job-details?id=" . $jobid . ") | [VTC Jobs](https://www.dashboard.falconites.com/vtc-jobs)");
  $embed->thumbnail("https://cdn.discordapp.com/attachments/571411450636009604/630804996287365130/falcon_logo.jpg");
  $embed->field("Game", $gameid == "ats" ? "American Truck Simulator" : "Euro Truck Simulator 2", true);
  $embed->field("Cargo", $cargo, true);
  $embed->field("From City", $fromcity, true);
  $embed->field("To City", $tocity, true);
  $embed->field("From Company", $fromcom, true);
  $embed->field("To Company", $tocom, true);
  $embed->field("Odometer", $distance . ($gameid == "ats" ? " Miles" : " KM"), true);
  $embed->field("Fuel", $fuel . " Litres", true);

  $webhook->username("FT Job Logger (Alpha)")->message('')->embed($embed)->send();
}

function does_url_exists($url)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  if ($code == 200) {
    $status = true;
  } else {
    $status = false;
  }
  curl_close($ch);
  return $status;
}

  #UserID, Game Name, SourceCity, Source Company, DestinationCity, Destination Company, distanceDriven, fuelBurned, Income, Cargo Name, Cargo Mass, fee, realTimeStarted, realTimeEnded, topSpeed, speedingCount, CollisionCount, Damage, truckMake, truckModel, Date
