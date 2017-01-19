<!DOCTYPE html>
<html>
    <head>
        <title>SPDB Projekt</title>

        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://openlayers.org/en/v3.20.1/css/ol.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">

    	<script src="js/jquery-3.1.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
        <script src="https://openlayers.org/en/v3.20.1/build/ol.js"></script>
    </head>
    <body onload="init()" style="padding: 0; margin: 0;">
        <div class="main-container">
            <div class="header">
                <label for="lineSelect">Linia:</label>
                <select id="lineSelect" onchange="onLineSelected(this.value)">
                    <option value="-1">Wybierz linię</option>
                </select>

                <label for="variantSelect" style="margin-left: 20px;">Wariant:</label>
                <select id="variantSelect" onchange="onVariantSelected(this.value)">
                    <option value="-1">Wybierz wariant</option>
                </select>
            </div>
            <div class="content">
                <div id="map" class="map">
                    <div id="popup"></div>
                </div>
            </div>
        </div>
        <div class="loader-container" id="loader">
            <div style="position: absolute; top: 50%; left: 45%;">
                <div class="loader"></div>
                <div style="color: silver; padding-top: 10px;">Trwa ładowanie danych...</div>
            </div>
        </div>
    </body>
    <script src="js/main.js"></script>
</html>

<script>
    function init() {
        loadLines();
    }

    function loadLines() {
        toggleLoader();
        $.get("lines.php", function(data) {
            data.forEach(function(line) {
                $('#lineSelect').append($('<option>', {
                    value: line.loid,
                    text: line.name
                }));
            });
            toggleLoader();
        });
    }

    function onLineSelected(lineLoid) {
        if(lineLoid != -1) {
            toggleLoader();
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
                toggleLoader();
            });
        }
    }

    function onVariantSelected(variantLoid) {
        if(variantLoid != -1) {
            toggleLoader();
            $.get("variant.php", {variant_loid: variantLoid}, function(data) {
                draw(data)
                toggleLoader();
            });
        }
    }

    function toggleLoader() {
        $('#loader').toggle();
    }
</script>
