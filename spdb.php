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
                        <label for="lineSelect" style="margin-left: 20px;">Linia:</label>
                        <select id="lineSelect" onchange="onLineSelected(this.value)">
                            <option value="-1">Wybierz linię</option>
                        </select>

                        <label for="variantSelect" style="margin-left: 20px;">Wariant:</label>
                        <select id="variantSelect">
                            <option value="-1">Wybierz wariant</option>
                        </select>

                        <label for="daySelect" style="margin-left: 20px;">Dzień tygodnia:</label>
                        <select id="daySelect">
                            <option value="-1">Wybierz dzień</option>
                            <option value="1">Poniedziałek</option>
                            <option value="2">Wtorek</option>
                            <option value="3">Środa</option>
                            <option value="4">Czwartek</option>
                            <option value="5">Piątek</option>
                            <option value="6">Sobota</option>
                            <option value="0">Niedziela</option>
                        </select>

                        <label for="hourRangeSelect" style="margin-left: 20px;">Przedział godzinowy:</label>
                        <select id="hourRangeSelect">
                            <option value="-1">Wybierz przedział</option>
                        </select>

                        <label style="margin-left: 20px;"><input type="radio" name="delay" value="1" checked> Opóźnienia</label>
                        <label style="margin-left: 5px;"><input type="radio" name="delay" value="0"> Przyspieszenia</label>

                        <button type="button" onclick="loadData()" style="margin-left: 20px;">Załaduj dane</button>
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
        return setInterval(setTime, 1000);
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
        setupHourRanges();
    }

    function setupHourRanges() {
        for(var i = 0; i < 24; i = i + 2) {
            $('#hourRangeSelect').append($('<option>', {
                value: i,
                text: i + ' - ' + (i+2)
            }));
        }
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
            var timer = startTimer();
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
                clearInterval(timer);
            });
        }
    }

    function loadData() {
        var startHour = parseInt($("#hourRangeSelect").val());
        var day = parseInt($("#daySelect").val());
        var delay = parseInt($('input[name=delay]:checked').val());
        var variantLoid = parseInt($("#variantSelect").val());

        if(variantLoid != -1) {
            toggleLoader();
            var timer = startTimer();
            $.get("variant.php", {variant_loid: variantLoid, start_hour: startHour, end_hour: startHour+2, day: day, delay: delay}, function(data) {
                draw(data);
                toggleLoader();
                clearInterval(timer);
            });
        }
    }

    function toggleLoader() {
        $('#loader').toggle();
    }
</script>
