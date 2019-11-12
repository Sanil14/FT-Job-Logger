<?php
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'JobID', 'dt' => 0 ),
    array( 'db' => 'Username',  'dt' => 1 ),
    array( 'db' => 'SourceCity',   'dt' => 2 ),
    array( 'db' => 'DestinationCity',     'dt' => 3 ),
    array(
        'db'        => 'Dated',
        'dt'        => 4,
        'formatter' => function( $d, $row ) {
          if ($_GET["timezone"] == "GMT") {
            $g = gmdate('r',$d);
            $from = "UTC";
            $to = "GMT";
            return date_create($g, new DateTimeZone($from))
                      ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
          } else if ($_GET["timezone"] == "CST") {
            $g = gmdate('r',$d);
            $from = "UTC";
            $to = "CDT";
            return date_create($g, new DateTimeZone($from))
                      ->setTimezone(new DateTimeZone($to))->format("d/m/Y H:i:s");
          }
        }
      ),
      array(
        'db'        => 'GameType',
        'dt'        => 5 
      )
);
 
// SQL server connection information
/*$sql_details = array(
    'user' => 'falconit_tracker',
    'pass' => ';BODx}C5-2C7',
    'db'   => 'falconit_dashboard',
    'host' => 'localhost'
);*/
$sql_details = array(
  'user' => 'root',
  'pass' => '',
  'db'   => 'falconit_dashboard',
  'host' => 'localhost'
);

require('ssp.class.php');

$datatable = SSP::simple($_GET,$sql_details,$columns);

echo json_encode($datatable);
 
//echo (SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns ));