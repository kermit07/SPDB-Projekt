﻿var raster = new ol.layer.Tile({
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
var colors = [];
var layerLines = [];
var layerFirstPoint = [];
var layerPoints = [];
var layerLastPoint = [];

function loadMarkers(data) {
	markers = [];
    for (var i = 0; i < data.length; i++) {
        var x = parseFloat(data[i].longitude);
        var y = parseFloat(data[i].latitude);
        var crd = ol.proj.transform([x, y], 'EPSG:4326', 'EPSG:3857')
		var dRate = Math.round(data[i].delay_rate) + "%";
		var dAvg = "bd";
		var color = "rgb(255,255,255)";
		if(data[i].avg_delay != null) {
			dAvg = Math.floor(parseFloat(data[i].avg_delay)/60) + " min " + Math.floor(parseFloat(data[i].avg_delay)%60) + " sec";
			color = numberToColorHsl(Math.abs(parseFloat(data[i].avg_delay)), 0, 15 * 60);
		}
		markers[i] = {coordinates: crd, delayRate: dRate, delayAvg: dAvg, stopName: data[i].name};
		if(i > 0) // przesunięcie ponieważ rysuje linie inaczej niż byśmy chcieli
			colors[i-1] = color;
    }
	color[data.length - 1] = "#FFFFFF"
}


var lineStyleFunction = function(feature, resolution) {
	var styles = [];

    var i = 0, geometry = feature.getGeometry();

    geometry.forEachSegment(function (start, end) {

        color = colors[i];

        styles.push(new ol.style.Style({
            geometry: new ol.geom.LineString([start, end]),
            fill: new ol.style.Fill({
                color: color
            }),
            stroke: new ol.style.Stroke({
                color: color,
                width: 4
            })
        }));

        i++;
    });

    return styles;
}

var firstPointStyleFunction = function(feature, resolution) {
	
	return [
		new ol.style.Style({
			image: new ol.style.Circle({
				fill:new ol.style.Fill({color: "green"}),
				stroke: new ol.style.Stroke({color: 'black', width: 1}),
				radius: 4
			})
		})
	];
}

var pointsStyleFunction = function(feature, resolution) {
	
	return [
		new ol.style.Style({
			image: new ol.style.Circle({
				fill:new ol.style.Fill({color: "white"}),
				stroke: new ol.style.Stroke({color: 'black', width: 1}),
				radius: 4
			})
		})
	];
}

var lastPointStyleFunction = function(feature, resolution) {
	
	return [
		new ol.style.Style({
			image: new ol.style.Circle({
				fill:new ol.style.Fill({color: "red"}),
				stroke: new ol.style.Stroke({color: 'black', width: 1}),
				radius: 4
			})
		})
	];
}

function drawLines() {
	var coordinates = markers.map(function(a) {return a.coordinates;});
    layerLines = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: [new ol.Feature({
                geometry: new ol.geom.LineString(coordinates, 'XY'),
                name: 'Line'
            })]
        }),
        style: lineStyleFunction
    });
    map.addLayer(layerLines);
}

function drawPoints() {
	var firstPointsFeature = [
		new ol.Feature({
			geometry: new ol.geom.Point(markers[0].coordinates),
			stopName: markers[0].stopName,
			delayRate: markers[0].delayRate,
			delayAvg: markers[0].delayAvg
		})
	];
	var lastPointsFeature = [
		new ol.Feature({
			geometry: new ol.geom.Point(markers[markers.length - 1].coordinates),
			stopName: markers[markers.length - 1].stopName,
			delayRate: markers[markers.length - 1].delayRate,
			delayAvg: markers[markers.length - 1].delayAvg
		})
	];
	
    var pointsFeatures = [];
    for (var i = 1; i < markers.length - 1; i++) {
        pointsFeatures.push(
            new ol.Feature({
                geometry: new ol.geom.Point(markers[i].coordinates),
                stopName: markers[i].stopName,
                delayRate: markers[i].delayRate,
                delayAvg: markers[i].delayAvg
            })
        );
    }

    layerFirstPoint = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: firstPointsFeature
        }),
		style: firstPointStyleFunction
    });

    layerPoints = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: pointsFeatures
        }),
		style: pointsStyleFunction
    });

    layerLastPoint = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: lastPointsFeature
        }),
		style: lastPointStyleFunction
    });

    map.addLayer(layerFirstPoint);
    map.addLayer(layerPoints);
    map.addLayer(layerLastPoint);
}

function drawMostDelayedSections(data) {
	loadMarkers(data);
	if(layerLines)
		map.removeLayer(layerLines);
	if(layerFirstPoint)
		map.removeLayer(layerFirstPoint);
	if(layerPoints)
		map.removeLayer(layerPoints);
	if(layerLastPoint)
		map.removeLayer(layerLastPoint);
    drawLines();
    drawPoints();
}

function draw(data) {
    loadMarkers(data);
	if(layerLines)
		map.removeLayer(layerLines);
	if(layerFirstPoint)
		map.removeLayer(layerFirstPoint);
	if(layerPoints)
		map.removeLayer(layerPoints);
	if(layerLastPoint)
		map.removeLayer(layerLastPoint);
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
    var pixel = map.getEventPixel(e.originalEvent);
    var hit = false;
	map.forEachFeatureAtPixel(pixel, function (feature) {
		if(feature.getGeometry() instanceof ol.geom.Point)
			hit = true;
	});
    map.getTarget().style.cursor = hit ? 'pointer' : '';
});