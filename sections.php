<?php
include("db_config.php");

$conn = pg_pconnect("host=" . $host . " dbname=" . $database . " user=" . $username . " password=" . $password);
if (!$conn) {
  echo "Could not connect to database!\n";
  exit;
}

$result = pg_query($conn, "SELECT sections.*, sp.latitude, sp.longitude, sp.name, sp2.latitude AS prev_latitude, sp2.longitude AS prev_longitude, sp2.name AS prev_name FROM ( SELECT ds.stoppoint_loid, vs2.stoppoint_loid AS prev_sp_loid, COUNT(*) AS total, COUNT(ds.delaysec) AS delayed, COUNT(*) - COUNT(ds.delaysec) AS not_delayed, AVG(ds.delaysec) AS avg_delay, MAX(ds.delaysec), MIN(ds.delaysec), (COUNT(ds.delaysec) :: FLOAT / COUNT(*)) * 100 AS delay_rate FROM csipdaystopping ds LEFT JOIN csipcoursestopping cs ON cs.loid = ds.stopping_loid INNER JOIN csipcoursestopping cs2 ON (cs2.course_loid = cs.course_loid AND cs2.orderincourse = cs.orderincourse - 1) LEFT JOIN csipvariantstopping vs2 ON vs2.loid = cs2.variantstopping_loid WHERE (ds.delaysec IS NULL OR (ds.delaysec > 0 AND (ds.delaysec > 30 AND ds.delaysec < 1.5 * 60 * 60 OR ds.delaysec < -30 AND ds.delaysec > -1.5 * 60 * 60)) ) GROUP BY ds.stoppoint_loid, vs2.stoppoint_loid ORDER BY delay_rate DESC LIMIT 5 ) AS sections LEFT JOIN csipstoppoint sp ON sp.loid = sections.stoppoint_loid LEFT JOIN csipstoppoint sp2 ON sp2.loid = sections.prev_sp_loid");

if (!$result) {
  echo "Could not perform query!\n";
  exit;
}

$resultArray = pg_fetch_all($result);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resultArray);
?>