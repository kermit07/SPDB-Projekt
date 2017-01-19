<?php
include("db_config.php");

$conn = pg_pconnect("host=" . $host . " dbname=" . $database . " user=" . $username . " password=" . $password);
if (!$conn) {
  echo "Could not connect to database!\n";
  exit;
}

$result = pg_query($conn, "SELECT l.loid, l.name FROM csipline l ORDER BY LENGTH(l.name), l.name");

if (!$result) {
  echo "Could not perform query!\n";
  exit;
}

$resultArray = pg_fetch_all($result);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resultArray);
?>