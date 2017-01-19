<?php
include("db_config.php");

$conn = pg_pconnect("host=" . $host . " dbname=" . $database . " user=" . $username . " password=" . $password);
if (!$conn) {
  echo "Could not connect to database!\n";
  exit;
}

$line_loid = $_GET["line_loid"]; //2003

$result = pg_query($conn, "SELECT v.loid, v.logicalkey, variantstats.day_stops FROM csipvariant v INNER JOIN ( SELECT v.loid, COUNT(ds.loid) AS day_stops FROM csipvariant v INNER JOIN csipvariantstopping vs ON vs.variant_loid = v.loid INNER JOIN csipcoursestopping cs ON cs.variantstopping_loid = vs.loid INNER JOIN csipdaystopping ds ON ds.stopping_loid = cs.loid WHERE v.line_loid = " . $line_loid . " GROUP BY v.loid ORDER BY v.loid ) AS variantstats ON variantstats.loid = v.loid WHERE v.line_loid = " . $line_loid);

/*$result = pg_query($conn, "SELECT v.loid, v.logicalkey FROM csipvariant v WHERE v.line_loid = " . $line_loid . " ORDER BY v.logicalkey");*/

if (!$result) {
  echo "Could not perform query!\n";
  exit;
}

$resultArray = pg_fetch_all($result);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resultArray);
?>