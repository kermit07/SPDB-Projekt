<!DOCTYPE html>
<html>
  <head>
    <title>LineString Arrows</title>
	<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://openlayers.org/en/v3.20.1/css/ol.css" type="text/css">
	
    <!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
    <script src="https://openlayers.org/en/v3.20.1/build/ol.js"></script>
	
	<style media="screen" type="text/css">
	  .popover-content {
		width: 280px;
	  }
	</style>
  </head>
  <body>
    <div id="map" class="map"><div id="popup"></div></div>
  </body>
  <script src="js/main.js"></script>
</html>
	

<?php
/*
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
$jsonArray = json_encode($resultArray); */
?>


<script>
// var obj = JSON.parse('<?php echo $jsonArray; ?>');
var obj = JSON.parse('[{"loid":"668968","distancefromschedule":"0","orderinvariant":"0","sp_loid":"440","latitude":"53.153843","longitude":"23.133523","name":"Berlinga\/Dziesi\u0119ciny","total":"1758","delayed":"106","not_delayed":"1652","avg_delay":"96.9245283018867925","delay_rate":"6.02957906712173"},{"loid":"668969","distancefromschedule":"42","orderinvariant":"1","sp_loid":"365","latitude":"53.153655","longitude":"23.133888","name":"Berlinga\/Dziesi\u0119ciny P\u0119tla","total":"1783","delayed":"137","not_delayed":"1646","avg_delay":"97.2700729927007299","delay_rate":"7.68367919237241"},{"loid":"668970","distancefromschedule":"179","orderinvariant":"2","sp_loid":"346","latitude":"53.154232","longitude":"23.133782","name":"Berlinga\/Dziesi\u0119ciny","total":"1781","delayed":"148","not_delayed":"1633","avg_delay":"117.0337837837837838","delay_rate":"8.30993823694554"},{"loid":"668971","distancefromschedule":"602","orderinvariant":"3","sp_loid":"347","latitude":"53.154418","longitude":"23.127077","name":"Berlinga\/Palmowa","total":"1783","delayed":"150","not_delayed":"1633","avg_delay":"160.6600000000000000","delay_rate":"8.41278743690409"},{"loid":"668972","distancefromschedule":"444","orderinvariant":"4","sp_loid":"348","latitude":"53.150838","longitude":"23.122882","name":"Berlinga\/Gajowa","total":"1783","delayed":"139","not_delayed":"1644","avg_delay":"140.7625899280575540","delay_rate":"7.79584969153113"},{"loid":"668973","distancefromschedule":"471","orderinvariant":"5","sp_loid":"441","latitude":"53.147935","longitude":"23.118668","name":"Antoniuk F. \/ Konstytucji 3 Maja","total":"1782","delayed":"133","not_delayed":"1649","avg_delay":"150.4661654135338346","delay_rate":"7.4635241301908"},{"loid":"668974","distancefromschedule":"703","orderinvariant":"6","sp_loid":"442","latitude":"53.145457","longitude":"23.128130","name":"Antoniuk F. \/ Ogrodniczki","total":"1783","delayed":"129","not_delayed":"1654","avg_delay":"135.5968992248062016","delay_rate":"7.23499719573752"},{"loid":"668975","distancefromschedule":"525","orderinvariant":"7","sp_loid":"368","latitude":"53.143127","longitude":"23.133978","name":"Antoniukowska\/Wierzbowa","total":"1773","delayed":"117","not_delayed":"1656","avg_delay":"109.6153846153846154","delay_rate":"6.5989847715736"},{"loid":"668976","distancefromschedule":"413","orderinvariant":"8","sp_loid":"369","latitude":"53.140238","longitude":"23.138163","name":"Antoniukowska\/Knyszy\u0144ska","total":"1781","delayed":"114","not_delayed":"1667","avg_delay":"141.6578947368421053","delay_rate":"6.40089837170129"},{"loid":"668977","distancefromschedule":"1066","orderinvariant":"9","sp_loid":"287","latitude":"53.135711","longitude":"23.144139","name":"D\u0105browskiego P.K.","total":"1780","delayed":"107","not_delayed":"1673","avg_delay":"141.2803738317757009","delay_rate":"6.01123595505618"},{"loid":"668978","distancefromschedule":"354","orderinvariant":"10","sp_loid":"370","latitude":"53.133954","longitude":"23.149067","name":"Lipowa\/\u015aw. Rocha","total":"1779","delayed":"101","not_delayed":"1678","avg_delay":"141.6237623762376238","delay_rate":"5.67734682405846"},{"loid":"668979","distancefromschedule":"584","orderinvariant":"11","sp_loid":"22","latitude":"53.133732","longitude":"23.156895","name":"Malmeda\/Zamenhofa","total":"1781","delayed":"111","not_delayed":"1670","avg_delay":"169.7027027027027027","delay_rate":"6.23245367770915"},{"loid":"668980","distancefromschedule":"624","orderinvariant":"12","sp_loid":"175","latitude":"53.134332","longitude":"23.164218","name":"Pi\u0142sudskiego\/Sienkiewicza","total":"1776","delayed":"112","not_delayed":"1664","avg_delay":"159.6071428571428571","delay_rate":"6.30630630630631"},{"loid":"668981","distancefromschedule":"539","orderinvariant":"13","sp_loid":"489","latitude":"53.132693","longitude":"23.169845","name":"Pa\u0142acowa\/Hotel","total":"1768","delayed":"100","not_delayed":"1668","avg_delay":"153.5800000000000000","delay_rate":"5.65610859728507"},{"loid":"668982","distancefromschedule":"445","orderinvariant":"14","sp_loid":"388","latitude":"53.134282","longitude":"23.174828","name":"S\u0142onimska\/Urz\u0105d Miejski","total":"1757","delayed":"84","not_delayed":"1673","avg_delay":"137.0119047619047619","delay_rate":"4.7808764940239"},{"loid":"668983","distancefromschedule":"595","orderinvariant":"15","sp_loid":"389","latitude":"53.134720","longitude":"23.183425","name":"Piasta","total":"1750","delayed":"71","not_delayed":"1679","avg_delay":"149.2816901408450704","delay_rate":"4.05714285714286"},{"loid":"668984","distancefromschedule":"454","orderinvariant":"16","sp_loid":"390","latitude":"53.133587","longitude":"23.191238","name":"Mieszka I\/Apteka","total":"1761","delayed":"89","not_delayed":"1672","avg_delay":"164.7640449438202247","delay_rate":"5.05394662123793"},{"loid":"668985","distancefromschedule":"333","orderinvariant":"17","sp_loid":"255","latitude":"53.133277","longitude":"23.194305","name":"Piastowska\/Ko\u015bci\u00f3\u0142","total":"1762","delayed":"104","not_delayed":"1658","avg_delay":"141.7019230769230769","delay_rate":"5.90238365493757"},{"loid":"668986","distancefromschedule":"328","orderinvariant":"18","sp_loid":"256","latitude":"53.135772","longitude":"23.195150","name":"Towarowa\/Piastowska","total":"1772","delayed":"113","not_delayed":"1659","avg_delay":"172.3893805309734513","delay_rate":"6.37697516930023"},{"loid":"668987","distancefromschedule":"223","orderinvariant":"19","sp_loid":"257","latitude":"53.136733","longitude":"23.192543","name":"Towarowa\/Gara\u017ce","total":"1769","delayed":"108","not_delayed":"1661","avg_delay":"161.3240740740740741","delay_rate":"6.10514414923686"},{"loid":"668988","distancefromschedule":"453","orderinvariant":"20","sp_loid":"860","latitude":"53.138308","longitude":"23.186757","name":"Towarowa\/P\u0119tla","total":"1787","delayed":"0","not_delayed":"1787","avg_delay":null,"delay_rate":"0"}]');
draw(obj)
</script>
