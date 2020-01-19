
<?php
//include connection file 
include_once("connection.php");

// initilize all variable
$params = $columns = $totalRecords = $data = array();

$params = $_REQUEST;

//define index of column
$columns = array(
    0 => 'JobID',
    1 => 'SourceCity',
    2 => 'DestinationCity',
    3 => 'Cargo Name',
    4 => 'Dated',
    5 => 'GameType'
);

$where = $sqlTot = $sqlRec = "";

// check search value exist
if (!empty($params['search']['value'])) {
    $where .= " WHERE ";
    $where .= " ( JobID LIKE '" . $params['search']['value'] . "%' ";
    $where .= " OR SourceCity LIKE '" . $params['search']['value'] . "%' ";
    $where .= " OR DestinationCity LIKE '" . $params['search']['value'] . "%' ";
}

if ($params['jobfilter'] != "All" && !empty($params['search']['value'])) {
    $where .= " OR GameType LIKE '" . $params['jobfilter'] . "%' )";
} else if ($params['jobfilter'] != "All") {
    $where .= " WHERE ( GameType LIKE '" . $params['jobfilter'] . "%' )";
} else if (!empty($params['search']['value'])) {
    $where .= " )";
}

//echo $where;

$uid = $params['userid'];

// getting total number records without any search
$sql = "SELECT user_jobs.JobID,user_jobs.SourceCity,user_jobs.DestinationCity,user_jobs.CargoName,user_jobs.Dated,user_jobs.GameType
  FROM `user_jobs` INNER JOIN `user_profile` ON user_jobs.UserID = user_profile.UserID";
$sqlTot .= $sql;
$sqlRec .= $sql;
//concatenate search sql if value exist
if (isset($where) && $where != '') {
    $where .= " AND user_jobs.UserID='$uid'";
    $sqlTot .= $where;
    $sqlRec .= $where;
} else {
    $where .= " AND user_jobs.UserID='$uid'";
    $sqlTot .= $where;
    $sqlRec .= $where;
}

function moveElement(&$array, $a, $b)
{
    $out = array_splice($array, $a, 1);
    array_splice($array, $b, 0, $out);
}

$sqlRec .=  " ORDER BY " . $columns[$params['order'][0]['column']] . "   " . $params['order'][0]['dir'] . "  LIMIT " . $params['start'] . " ," . $params['length'] . " ";

$queryTot = mysqli_query($conn, $sqlTot) or die("database error:" . mysqli_error($conn));


$totalRecords = mysqli_num_rows($queryTot);

$queryRecords = mysqli_query($conn, $sqlRec) or die("error to fetch employees data");

//iterate on results row and create new index array of data
while ($row = mysqli_fetch_row($queryRecords)) {
    if ($params["timezone"] == "GMT") {
        $g = gmdate('r', $row[4]);
        $from = "UTC";
        $to = "GMT";
        $row[4] = date_create($g, new DateTimeZone($from))
            ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
    } else if ($params["timezone"] == "CST") {
        $g = gmdate('r', $row[4]);
        $from = "UTC";
        $to = "CDT";
        $row[4] = date_create($g, new DateTimeZone($from))
            ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
    }
    $data[] = $row;
}

$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
?>
	