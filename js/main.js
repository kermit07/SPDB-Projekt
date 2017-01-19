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


var markers = [];

function loadMarkers(data) {
    for (var i = 0; i < data.length; i++) {
        var x = parseFloat(data[i].longitude);
        var y = parseFloat(data[i].latitude);
        var crd = ol.proj.transform([x, y], 'EPSG:4326', 'EPSG:3857')
		var dRate = Math.round(data[i].delay_rate) + "%";
		var dAvg = "bd";
		if(data[i].avg_delay != null)
			dAvg = Math.floor(parseFloat(data[i].avg_delay)/60) + " min " + Math.floor(parseFloat(data[i].avg_delay)%60) + " sec";
		markers[i] = {coordinates: crd, delayRate: dRate, delayAvg: dAvg, stopName: data[i].name};
    }
}

function drawLines() {
	var coordinates = markers.map(function(a) {return a.coordinates;});
    var layerLines = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: [new ol.Feature({
                geometry: new ol.geom.LineString(coordinates, 'XY'),
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
                geometry: new ol.geom.Point(markers[i].coordinates),
                stopName: markers[i].stopName,
                delayRate: markers[i].delayRate,
                delayAvg: markers[i].delayAvg
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
}
var element = document.getElementById('popup');

var popup = new ol.Overlay({
    element: element,
    positioning: 'bottom-center',
    stopEvent: false
});
map.addOverlay(popup);

var actualPopupContent = "Nazwa przystanku<br/>Prawdopodobieństwo opóźnienia: 10%<br/>Średni czas opóźnienia: 213";

map.on('click', function(evt) {
	var feature = map.forEachFeatureAtPixel(evt.pixel,
        function(feature) {
            return feature;
        }
    );
    if (feature && feature.getGeometry() instanceof ol.geom.Point) {
		actualPopupContent = feature.get('stopName') + "<br/>"
		                     + "Prawdopodobieństwo opóźnienia: " + feature.get('delayRate') + "<br/>"
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
    if (e.dragging) {
        $(element).popover('destroy');
        return;
    }
    var pixel = map.getEventPixel(e.originalEvent);
    var hit = false;
	map.forEachFeatureAtPixel(pixel, function (feature) {
		if(feature.getGeometry() instanceof ol.geom.Point)
			hit = true;
	});
    map.getTarget().style.cursor = hit ? 'pointer' : '';
});
