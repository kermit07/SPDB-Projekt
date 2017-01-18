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


var vectorSource = new ol.source.Vector({
    //create empty vector
});

var markers = [];

function AddMarkers(data) {
    //create a bunch of icons and add to source vector
    for (var i = 0; i < data.length; i++) {
        var x = parseFloat(data[i].longitude);
        var y = parseFloat(data[i].latitude);
		console.log("data: " +  data[i].longitude + " " +  data[i].latitude)
        markers[i] = ol.proj.transform([x, y], 'EPSG:4326', 'EPSG:3857');
		console.log(markers[i])

        var iconFeature = new ol.Feature({
            geometry: new ol.geom.Point(markers[i]),
            name: 'Marker ' + i
        });
        vectorSource.addFeature(iconFeature);
    }

    //create the style
    var iconStyle = new ol.style.Style({
        image: new ol.style.Icon( /** @type {olx.style.IconOptions} */ ({
            anchor: [0.5, 46],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            opacity: 0.75,
            src: 'http://upload.wikimedia.org/wikipedia/commons/a/ab/Warning_icon.png'
        }))
    });



    //add the feature vector to the layer vector, and apply a style to whole layer
    var vectorLayer = new ol.layer.Vector({
        source: vectorSource,
		style: iconStyle
    });
    return vectorLayer;
}

function draw(data) {
	var layerMarkers = AddMarkers(data);

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
	map.addLayer(layerMarkers);
	map.addLayer(layerLines);
	console.log("finish")
}
