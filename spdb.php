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
<<<<<<< e8a8067f15be70cc04d51c00e358128353e47ea3
	
	<style media="screen" type="text/css">
	  .popover-content {
		width: 280px;
	  }
	</style>
  </head>
  <body>
    <div id="map" class="map"><div id="popup"></div></div>
  </body>
=======
</head>
<body onload="init()">
    <div style="margin: 20px;">
        <label for="lineSelect">Linia:</label>
        <select id="lineSelect">
            <option value="-1">Wybierz linię</option>
        </select>
    </div>
  <div id="map" class="map"></div>

>>>>>>> Dodanie wyboru linii autobusowej
  <script src="js/main.js"></script>
</html>
<<<<<<< e8a8067f15be70cc04d51c00e358128353e47ea3
=======

<script>
    function init() {
        $.get("lines.php", function(data) {
            data.forEach(function(line) {
                $('#lineSelect').append($('<option>', {
                    value: line.loid,
                    text: line.name
                }));
            });
        });

        $.get("variant.php", function(data) {
            draw(data)
        });
    }
</script>
>>>>>>> Dodanie wyboru linii autobusowej
