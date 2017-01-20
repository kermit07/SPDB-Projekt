<?php
include("db_config.php");

$conn = pg_pconnect("host=" . $host . " dbname=" . $database . " user=" . $username . " password=" . $password);
if (!$conn) {
  echo "Could not connect to database!\n";
  exit;
}

$variant_loid = $_GET["variant_loid"]; //41089

$start_hour = $_GET["start_hour"];
$end_hour = $_GET["end_hour"];
$day = $_GET["day"];
$delay = $_GET["delay"];

$query = "SELECT vs.loid, vs.distancefromschedule, vs.orderinvariant, sp.loid AS sp_loid, sp.latitude, sp.longitude, sp.name, delay.total, delay.delayed, delay.not_delayed, delay.avg_delay, delay.delay_rate FROM csipvariantstopping vs LEFT JOIN csipstoppoint sp ON sp.loid = vs.stoppoint_loid LEFT JOIN ( SELECT vs.loid AS vs_loid, COUNT(*) AS total, COUNT(ds.delaysec) AS delayed, COUNT(*) - COUNT(ds.delaysec) AS not_delayed, AVG(ds.delaysec) AS avg_delay, (COUNT(ds.delaysec) :: FLOAT / COUNT(*)) * 100 AS delay_rate FROM csipvariantstopping vs LEFT JOIN csipcoursestopping cs ON cs.variantstopping_loid = vs.loid LEFT JOIN csipdaystopping ds ON ds.stopping_loid = cs.loid WHERE (ds.delaysec IS NULL OR ( ";

if($delay == 0)
	$query .= "ds.delaysec < 0 "; // przyspieszenia
else
	$query .= "ds.delaysec > 0 "; // opoznienia

$query .= "AND ds.delaysec < 1.5 * 60 * 60 AND ds.delaysec > -1.5 * 60 * 60) ) ";

if($start_hour != -1)
	$query .= "AND EXTRACT(HOUR FROM ds.scheduleddeparture) >= " . $start_hour . " AND EXTRACT(HOUR FROM ds.scheduleddeparture) <= " . $end_hour . " ";

if($day != -1)
	$query .= "AND EXTRACT(DOW FROM ds.scheduleddeparture) = " . $day . " ";

$query .= "AND vs.variant_loid = ".$variant_loid." GROUP BY vs.loid ) AS delay ON delay.vs_loid = vs.loid WHERE vs.variant_loid = ".$variant_loid;

$result = pg_query($conn, $query);

if (!$result) {
  echo "Could not perform query!\n";
  exit;
}

$resultArray = pg_fetch_all($result);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resultArray);
?>