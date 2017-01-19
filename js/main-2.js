var raster = new ol.layer.Tile({
    source: new ol.source.OSM()
});

var map = new ol.Map({
    layers: [raster],
    target: document.getElementById('map'),
    view: new ol.View({
        center: [2577039, 7007526],
        zoom: 12.5
    })
});

var dbResults = [];
var colors = [];
var arrowLayers = [];

function loadDbResults(data) {
	dbResults = [];
    for (var i = 0; i < data.length; i++) {
        var prevX = parseFloat(data[i].prev_longitude);
        var prevY = parseFloat(data[i].prev_latitude);
        var x = parseFloat(data[i].longitude);
        var y = parseFloat(data[i].latitude);
        var prevCrd = ol.proj.transform([prevX, prevY], 'EPSG:4326', 'EPSG:3857')
        var crd = ol.proj.transform([x, y], 'EPSG:4326', 'EPSG:3857')
		console.log("prevCrd: " + prevY + ", " + prevX)
		console.log("crd: " + y + ", " + x)
		var dRate = Math.round(data[i].delay_rate) + "%";
		var dAvg = "bd";
		var color = "rgb(255,255,255)";
		if(data[i].avg_delay != null) {
			dAvg = Math.floor(parseFloat(data[i].avg_delay)/60) + " min " + Math.floor(parseFloat(data[i].avg_delay)%60) + " sec";
			color = numberToColorHsl(parseFloat(data[i].avg_delay), 0, 15 * 60);
		}
		dbResults[i] = {prevCrd: prevCrd, crd: crd, delayRate: dRate, delayAvg: dAvg, stopName: data[i].name};
		console.log(color)
    }
}


var arrayStyleFunction = function(feature) {
	var geometry = feature.getGeometry();
	var styles = [
	  // linestring
	  new ol.style.Style({
		stroke: new ol.style.Stroke({
		  color: '#FF0000',
		  width: 2
		})
	  })
	];

	geometry.forEachSegment(function(start, end) {
	  var dx = end[0] - start[0];
	  var dy = end[1] - start[1];
	  var rotation = Math.atan2(dy, dx);
	  // arrows
	  styles.push(new ol.style.Style({
		geometry: new ol.geom.Point(end),
		image: new ol.style.Icon({
		  src: 'images/arrow.png',
		  anchor: [0.75, 0.5],
		  rotateWithView: true,
		  rotation: -rotation
		})
	  }));
});

return styles;
};

function drawLines() {
	for (var i = 0; i < dbResults.length; i++) {
		var coordinates = [ dbResults[i].prevCrd, dbResults[i].crd ];
		
		arrowLayer = new ol.layer.Vector({
			source: new ol.source.Vector({
				features: [new ol.Feature({
					geometry: new ol.geom.LineString(coordinates, 'XY'),
					stopName: dbResults[i].stopName,
					delayRate: dbResults[i].delayRate,
					delayAvg: dbResults[i].delayAvg
					
				})]
			}),
			style: arrayStyleFunction
		});
		arrowLayers.push(arrowLayer);
		map.addLayer(arrowLayer);
	}
}

function draw(data) {
    loadDbResults(data);
	for (var i = 0; i < arrowLayers.length; i++) {
		map.removeLayer(arrowLayers[i]);
	}
	arrowLayers = [];
	
    drawLines();
}


var element = document.getElementById('popup');

var popup = new ol.Overlay({
    element: element,
    positioning: 'bottom-center',
    stopEvent: false
});
map.addOverlay(popup);

var actualPopupContent = "Nazwa przystanku<br/>Prawdopodobieństo opóźnienia: 10%<br/>Średni czas opóźnienia: 213";

map.on('click', function(evt) {
	var feature = map.forEachFeatureAtPixel(evt.pixel,
        function(feature) {
            return feature;
        }
    );
    if (feature && feature.getGeometry() instanceof ol.geom.LineString) {
		actualPopupContent = feature.get('stopName') + "<br/>"
		                     + "Prawdopodobieństo opóźnienia: " + feature.get('delayRate') + "<br/>"
							 + "Średni czas opóźnienia: " + feature.get('delayAvg');
        popup.setPosition(evt.coordinate);
        $(element).popover({
            'placement': 'top',
            'html': true,
            'content': function() {
				 return actualPopupContent;
			}
        });
        $(element).popover('show');
    } else {
		$(element).popover('destroy');
	}
});

// change mouse cursor when over marker
map.on('pointermove', function(e) {
    var pixel = map.getEventPixel(e.originalEvent);
    var hit = false;
	map.forEachFeatureAtPixel(pixel, function (feature) {
		if(feature.getGeometry() instanceof ol.geom.LineString)
			hit = true;
	});
    map.getTarget().style.cursor = hit ? 'pointer' : '';
});