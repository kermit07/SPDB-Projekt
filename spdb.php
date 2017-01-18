<!DOCTYPE html>
<html>
  <head>
    <title>LineString Arrows</title>
    <link rel="stylesheet" href="https://openlayers.org/en/v3.20.1/css/ol.css" type="text/css">
    <!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
    <script src="https://openlayers.org/en/v3.20.1/build/ol.js"></script>
  </head>
  <body>
    <div id="map" class="map"></div>
    <script src="js/main.js"></script>
  </body>
</html>
	

<?php

$conn = pg_pconnect("host=localhost dbname=mydb user=postgres password=asdasd");
if (!$conn) {
  echo "An error occurred1.\n";
  exit;
}

$vid = 41089;
$result = pg_query($conn, "SELECT vs.loid, vs.distancefromschedule, vs.orderinvariant, sp.loid AS sp_loid, sp.latitude, sp.longitude, sp.name, delay.total, delay.delayed, delay.not_delayed, delay.avg_delay, delay.delay_rate FROM csipvariantstopping vs LEFT JOIN csipstoppoint sp ON sp.loid = vs.stoppoint_loid LEFT JOIN ( SELECT vs.loid AS vs_loid, COUNT(*) AS total, COUNT(ds.delaysec) AS delayed, COUNT(*) - COUNT(ds.delaysec) AS not_delayed, AVG(ds.delaysec) AS avg_delay, (COUNT(ds.delaysec) :: FLOAT / COUNT(*)) * 100 AS delay_rate FROM csipvariantstopping vs LEFT JOIN csipcoursestopping cs ON cs.variantstopping_loid = vs.loid LEFT JOIN csipdaystopping ds ON ds.stopping_loid = cs.loid WHERE (ds.delaysec IS NULL OR (ds.delaysec > 0 AND ds.delaysec < 1.5 * 60 * 60 AND ds.delaysec > -1.5 * 60 * 60) ) AND vs.variant_loid = ".$vid." GROUP BY vs.loid ) AS delay ON delay.vs_loid = vs.loid WHERE vs.variant_loid = ".$vid);
if (!$result) {
  echo "An error occurred2.\n";
  exit;
}

$resultArray = pg_fetch_all($result);
$jsonArray = json_encode($resultArray);
?>


<script>
var obj = JSON.parse('<?php echo $jsonArray; ?>');
console.log(obj)
draw(obj)
</script>
