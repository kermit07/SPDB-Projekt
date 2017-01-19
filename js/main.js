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

function hslToRgb(h, s, l){
    var r, g, b;

    if(s == 0){
        r = g = b = l; // achromatic
    }else{
        var hue2rgb = function hue2rgb(p, q, t){
            if(t < 0) t += 1;
            if(t > 1) t -= 1;
            if(t < 1/6) return p + (q - p) * 6 * t;
            if(t < 1/2) return q;
            if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
            return p;
        }

        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }

    return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
}

function numberToColorHsl(val, minVal, maxVal) {
	var i = val / (maxVal - minVal) + minVal;
	console.log(i)
    // as the function expects a value between 0 and 1, and red = 0° and green = 120°
    // we convert the input to the appropriate hue value
    var hue = (1 - i) * 80 / 360;
    // we convert hsl to rgb (saturation 100%, lightness 50%)
    var rgb = hslToRgb(hue, 1, .5);
    
    return 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ')'; 
}

var markers = [];
var colors = [];

function loadMarkers(data) {
    for (var i = 0; i < data.length; i++) {
        var x = parseFloat(data[i].longitude);
        var y = parseFloat(data[i].latitude);
        var crd = ol.proj.transform([x, y], 'EPSG:4326', 'EPSG:3857')
		var dRate = Math.round(data[i].delay_rate) + "%";
		var dAvg = "bd";
		var color = "rgb(255,255,255)";
		if(data[i].avg_delay != null) {
			dAvg = Math.floor(parseFloat(data[i].avg_delay)/60) + " min " + Math.floor(parseFloat(data[i].avg_delay)%60) + " sec";
			color = numberToColorHsl(parseFloat(data[i].avg_delay), 0, 15 * 60);
		}
		markers[i] = {coordinates: crd, delayRate: dRate, delayAvg: dAvg, stopName: data[i].name};
		colors[i] = color;
    }
}


var styleFunction = function(feature, resolution) {
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

function drawLines() {
	var coordinates = markers.map(function(a) {return a.coordinates;});
    var layerLines = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: [new ol.Feature({
                geometry: new ol.geom.LineString(coordinates, 'XY'),
                name: 'Line'
            })]
        }),
        style: styleFunction
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