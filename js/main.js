var raster = new ol.layer.Tile({
    source: new ol.source.OSM()
});

var map = new ol.Map({
    layers: [raster],
    target: 'map',
    view: new ol.View({
        center: [2577039, 7007526],
        zoom: 12.5
    })
});


var markers = [];

function loadMarkers(data) {
    for (var i = 0; i < data.length; i++) {
        var x = parseFloat(data[i].longitude);
        var y = parseFloat(data[i].latitude);
		console.log("data: " +  data[i].longitude + " " +  data[i].latitude)
        markers[i] = ol.proj.transform([x, y], 'EPSG:4326', 'EPSG:3857');
    }
}

function drawLines() {
	var layerLines = new ol.layer.Vector({
		source: new ol.source.Vector({
			features: [new ol.Feature({
				geometry: new ol.geom.LineString(markers, 'XY'),
				name: 'Line'
			})]
		}),
		style: new ol.style.Style({
			stroke: new ol.style.Stroke(({
				width: 4
			}))
		})
	});
	map.addLayer(layerLines);
}

function drawPoints() {
	var pointsFeatures = [];
	for (var i = 0; i < markers.length; i++) {
        pointsFeatures.push(
			new ol.Feature({
				geometry: new ol.geom.Point(markers[i]),
				name: 'Marker ' + i
			})
		);
    }
	
	var layerPoints = new ol.layer.Vector({
		source: new ol.source.Vector({
			features: pointsFeatures
		})
	});
	
	map.addLayer(layerPoints);
}

function draw(data) {
	loadMarkers(data);
	
	drawLines();
	drawPoints();
	
	console.log("finish")
}
