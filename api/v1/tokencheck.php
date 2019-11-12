<?php
  header('Content-Type: application/json');
  if (!isset($_GET["key"])) {
    $data = array(
      "status"=>"400",
      "error"=>"key not found"
    );
    echo json_encode($data);
    exit();
  }
  if ($_GET["key"] != "9xsyr1pr1miyp45") {
    $data = array(
      "status"=>"403",
      "error"=>"unauthorized key"
    );
    echo json_encode($data);
    exit();
  }
?>