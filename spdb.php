<!DOCTYPE html>
<html>
    <head>
        <title>SPDB Projekt</title>

        <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
        <link rel="stylesheet" href="https://openlayers.org/en/v3.20.1/css/ol.css" type="text/css">

    	<script src="js/jquery-3.1.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
        <script src="https://openlayers.org/en/v3.20.1/build/ol.js"></script>

        <style media="screen" type="text/css">
            .popover-content {
                width: 280px;
            }
        </style>
    </head>
    <body onload="init()">
        <div style="margin: 20px;">
            <label for="lineSelect">Linia:</label>
            <select id="lineSelect" onchange="onLineSelected(this.value)">
                <option value="-1">Wybierz linię</option>
            </select>

            <label for="variantSelect" style="margin-left: 20px;">Wariant:</label>
            <select id="variantSelect" onchange="onVariantSelected(this.value)">
                <option value="-1">Wybierz wariant</option>
            </select>
        </div>
        <div id="map" class="map"><div id="popup"></div></div>
    </body>
    <script src="js/main.js"></script>
</html>

<script>
    function init() {
        loadLines();
    }

    function loadLines() {
        $.get("lines.php", function(data) {
            data.forEach(function(line) {
                $('#lineSelect').append($('<option>', {
                    value: line.loid,
                    text: line.name
                }));
            });
        });
    }

    function onLineSelected(lineLoid) {
        if(lineLoid != -1) {
            $.get("variants.php", {line_loid: lineLoid}, function(data) {
                $('#variantSelect').empty();
                $('#variantSelect').append($('<option>', {
                    value: -1,
                    text: 'Wybierz wariant'
                }));

                data.forEach(function(variant) {
                    $('#variantSelect').append($('<option>', {
                        value: variant.loid,
                        text: '(' + variant.day_stops + ') ' + variant.logicalkey
                    }));
                });
            });
        }
    }

    function onVariantSelected(variantLoid) {
        if(variantLoid != -1) {
            $.get("variant.php", {variant_loid: variantLoid}, function(data) {
                draw(data)
            });
        }
    }
</script>
