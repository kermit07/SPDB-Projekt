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
    // as the function expects a value between 0 and 1, and red = 0° and green = 120°
    // we convert the input to the appropriate hue value
    var hue = (1 - i) * 80 / 360;
    // we convert hsl to rgb (saturation 100%, lightness 50%)
    var rgb = hslToRgb(hue, 1, .5);
    
    return 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ')'; 
}

function mkUpLimit(data) {
	var maxVal = 0;
	for (var i = 0; i < data.length; i++) {
		if(data[i] > maxVal)
			maxVal = data[i];
	}
	
}