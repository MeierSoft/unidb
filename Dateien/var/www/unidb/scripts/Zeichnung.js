var T_Text = new Array;
var zeichnen = false;
$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
});

document.querySelector("body").addEventListener ("keydown", function (event) {
	if (event.ctrlKey && event.keyCode == 37) {
		links.onclick();
	}
	if (event.ctrlKey && event.keyCode == 38) {
		rauf.onclick();
	}
	if (event.ctrlKey && event.keyCode == 39) {
		rechts.onclick();
	}
	if (event.ctrlKey && event.keyCode == 40) {
		runter.onclick();
	}
	if (event.ctrlKey && event.keyCode == 36) {
		startpos.onclick();
	}
	if (event.ctrlKey && event.keyCode == 46) {
		loeschen.onclick();
	}
	if (event.ctrlKey && event.keyCode == 67) {
		kopieren.onclick();
	}
	if (event.ctrlKey && event.keyCode == 86) {
		einfuegen.onclick();
	}
});

var canvas = null;

(function() {
	var $ = function(id){return document.getElementById(id)};
	var drawingModeEl = $('drawing-mode');
	drawingModeEl.onclick = function() {
		canvas.isDrawingMode = !canvas.isDrawingMode;
		if (canvas.isDrawingMode == true) {
			var Inhalt = '<div style="position: absolute; top: 10px; left: 10px;">';
			Inhalt = Inhalt + '	<div id="drawing-mode-options">';
			Inhalt = Inhalt + '		<table>';
	  		Inhalt = Inhalt + '			<tr>';
		  	Inhalt = Inhalt + '				<td align="right">Modus:</td>';
	  		Inhalt = Inhalt + '				<td width="20px"></td>';
			Inhalt = Inhalt + '				<td>';
			Inhalt = Inhalt + '					<select id="drawing-mode-selector">';
			Inhalt = Inhalt + '						<option value="Pencil">Bleistift</option>';
			Inhalt = Inhalt + '      				<option value="Circle">Kreise</option>';
			Inhalt = Inhalt + '	      			<option value="Spray">Spray</option>';
			Inhalt = Inhalt + '   	   			<option value="Pattern">Muster</option>';
			Inhalt = Inhalt + '	      			<option value="hline">h Linie</option>';
			Inhalt = Inhalt + '   	   			<option value="vline">v Linie</option>';
			Inhalt = Inhalt + '      				<option value="square">Quadrat</option>';
			Inhalt = Inhalt + '	      			<option value="diamond">Diamant</option>';
			Inhalt = Inhalt + '   	   			<option value="texture">Textur</option>';
			Inhalt = Inhalt + '	    			</select>';
			Inhalt = Inhalt + '   	 		</td>';
			Inhalt = Inhalt + '			</tr>';
			Inhalt = Inhalt + '			<tr>';
			Inhalt = Inhalt + '		  		<td align="right"><label for="drawing-line-width">Linienbreite:</label></td>';
			Inhalt = Inhalt + '		  		<td align="right" width="20px"><span id="linienbreite" class="info">1</span></td>';
			Inhalt = Inhalt + '				<td>';
			Inhalt = Inhalt + '    				<input type="range" value="1" min="1" max="15" id="drawing-line-width">';
			Inhalt = Inhalt + '	    		</td>';
			Inhalt = Inhalt + '   	 	</tr>';
			Inhalt = Inhalt + '			<tr>';
			Inhalt = Inhalt + '	  			<td align="right">Linienfarbe:</td>';
			Inhalt = Inhalt + '	  			<td width="20px"></td>';
			Inhalt = Inhalt + '				<td>';
			Inhalt = Inhalt + '					<input type="color" value="#000000" id="drawing-color">';
			Inhalt = Inhalt + '				</td>';
			Inhalt = Inhalt + '	    	</tr>';
			Inhalt = Inhalt + '			<tr>';
			Inhalt = Inhalt + '				<td align="right">Schattenfarbe:</td>';
			Inhalt = Inhalt + '				<td width="20px"></td>';
			Inhalt = Inhalt + '				<td>';
			Inhalt = Inhalt + '					<input type="color" value="#005E7A" id="drawing-shadow-color">';
			Inhalt = Inhalt + '				</td>';
			Inhalt = Inhalt + '   	 	</tr>';
			Inhalt = Inhalt + '			<tr>';
			Inhalt = Inhalt + '				<td align="right">Schattenbreite:</td>';
			Inhalt = Inhalt + '				<td align="right" width="20px"><span id="schattenbreite" class="info">0</span></td>';
			Inhalt = Inhalt + '				<td>';
			Inhalt = Inhalt + '					<input type="range" value="0" min="0" max="50" id="drawing-shadow-width">';
			Inhalt = Inhalt + '				</td>';
			Inhalt = Inhalt + '	    	</tr>';
			Inhalt = Inhalt + '			<tr>';
			Inhalt = Inhalt + '				<td align="right">Schattenversatz:</td>';
			Inhalt = Inhalt + '				<td align="right" width="20px"><span id="schattenversatz" class="info">0</span></td>';
			Inhalt = Inhalt + '				<td>';
			Inhalt = Inhalt + '					<input type="range" value="0" min="0" max="50" id="drawing-shadow-offset">';
			Inhalt = Inhalt + '				</td>';
			Inhalt = Inhalt + '   	 	</tr>';
			Inhalt = Inhalt + '		</table>';
			Inhalt = Inhalt + '	</div>';
			Inhalt = Inhalt + '</div>';
			jsPanel.create({
				dragit: {snap: true},
				headerControls: {size: 'xs'},
				id: 'Menue_Dialog',
				theme: 'info',
				contentSize: '300 220',
				content: Inhalt
			});
			drawingModeEl = $('drawing-mode'),
				drawingOptionsEl = $('drawing-mode-options'),
				drawingColorEl = $('drawing-color'),
				drawingShadowColorEl = $('drawing-shadow-color'),
				drawingLineWidthEl = $('drawing-line-width'),
				drawingShadowWidth = $('drawing-shadow-width'),
				drawingShadowOffset = $('drawing-shadow-offset')
			$('drawing-mode-selector').onchange = function() {
				if (this.value === 'hline') {
					canvas.freeDrawingBrush = vLinePatternBrush;
				} else if (this.value === 'vline') {
					canvas.freeDrawingBrush = hLinePatternBrush;
				} else if (this.value === 'square') {
					canvas.freeDrawingBrush = squarePatternBrush;
				} else if (this.value === 'diamond') {
					canvas.freeDrawingBrush = diamondPatternBrush;
				} else if (this.value === 'texture') {
					canvas.freeDrawingBrush = texturePatternBrush;
				} else {
					canvas.freeDrawingBrush = new fabric[this.value + 'Brush'](canvas);
				}
				if (canvas.freeDrawingBrush) {
					var brush = canvas.freeDrawingBrush;
					brush.color = drawingColorEl.value;
					if (brush.getPatternSrc) {
						brush.source = brush.getPatternSrc.call(brush);
					}
					brush.width = parseInt(drawingLineWidthEl.value, 10) || 3;
					brush.shadow = new fabric.Shadow({
						blur: parseInt(drawingShadowWidth.value, 10) || 0,
						offsetX: 0,
						offsetY: 0,
						affectStroke: true,
						color: drawingShadowColorEl.value,
					});
				}
			};	

			drawingColorEl.onchange = function() {
				var brush = canvas.freeDrawingBrush;
				brush.color = this.value;
				if (brush.getPatternSrc) {
					brush.source = brush.getPatternSrc.call(brush);
				}
			};
			drawingShadowColorEl.onchange = function() {
				canvas.freeDrawingBrush.shadow.color = this.value;
			};
			drawingLineWidthEl.onchange = function() {
				canvas.freeDrawingBrush.width = parseInt(this.value, 10) || 3;
				document.getElementById("linienbreite").innerHTML = this.value;
			};
			drawingShadowWidth.onchange = function() {
				canvas.freeDrawingBrush.shadow.blur = parseInt(this.value, 10) || 0;
				document.getElementById("schattenbreite").innerHTML = this.value;
			};
			drawingShadowOffset.onchange = function() {
				canvas.freeDrawingBrush.shadow.offsetX = parseInt(this.value, 10) || 0;
				canvas.freeDrawingBrush.shadow.offsetY = parseInt(this.value, 10) || 0;
				document.getElementById("schattenversatz").innerHTML = this.value;
			};
			if (canvas.freeDrawingBrush) {
				canvas.freeDrawingBrush.color = drawingColorEl.value;
				canvas.freeDrawingBrush.width = parseInt(drawingLineWidthEl.value, 10) || 3;
				canvas.freeDrawingBrush.shadow = new fabric.Shadow({
					blur: parseInt(drawingShadowWidth.value, 10) || 0,
					offsetX: 0,
					offsetY: 0,
					affectStroke: true,
					color: drawingShadowColorEl.value,
				});
			}
			drawingModeEl.value = 'markieren';
			document.getElementById("modus").value = "true";
			zeichnen = true;
		} else {
			drawingModeEl.value = 'zeichnen';
			document.getElementById("modus").value = "false";
			zeichnen = false;
			try {
				Menue_Dialog.close();
			} catch (err) {}
		}

	};

	if (document.getElementById("modus").value == "true") {
		zeichnen = true;
		document.getElementById("drawing-mode").value = 'markieren';
	} else {
		zeichnen = false;
		document.getElementById("drawing-mode").value = 'zeichnen';
	}
	
	var canvas = this.__canvas = new fabric.Canvas('c', {
		isDrawingMode: zeichnen
	});
	canvas.freeDrawingBrush.width = 3;
	fabric.Object.prototype.transparentCorners = false;
	
	vergr = $('vergr');
	clearEl = $('clear-canvas');
	speichern = $('speichern');
	loeschen = $('loeschen');
	lloeschen = $('lloeschen');
	nachhinten = $('nachhinten');
	weitervor = $('weitervor');
	nachvorne = $('nachvorne');
	weiterzurueck = $('weiterzurueck');
	rechteck = $('rechteck');
	textrahmen = $('textrahmen');
	liniew = $('liniew');
	linies = $('linies');
	dreieck = $('dreieck');
	kreis = $('kreis');
	rechts = $('rechts');
	links = $('links');
	rauf = $('rauf');
	runter = $('runter');
	startpos = $('startpos');
	eigenschaften = $('eigenschaften');
	ansicht = $('ansicht');
	
	clearEl.onclick = function() { canvas.clear() };

	import_svg.onclick = function () {
		jsPanel.create({
			dragit: {snap: true},
			headerControls: {size: 'xs'},
			id: 'SVG_Import_Dialog',
			theme: 'info',
			contentAjax: {url: './SVG_Import.html'},
			onbeforeclose: function() {
				SVG_einlesen();
				return true;
			}
		});
	};
	
	textrahmen.onclick = function() {
		var textobjekt = new fabric.Textbox('Text');
		textobjekt.set({fontFamily: 'Arial', fontSize: 30, stroke: "#000000", strokeWidth: 1});
		canvas.add(textobjekt);
		canvas.renderAll();
	}
	
	liniew.onclick = function() {
		var line = new fabric.Line();
		line.set({x1: 50, x2: 150, y1: 50, y2: 50, stroke: "#000000", strokeWidth: 3});
		canvas.add(line);
		canvas.renderAll();
	}
	
	linies.onclick = function() {
		var line = new fabric.Line();
		line.set({x1: 50, x2: 50, y1: 50, y2: 150, stroke: "#000000", strokeWidth: 3});
		canvas.add(line);
		canvas.renderAll();
	}
	
	dreieck.onclick = function() {
		var triangle = new fabric.Triangle();
  		triangle.set({width: 100, height: 100, fill: '#ffffff', stroke: "#000000", strokeWidth: 3});
		canvas.add(triangle);
		canvas.renderAll();
	}
		
	rechteck.onclick = function() {
		var rect = new fabric.Rect();
		rect.set({width: 100, height: 100, stroke: "#000000", strokeWidth: 3, fill: "#ffffff"});
		canvas.add(rect);
		canvas.renderAll();
	}
	
	kreis.onclick = function() {
		var circle = new fabric.Circle();
		circle.set({radius: 100, stroke: "#000000", strokeWidth: 3, fill: "#ffffff"});
		canvas.add(circle);
		canvas.renderAll();
	}
		
	nachhinten.onclick = function() {
		canvas._activeObject.sendToBack();
		canvas.renderAll();
	}
	
	nachvorne.onclick = function() {
		canvas._activeObject.bringToFront;
		canvas.renderAll();
	}

	weitervor.onclick = function() {
		canvas._activeObject.bringForward();
		canvas.renderAll();
	}
	
	weiterzurueck.onclick = function() {
		canvas._activeObject.sendBackwards();
		canvas.renderAll();
	}
	
	eigenschaften.onclick = function() {
		var Objekte = 1;
		try { Objekte = canvas.getActiveObject()._objects.length;} catch (err) {}
		var Inhalt = '<div style="position: absolute; top: 10px; left: 10px;"><table>';
		Inhalt = Inhalt + '<tr><td align="right">Linienbreite:</td><td width="10px"><td><input style="width: 30px;" type="text" value="' + canvas._activeObject.strokeWidth + '" min="1" max="15" id="d_linienbreite_man"></td><td><input type="range" value="' + canvas._activeObject.strokeWidth + '" min="1" max="15" id="d_linienbreite"></td></tr>';
		if (canvas._activeObject.type != "textbox") {
			Inhalt = Inhalt + '<tr><td align="right">Linienfarbe:</td><td width="10px"></td><td colspan="2"><input type="color" value="' + canvas._activeObject.stroke + '" id="d_linienfarbe"></td></tr>';
		}
		Inhalt = Inhalt + '<tr><td align="right">Transparenz:</td><td width="10px"><td><input style="width: 30px;" type="text" value="' + (100-parseFloat(canvas._activeObject.opacity)*100).toString() + '" min="0" max="100" id="transparenz_man">&nbsp;%</td><td><input type="range" value="' + (100-parseFloat(canvas._activeObject.opacity)*100).toString() + '" min="0" max="100" id="transparenz"></td></tr>';
		if (Objekte == 1) {
			Inhalt = Inhalt + '<tr><td align="right">oben:</td><td width="10px"></td><td colspan="2"><input style="width: 60px;" type="text" value="' + canvas._activeObject.top + '" id="d_oben"></td></tr>';
			Inhalt = Inhalt + '<tr><td align="right">links:</td><td width="10px"></td><td colspan="2"><input style="width: 60px;" type="text" value="' + canvas._activeObject.left + '" id="d_links"></td></tr>';
			Inhalt = Inhalt + '<tr><td align="right">Höhe:</td><td width="10px"></td><td colspan="2"><input style="width: 60px;" type="text" value="' + canvas._activeObject.height + '" id="d_hoehe"></td></tr>';
			Inhalt = Inhalt + '<tr><td align="right">Breite:</td><td width="10px"></td><td colspan="2"><input style="width: 60px;" type="text" value="' + canvas._activeObject.width + '" id="d_breite"></td></tr>';
		}
		if (canvas._activeObject.type == "rect" || canvas._activeObject.type == "circle" || canvas._activeObject.type == "triangle" || canvas._activeObject.type == "textbox") {
			Inhalt = Inhalt + '<tr><td align="right">Füllfarbe:</td><td width="10px"></td><td colspan="2"><input type="color" value="' + canvas._activeObject.stroke + '" id="fuellfarbe"></td></tr>';
		}
		if (canvas._activeObject.type == "textbox") {
			Inhalt = Inhalt + '<tr><td align="right">Schriftart:</td><td width="10px"></td><td colspan="2"><input type="text" value="' + canvas._activeObject.fontFamily + '" id="schriftart"></td></tr>';
			Inhalt = Inhalt + '<tr><td align="right">Schriftgröße:</td><td width="10px"></td><td colspan="2"><input style="width: 60px;" type="text" value="' + canvas._activeObject.fontSize + '" id="schriftgroesse"></td></tr>';
			Inhalt = Inhalt + '<tr><td align="right">Hintergrundfarbe:</td><td width="10px"></td><td colspan="2"><input type="color" value="' + canvas._activeObject.backgroundColor + '" id="hintergrundfarbe"></td></tr>';
		}
		Inhalt = Inhalt + '</table></div>';
		jsPanel.create({
			dragit: {snap: true},
			headerControls: {size: 'xs'},
			id: 'Eigenschaften_Dialog',
			theme: 'info',
			contentSize: '320 380',
			content: Inhalt
		});
		d_linienbreite_man = $('d_linienbreite_man');
		d_linienbreite = $('d_linienbreite');
		transparenz_man = $('transparenz_man');
		transparenz = $('transparenz');
		d_oben = $('d_oben');
		d_links = $('d_links');
		
		try {
			d_linienfarbe = $('d_linienfarbe');
			d_linienfarbe.onchange = function() {
				aendern("stroke", d_linienfarbe.value);
			}
		} catch (err) {}
		
		d_linienbreite_man.onchange = function() {
			aendern("strokeWidth", parseFloat(d_linienbreite_man.value));
			d_linienbreite.value = d_linienbreite_man.value;
		}

		d_linienbreite.onchange = function() {
			aendern("strokeWidth", parseFloat(d_linienbreite.value));
			d_linienbreite_man.value = d_linienbreite.value;
		}
		
		transparenz_man.onchange = function() {
			aendern("opacity", (1-parseFloat(transparenz_man.value)/100).toString());
			transparenz.value = transparenz_man.value;
		}
		
		transparenz.onchange = function() {
			aendern("opacity", (1-parseFloat(transparenz.value)/100).toString());
			transparenz_man.value = transparenz.value;
		}
		
		d_oben.onchange = function() {
			aendern("top", parseFloat(d_oben.value));
		}
		
		d_links.onchange = function() {
			caendern("left", parseFloat(d_links.value));
		}
		
		try {
			d_breite = $('d_breite');
			d_hoehe = $('d_hoehe');
			d_breite.onchange = function() {
				aendern("width", parseFloat(d_breite.value));
			}
		
			d_hoehe.onchange = function() {
				aendern("height", parseFloat(d_hoehe.value));
			}
		} catch (err) {}	
		
		try {
			fuellfarbe = $('fuellfarbe');
			fuellfarbe.onchange = function() {
				aendern("fill", fuellfarbe.value);
			}
		} catch (err) {}
		
		try {
			schriftart = $('schriftart');
			schriftgroesse = $('schriftgroesse');
			hintergrundfarbe = $('hintergrundfarbe');
			
			schriftart.onchange = function() {
				aendern("fontFamily", schriftart.value);
			}
			
			schriftgroesse.onchange = function() {
				aendern("fontSize", parseFloat(schriftgroesse.value));
			}
			
			hintergrundfarbe.onchange = function() {
				aendern("backgroundColor", hintergrundfarbe.value);
			}
		} catch (err) {}
		
		function aendern(Eigenschaft, Wert) {
			if (Objekte > 1) {
				for (i = 0; i < canvas.getActiveObject()._objects.length; i++) {
					canvas.getActiveObject()._objects[i].set(Eigenschaft, Wert);
				}
			} else {
				canvas._activeObject.set(Eigenschaft, Wert);
			}
			canvas.renderAll();
		}
	}

	function SVG_einlesen() {
		fabric.loadSVGFromString(document.getElementById('import_output').innerHTML, function(objects, options) {
			var obj = fabric.util.groupSVGElements(objects, options);
			canvas.add(obj).renderAll();
		});
		document.getElementById('import_output').innerHTML = "";
	}
	
	speichern.onclick = function() {
		document.getElementById("zeichnung").value = JSON.stringify(canvas);
		var maxrechts = 0;
		var maxunten = 0;
		var minrechts = 1000000;
		var minunten = 1000000;
		for (i = 0; i < canvas._objects.length; i++) {
			if (canvas._objects[i].width + canvas._objects[i].left > maxrechts) {maxrechts = canvas._objects[i].width + canvas._objects[i].left;}
			if (canvas._objects[i].height + canvas._objects[i].top > maxunten) {maxunten = canvas._objects[i].height + canvas._objects[i].top;}
			if (canvas._objects[i].left < minrechts) {minrechts = canvas._objects[i].left;}
			if (canvas._objects[i].top < minunten) {minunten = canvas._objects[i].top;}
		}
		Breite = canvas.width;
		Hoehe = canvas.height;
		var gesichert = canvas.toString();
		canvas.left = minrechts;
		canvas.top = minunten;
		canvas.width = maxrechts - minrechts;
		canvas.height = maxunten - minunten;
		for (i = 0; i < canvas._objects.length; i++) {
			canvas._objects[i].left = canvas._objects[i].left - minrechts;
			canvas._objects[i].top = canvas._objects[i].top - minunten;
		}
		canvas.requestRenderAll();
		document.getElementById("svg").value = canvas.toSVG();
		canvas.left = 0;
		canvas.top = 0;
		canvas.width = Breite;
		canvas.height = Hoehe;
		canvas.loadFromJSON(gesichert);
		canvas.requestRenderAll();
		document.forms["phpform"].Aktion.value = T_Text[2];
		document.forms["phpform"].submit();
	};
	
	einfuegen.onclick = function() {
		_clipboard.clone(function(clonedObj) {
			canvas.discardActiveObject();
			clonedObj.set({
				left: clonedObj.left + 10,
				top: clonedObj.top + 10,
				evented: true,
			});
			if (clonedObj.type === 'activeSelection') {
				clonedObj.canvas = canvas;
				clonedObj.forEachObject(function(obj) {
					canvas.add(obj);
				});
				clonedObj.setCoords();
			} else {
				canvas.add(clonedObj);
			}
			_clipboard.top += 10;
			_clipboard.left += 10;
			canvas.setActiveObject(clonedObj);
			canvas.requestRenderAll();
		});
	};

	loeschen.onclick = function() {
		try {
			for (i = 0; i < canvas.getActiveObject()._objects.length; i++) {
 				canvas.remove(canvas.getActiveObject()._objects[i])
		 	}
		 	canvas.remove(canvas.getActiveObject());
	 	} catch (err) {
			canvas.remove(canvas.getActiveObject());
		}
	};

	lloeschen.onclick = function() {
		canvas.remove(canvas._objects[canvas._objects.length - 1]);
	}  
   
	kopieren.onclick = function() {
		canvas.getActiveObject().clone(function(cloned) {
			_clipboard = cloned;
		});
	}
	
	rechts.onclick = function() {
		if ((canvas.viewportTransform[4] - 200) < (canvas.getZoom() - 1) * (canvas.width) * -1) {
			canvas.viewportTransform[4] = (canvas.getZoom() - 1) * (canvas.width) * -1;
		} else {
			canvas.viewportTransform[4] = canvas.viewportTransform[4] - 200;
		}
		canvas.renderAll();
	}
	
	links.onclick = function() {
		if (canvas.viewportTransform[4] + 200 > 0) {
			canvas.viewportTransform[4] = 0;
		} else {
			canvas.viewportTransform[4] = canvas.viewportTransform[4] + 200;
		}
		canvas.renderAll();
	}

	rauf.onclick = function() {
		if (canvas.viewportTransform[5] + 200 > 0) {
			canvas.viewportTransform[5] = 0;
		} else {
			canvas.viewportTransform[5] = canvas.viewportTransform[5] + 200;
		}
		canvas.renderAll();
	}
	
	runter.onclick = function() {
		if (canvas.viewportTransform[5] - 200 < canvas.height * (canvas.getZoom() - 1) * -1) {
			canvas.viewportTransform[5] = canvas.height * (canvas.getZoom() - 1) * -1;
		} else {
			canvas.viewportTransform[5] = canvas.viewportTransform[5] - 200;
		}
		canvas.renderAll();
	}
	
	startpos.onclick = function() {
		canvas.viewportTransform[4] = 0;
		canvas.viewportTransform[5] = 0;
		canvas.renderAll();
	}
	
	if (fabric.PatternBrush) {
		var vLinePatternBrush = new fabric.PatternBrush(canvas);
		vLinePatternBrush.getPatternSrc = function() {
			var patternCanvas = fabric.document.createElement('canvas');
			patternCanvas.width = patternCanvas.height = 10;
			var ctx = patternCanvas.getContext('2d');
			ctx.strokeStyle = this.color;
			ctx.lineWidth = 5;
			ctx.beginPath();
			ctx.moveTo(0, 5);
			ctx.lineTo(10, 5);
			ctx.closePath();
			ctx.stroke();
			return patternCanvas;
		};

		var hLinePatternBrush = new fabric.PatternBrush(canvas);
	
		hLinePatternBrush.getPatternSrc = function() {
			var patternCanvas = fabric.document.createElement('canvas');
			patternCanvas.width = patternCanvas.height = 10;
			var ctx = patternCanvas.getContext('2d');
			ctx.strokeStyle = this.color;
			ctx.lineWidth = 5;
			ctx.beginPath();
			ctx.moveTo(5, 0);
			ctx.lineTo(5, 10);
			ctx.closePath();
			ctx.stroke();
			return patternCanvas;
		};

		var squarePatternBrush = new fabric.PatternBrush(canvas);
		squarePatternBrush.getPatternSrc = function() {
			var squareWidth = 10, squareDistance = 2;
			var patternCanvas = fabric.document.createElement('canvas');
			patternCanvas.width = patternCanvas.height = squareWidth + squareDistance;
			var ctx = patternCanvas.getContext('2d');
			ctx.fillStyle = this.color;
			ctx.fillRect(0, 0, squareWidth, squareWidth);
			return patternCanvas;
		};
		var diamondPatternBrush = new fabric.PatternBrush(canvas);
		diamondPatternBrush.getPatternSrc = function() {
			var squareWidth = 10, squareDistance = 5;
			var patternCanvas = fabric.document.createElement('canvas');
			var rect = new fabric.Rect({
				width: squareWidth,
				height: squareWidth,
				angle: 45,
				fill: this.color
			});
			var canvasWidth = rect.getBoundingRect().width;
			patternCanvas.width = patternCanvas.height = canvasWidth + squareDistance;
			rect.set({ left: canvasWidth / 2, top: canvasWidth / 2 });
			var ctx = patternCanvas.getContext('2d');
			rect.render(ctx);
			return patternCanvas;
		};
		var img = new Image();
		img.src = './css/honey_im_subtle.png';
		var texturePatternBrush = new fabric.PatternBrush(canvas);
		texturePatternBrush.source = img;
	}
	
	canvas.loadFromJSON(document.getElementById("zeichnung").value);
	
	canvas.on('mouse:down', function(opt) {
		var evt = opt.e;
		if (evt.ctrlKey === true) {
			this.isDragging = true;
			this.selection = false;
			this.lastPosX = evt.clientX;
			this.lastPosY = evt.clientY;
		}
	});

	canvas.on('mouse:move', function(opt) {
		if (this.isDragging) {
			var e = opt.e;
			var vpt = this.viewportTransform;
			vpt[4] += e.clientX - this.lastPosX;
			vpt[5] += e.clientY - this.lastPosY;
			this.requestRenderAll();
			this.lastPosX = e.clientX;
			this.lastPosY = e.clientY;
		}
	});

	canvas.on('mouse:up', function(opt) {
		// on mouse up we want to recalculate new interaction
		// for all objects, so we call setViewportTransform
		this.setViewportTransform(this.viewportTransform);
		this.isDragging = false;
		this.selection = true;
	});
	
	vergr.onchange = function(opt) {
		Groesse = parseInt(vergr.value * 10) / 10;
		document.getElementById("vergrAnz").innerHTML = Groesse;
		canvas.zoomToPoint({ x: 0, y: 0 }, Groesse);
	};
	
	canvas.zoomToPoint({ x: 0, y: 0 }, 1);
		
	ansicht.onclick = function() {
		try {
			var Breite = window.frameElement.clientWidth - 20;
			var Hoehe = window.frameElement.clientHeight - 50;
		} catch (err) {
			var Breite = window.innerWidth - 20;
			var Hoehe = window.innerHeight - 50;
		}
		if (ansicht.value == "volle Höhe") {
			Breite = Hoehe/297*210;
			ansicht.value = "volle Breite";
			var Faktor = Breite/canvas.width;
		} else {
			Hoehe = Breite/210*297;		
			ansicht.value = "volle Höhe";
			var Faktor = Hoehe/canvas.height;
		}
		canvas.zoomToPoint({ x: 0, y: 0 }, Faktor);
		vergr.value = Faktor;
		document.getElementById("vergrAnz").innerHTML = parseInt(Faktor*100)/100;
	}
})();   