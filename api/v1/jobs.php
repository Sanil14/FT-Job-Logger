<?php
header('Content-type: application/json');
include("database.php");
include("./DiscordWebhooks/Client.php");
include("./DiscordWebhooks/Embed.php");

use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;

$truckler_payload = $_POST["data"];

// Send response plugin
ignore_user_abort(true);
set_time_limit(0);
ob_start();
echo 200;
header('Connection: close');
header('Content-Length: '.ob_get_length());
ob_end_flush();
ob_flush();
flush();
// Continue as normal with script execution

file_put_contents("encoded.txt", $truckler_payload);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://trucklerprocessor.jammerxd.dev/api/v1/process_delivery/88/3/");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "data=" . $truckler_payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode === 200) {
  $json = json_decode($server_output, true);
  file_put_contents("decoded.txt", $server_output);
  if (json_last_error() === JSON_ERROR_NONE) {
    if (!empty($json["steamID"])) {
      sendData($json);
    }
  }
}

function sendData($data)
{
  global $conn;
  $mp = $data["gameIsMultiplayer"];
  $UID = UIDisValid($conn, $data["steamID"]) ? $data["steamID"] : outputError();
  $gameid = ($data["gameId"] == "ets2" || $data["gameId"] == "ats") ? $data["gameId"] : `UNKNOWN`;
  $sourcecity = gettype($data["jobSourceCity"]) == "string" ? $data["jobSourceCity"] : outputError();
  $sourcecom = gettype($data["jobSourceCompany"]) == "string" ? $data["jobSourceCompany"] : outputError();
  $destcity = gettype($data["jobDestinationCity"]) == "string" ? $data["jobDestinationCity"] : outputError();
  $destcom = gettype($data["jobDestinationCompany"]) == "string" ? $data["jobDestinationCompany"] : outputError();
  $odometer = is_numeric($data["jobDistanceDriven"]) ? $data["jobDistanceDriven"] : outputError();
  $fueld =  is_numeric($data["fuelBurned"]) ? $data["fuelBurned"] : outputError();
  $money =  is_numeric($data["jobIncome"]) ? $data["jobIncome"] : outputError();
  $cargoname = gettype($data["jobCargo"]) == "string" ? $data["jobCargo"] : outputError();
  $cargomass =  is_numeric($data["jobCargoMass"]) ? $data["jobCargoMass"] : outputError();
  $fee = $data["jobIsLate"];
  $timestarted = is_numeric($data["jobStartedEpoch"]) ? ($data["jobStartedEpoch"] / 1000) : outputError();
  $timeended = is_numeric($data["jobEndedEpoch"]) ? ($data["jobEndedEpoch"] / 1000) : outputError();
  $topspeedms = is_numeric(0) ? 0 : outputError();
  $speedingcount = is_numeric(0) ? 0 : outputError(); // Find out speeding count by looping through events
  $collisioncount = is_numeric(0) ? 0 : outputError(); // Find out collision count by looping through events
  $damage = is_numeric($data["engineDamage"] + $data["transmissionDamage"] + $data["cabinDamage"] + $data["chassisDamage"] + $data["wheelDamage"]) ? $data["engineDamage"] + $data["transmissionDamage"] + $data["cabinDamage"] + $data["chassisDamage"] + $data["wheelDamage"] : outputError();
  $truckmake = gettype($data["truckMake"]) == "string" ? $data["truckMake"] : outputError();
  $truckmodel = gettype($data["truckModel"]) == "string" ? $data["truckModel"] : outputError();
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
        $stats = "UPDATE `user_stats` SET TotalKM=TotalKM+$distance, TotalJobs=TotalJobs+1, TotalIncome=TotalIncome+$income, TotalFuel=TotalFuel+$fuel, AtsKM=AtsKM+$distance, AtsJobs=AtsJobs+1, AtsIncome=AtsIncome+$income, AtsFuel=AtsFuel+$fuel, LastJobDate=$date WHERE SteamID='$UID'";
      } else {
        $stats = "UPDATE `user_stats` SET TotalKM=TotalKM+$distance, TotalJobs=TotalJobs+1, TotalIncome=TotalIncome+$income, TotalFuel=TotalFuel+$fuel, Ets2KM=Ets2KM+$distance, Ets2Jobs=Ets2Jobs+1, Ets2Income=Ets2Income+$income, Ets2Fuel=Ets2Fuel+$fuel, LastJobDate=$date WHERE SteamID='$UID'";
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
        exit();
      } else {
        exit();
      }
    } else {
      exit();
    }
  } else {
    exit();
  }
}

function outputError()
{
  exit();
}

function UIDisValid($conn, $sid)
{
  $s = "SELECT Username FROM `user_profile` WHERE SteamID='$sid'";
  $q = mysqli_query($conn, $s);
  $rows = mysqli_num_rows($q);
  if ($rows == 0) {
    return false;
    exit();
  } else {
    return true;
  }
}

function checkStatsRow($conn, $uid)
{
  $s = "SELECT * FROM `user_stats` WHERE SteamID='$uid'";
  $q = mysqli_query($conn, $s);
  $rows = mysqli_num_rows($q);
  return ($rows != 0);
}

function discordWebhook($conn, $UID, $gameid, $fromcity, $tocity, $fromcom, $tocom, $distance, $cargo, $fuel)
{

  //$s = "SELECT user_profile.Username,user_jobs.JobID FROM `user_profile` INNER JOIN `user_jobs` ON user_profile.SteamID = user_jobs.SteamID WHERE user_profile.SteamID='$UID' ORDER BY user_jobs.JobID DESC LIMIT 1";
  $s = "SELECT user_profile.Username,MAX(user_jobs.JobID) AS JobID FROM user_jobs LEFT JOIN user_profile ON user_profile.SteamID = user_jobs.SteamID WHERE user_profile.SteamID = '$UID'";
  $q = mysqli_query($conn, $s);
  $info = mysqli_fetch_array($q);
  //echo implode(", ", $info);
  $jobid = $info["JobID"];

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

  #SteamID, Game Name, SourceCity, Source Company, DestinationCity, Destination Company, distanceDriven, fuelBurned, Income, Cargo Name, Cargo Mass, fee, realTimeStarted, realTimeEnded, topSpeed, speedingCount, CollisionCount, Damage, truckMake, truckModel, Date
