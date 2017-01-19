<!DOCTYPE html>
<html>
    <head>
        <title>SPDB Projekt - Linie</title>

        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://openlayers.org/en/v3.20.1/css/ol.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">

    	<script src="js/jquery-3.1.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
        <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
        <script src="https://openlayers.org/en/v3.20.1/build/ol.js"></script>
    </head>
    <body onload="init()">
        <div class="main-container">
            <div class="header" style="display: table; width: 100%;">
                <div style="display: table-row;">
                    <div style="display: table-cell; width: 300px;">
                        <h3 style="margin: 0;">Opóźnienia według linii</h3>
                    </div>
                    <div style="display: table-cell;">
                        <a href="index.html">Powrót</a>
                    </div>
                    <div style="display: table-cell;">
                        <label for="lineSelect">Linia:</label>
                        <select id="lineSelect" onchange="onLineSelected(this.value)">
                            <option value="-1">Wybierz linię</option>
                        </select>

                        <label for="variantSelect" style="margin-left: 20px;">Wariant:</label>
                        <select id="variantSelect" onchange="onVariantSelected(this.value)">
                            <option value="-1">Wybierz wariant</option>
                        </select>
                    </div>
                </div>
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
                <div style="color: silver; padding-top: 10px;">
                    Trwa ładowanie danych...
                    <label id="minutes">00</label>:<label id="seconds">00</label>
                </div>
            </div>
        </div>
    </body>
    <script src="js/utils.js"></script>
    <script src="js/main.js"></script>
</html>

<script type="text/javascript">
    var minutesLabel = document.getElementById("minutes");
    var secondsLabel = document.getElementById("seconds");
    var totalSeconds = 0;
    
    function startTimer() {
        totalSeconds = 0;
        setInterval(setTime, 1000);
    }

    function setTime()
    {
        ++totalSeconds;
        secondsLabel.innerHTML = pad(totalSeconds%60);
        minutesLabel.innerHTML = pad(parseInt(totalSeconds/60));
    }

    function pad(val)
    {
        var valString = val + "";
        if(valString.length < 2)
        {
            return "0" + valString;
        }
        else
        {
            return valString;
        }
    }
</script>

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
            startTimer();
            $.get("variants.php", {line_loid: lineLoid}, function(data) {
                $('#variantSelect').empty();
                $('#variantSelect').append($('<option>', {
                    value: -1,
                    text: 'Wybierz wariant'
                }));

                data.forEach(function(variant) {
                    $('#variantSelect').append($('<option>', {
                        value: variant.loid,
                        text: variant.loid + ' (' + variant.day_stops + ')'
                    }));
                });
                toggleLoader();
            });
        }
    }

    function onVariantSelected(variantLoid) {
        if(variantLoid != -1) {
            toggleLoader();
            startTimer();
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
