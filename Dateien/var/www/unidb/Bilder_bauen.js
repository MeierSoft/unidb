jsPanel.defaults.resizeit.minWidth = 16;
jsPanel.defaults.resizeit.minHeight = 16;
var markierte_elem = [];
var DH_Elemente=[];
var T_Text = new Array;
var Bedingungen = {};
var Instr2 = [];
var Elternelement = document.getElementById('DH_Bereich');
$(window).on('load',function() {;
	initDraw(document.getElementById('DH_Bereich'));
	T_Text = JSON.parse(document.getElementById("translation").value);
	try {Bedingungen = JSON.parse(document.getElementById("bed_Format").value);
		i = 1;
		while(Bedingungen[i] != undefined) {
			Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\µµµ/g,"'");
			i = i + 1;
		}	
	} catch (err) {}
	einrichten();
	lesen();
	if (document.getElementById('hintergrundbild').value > "") {document.body.style.background = "url('" + document.getElementById('hintergrundbild').value + "') no-repeat top 60px left 0px";}
	var refreshId = setInterval(function() {lesen();}, 60000);
	Elternelement = document.getElementById('DH_Bereich');
});

//setup event handler function
var handler = function (event) {
document.getElementById("Element_Dialog").top.value = document.getElementById("ausgewaehlt").style.top;
document.getElementById("Element_Dialog").left.value = document.getElementById("ausgewaehlt").style.left;
}
//assign handler to event
document.addEventListener('jspaneldragstop', handler, false);

document.addEventListener('jspanelresizestop', function (event) {
	if (event.detail === 'ausgewaehlt') {
		Groesse_anpassen();
	}
});
	
$(function(){
	T_Text = JSON.parse(document.getElementById("translation").value);
	$.contextMenu({
		selector: '.context-menu-one', 
		callback: function(key, options) {
			if (markierte_elem.length > 0) {
				ausrichten(key);
			}
		},
		items: {
			"gruppieren": {"name": T_Text[73], "icon": "edit"},
			"fold1": {
				"name": T_Text[14], 
				"items": {
					"oben": {"name": T_Text[15]},
					"unten": {"name": T_Text[16]},
					"rechts": {"name": T_Text[17]},
					"links": {"name": T_Text[18]}
				}
			},
			"fold2": {
				"name": T_Text[26], 
				"items": {
					"breitesten": {"name": T_Text[19]},
					"schmalsten": {"name": T_Text[20]},
					"höchsten": {"name": T_Text[21]},
					"niedrigsten": {"name": T_Text[22]}
					}
			},
		}
	});
	$.contextMenu({
		selector: '.context-menu-two', 
		callback: function(key, options) {
			if (key == "Eigenschaften") {Element_Dialog_oeffnen();}
			if (key == "Gruppierung aufheben") {Gruppierung_aufheben();}
			if (key == "entfernen") {Element_entfernen();}
		},
		items: {
			"Gruppierung aufheben": {"name": T_Text[23], "icon": "quit"},
			"Eigenschaften": {"name": T_Text[24], "icon": "edit"},
			"sep1": "---------",
			"entfernen": {"name": T_Text[25], "icon": "delete"}
		}
	});
});

function Gruppierung_aufheben() {
	Auswahl = Element_aus_Fenster();
	if (Auswahl.id.substr(0,6) == "Gruppe") {
		rlinks = Auswahl.parentElement.parentElement.offsetLeft;
		roben = Auswahl.parentElement.parentElement.offsetTop;
		while (Auswahl.childNodes.length > 0) {
			Auswahl.childNodes[0].style.top = (parseInt(Auswahl.childNodes[0].style.top) + roben) + "px";
			Auswahl.childNodes[0].style.left = (parseInt(Auswahl.childNodes[0].style.left) + rlinks) + "px";
			Auswahl.childNodes[0].className = "";
			Auswahl.childNodes[0].style.border = "";
			if (document.phpform.mobil.value=="1") {
				Auswahl.childNodes[0].setAttribute('ontouchend', 'auswaehlen(this);');
			} else {
				Auswahl.childNodes[0].setAttribute('onclick', 'auswaehlen(this);');
			}
			for (x = 0; x < Auswahl.childNodes[0].childNodes.length; x++) {
				try {
					if (Auswahl.childNodes[0].childNodes[x].id.substr(0,6) == "Deckel") {	
						if (document.phpform.mobil.value=="1") {
							Auswahl.childNodes[0].childNodes[x].setAttribute('ontouchend', 'auswaehlen(this);');
						} else {
							Auswahl.childNodes[0].childNodes[x].setAttribute('onclick', 'auswaehlen(this);');
						}
					}
				} catch (err) {}
			}
			document.getElementById("DH_Bereich").appendChild(Auswahl.childNodes[0]);
		}
		Element_entfernen();
	}
}

function ausrichten(key) {
	if (key == "gruppieren") {
		//Die Grenzen ausloten
		var rlinks = 1000000;
		var rrechts = -1000000;
		var roben = 1000000;
		var runten = -1000000;
		var rvertikal = -1000000;
		for (i = 0; i < markierte_elem.length; i++) {
			if (parseInt(document.getElementById(markierte_elem[i]).style.zIndex) > rvertikal){rvertikal = parseInt(document.getElementById(markierte_elem[i]).style.zIndex);}
			if (parseInt(document.getElementById(markierte_elem[i]).style.top) < roben){roben = parseInt(document.getElementById(markierte_elem[i]).style.top);}
			if (parseInt(document.getElementById(markierte_elem[i]).style.left) < rlinks){rlinks = parseInt(document.getElementById(markierte_elem[i]).style.left);}
			if (parseInt(document.getElementById(markierte_elem[i]).style.top) + parseInt(document.getElementById(markierte_elem[i]).style.height) > runten){runten = parseInt(document.getElementById(markierte_elem[i]).style.top) + parseInt(document.getElementById(markierte_elem[i]).style.height);}
			if (parseInt(document.getElementById(markierte_elem[i]).style.left) + parseInt(document.getElementById(markierte_elem[i]).style.width) > rrechts){rrechts = parseInt(document.getElementById(markierte_elem[i]).style.left) + parseInt(document.getElementById(markierte_elem[i]).style.width);}
		}
		//neues DIV erzeugen und die ausgewählten DIVs unterordnen
		newdiv = document.createElement('div');
		var jetzt = new Date();
		var id = jetzt.getTime();
		newdiv.setAttribute('id',"Gruppe" + id);
		newdiv.style.left = rlinks + 'px';
		newdiv.style.top = roben + 'px';
		newdiv.style.width = (rrechts - rlinks) + 'px';
		newdiv.style.height = (runten - roben) + 'px';
		newdiv.style.zIndex = rvertikal + 10;
		newdiv.style.position="absolute";
		if (document.phpform.mobil.value=="1") {
			newdiv.setAttribute('ontouchend', 'auswaehlen(this);');
		} else {
			newdiv.setAttribute('onclick', 'auswaehlen(this);');
		}
		document.getElementById("DH_Bereich").appendChild(newdiv);
		for (i = 0; i < markierte_elem.length; i++) {
			Element = document.getElementById(markierte_elem[i]);
			newdiv.appendChild(Element);
			Element.style.top = (parseInt(Element.style.top) - roben) + "px";
			Element.style.left = (parseInt(Element.style.left) - rlinks) + "px";
			Element.removeAttribute("ontouchend");
			Element.removeAttribute("onclick");
			Element.className = 'context-menu-two';
			for (x = 0; x < Element.childNodes.length; x++) {
				try {
					Element.childNodes[x].removeAttribute("ontouchend");
					Element.childNodes[x].removeAttribute("onclick");
				} catch (err) {}
			}
		}
		return 0;
	}
	if (key == "oben" || key == "links" || key == "schmalsten" || key == "niedrigsten") {
		var minmax = 1000000;
	} else {
		var minmax = -1000000;
	}
	for (i = 0; i < markierte_elem.length; i++) {
		if (key == "breitesten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.width) > minmax) {
				minmax = parseInt(document.getElementById(markierte_elem[i]).style.width);
			}
		}
		if (key == "schmalsten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.width) < minmax) {
				minmax = parseInt(document.getElementById(markierte_elem[i]).style.width);
			}
		}
		if (key == "höchsten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.height) > minmax) {
				minmax = parseInt(document.getElementById(markierte_elem[i]).style.height);
			}
		}
		if (key == "niedrigsten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.height) < minmax) {
				minmax = parseInt(document.getElementById(markierte_elem[i]).style.height);
			}
		}
		if (key == "oben") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.top) < minmax) {
				minmax = parseInt(document.getElementById(markierte_elem[i]).style.top);
			}
		}
		if (key == "unten") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.top) + parseInt(document.getElementById(markierte_elem[i]).style.height) > minmax) {
				minmax = parseInt(document.getElementById(markierte_elem[i]).style.top) + parseInt(document.getElementById(markierte_elem[i]).style.height);
			}
		}
		if (key == "links") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.left) < minmax) {
				minmax = parseInt(document.getElementById(markierte_elem[i]).style.left);
			}
		}
		if (key == "rechts") {
			if (parseInt(document.getElementById(markierte_elem[i]).style.left) + parseInt(document.getElementById(markierte_elem[i]).style.width) > minmax) {
				minmax = parseInt(document.getElementById(markierte_elem[i]).style.left) + parseInt(document.getElementById(markierte_elem[i]).style.width);
			}
		}
	}
	for (i = 0; i < markierte_elem.length; i++) {
		if (key == "breitesten") {
				document.getElementById(markierte_elem[i]).style.width = minmax.toString() + "px";
		}
		if (key == "schmalsten") {
				document.getElementById(markierte_elem[i]).style.width = minmax.toString() + "px";
		}
		if (key == "höchsten") {
				document.getElementById(markierte_elem[i]).style.height = minmax.toString() + "px";
		}
		if (key == "niedrigsten") {
				document.getElementById(markierte_elem[i]).style.height = minmax.toString() + "px";
		}
		if (key == "oben") {
				document.getElementById(markierte_elem[i]).style.top = minmax.toString() + "px";
		}
		if (key == "unten") {
			document.getElementById(markierte_elem[i]).style.top = (minmax - parseInt(document.getElementById(markierte_elem[i]).style.height)).toString() + "px";
		}
		if (key == "links") {
			document.getElementById(markierte_elem[i]).style.left = minmax.toString() + "px";
		}
		if (key == "rechts") {
			document.getElementById(markierte_elem[i]).style.left = (minmax - parseInt(document.getElementById(markierte_elem[i]).style.width)).toString() + "px";
		}
	}
}

function initDraw(DH_Bereich) {
	function setMousePosition(e) {
		var ev = e || window.event; //Moz || IE
		if (ev.pageX) { //Moz
			mouse.x = ev.pageX + window.pageXOffset;
			mouse.y = ev.pageY + window.pageYOffset;
		} else if (ev.clientX) { //IE
			mouse.x = ev.clientX + document.body.scrollLeft;
			mouse.y = ev.clientY + document.body.scrollTop;
		}
	};
	var mouse = {
		x: 0,
		y: 0,
		startX: 0,
		startY: 0
	};
	var element = null;
	DH_Bereich.onmousemove = function (e) {
		setMousePosition(e);
		if (element !== null) {
			element.style.width = Math.abs(mouse.x - mouse.startX) + 'px';
			element.style.height = Math.abs(mouse.y - mouse.startY) + 'px';
			element.style.left = (mouse.x - mouse.startX < 0) ? mouse.x + 'px' : mouse.startX + 'px';
			element.style.top = (mouse.y - mouse.startY < 0) ? mouse.y + 'px' : mouse.startY + 'px';
		}
	}
	DH_Bereich.onmouseup = function (e) {
		if (element !== null) {
			markierte_elem = [];
			for (i=0; i < DH_Bereich.childNodes.length; i++) {
				try {
					if (parseInt(DH_Bereich.childNodes[i].style.top) > parseInt(element.style.top)) {
						if (parseInt(DH_Bereich.childNodes[i].style.top) + parseInt(DH_Bereich.childNodes[i].style.height) < parseInt(element.style.top) + parseInt(element.style.height)) {
							if (parseInt(DH_Bereich.childNodes[i].style.left) > parseInt(element.style.left)) {
								if (parseInt(DH_Bereich.childNodes[i].style.left) + parseInt(DH_Bereich.childNodes[i].style.width) < parseInt(element.style.left) + parseInt(element.style.width)) {
									DH_Bereich.childNodes[i].style.border = "1px dotted";
									DH_Bereich.childNodes[i].setAttribute("rahmen_orig",DH_Bereich.childNodes[i].style.border);
									DH_Bereich.childNodes[i].className = 'context-menu-one';
									markierte_elem.push(DH_Bereich.childNodes[i].id);
								}
							}
						}
					}
				} catch (err) {}
			}
			DH_Bereich.removeChild(element);
			element = null;
			DH_Bereich.style.cursor = "default";
		} else {
			Auswahl_beenden();
			mouse.startX = mouse.x;
			mouse.startY = mouse.y;
			element = document.createElement('div');
			element.className = 'rectangle'
			element.style.left = mouse.x + 'px';
			element.style.top = mouse.y + 'px';
			DH_Bereich.appendChild(element)
			DH_Bereich.style.cursor = "crosshair";
		}
	}
}

function abspeichern() {
	try {Auswahl_beenden();} catch (err) {}
	for (i = 0; i < DH_Elemente.length; i++) {
		if (DH_Elemente[i].Typ == "Instrument") {
			try {document.getElementById(DH_Elemente[i].id).removeChild(document.getElementById("Deckel" + DH_Elemente[i].id));} catch (err) {}
		}
	}
	//Gruppierungen entfernen
	for (i = 0; i < DH_Bereich.childNodes.length; i++) {
		try {
			if (DH_Bereich.childNodes[i].id.substr(0,6) == "Gruppe") {	
				Auswahl = DH_Bereich.childNodes[i];
				rlinks = parseInt(Auswahl.style.left);
				roben = parseInt(Auswahl.style.top);
				while (Auswahl.childNodes.length > 0) {
					Auswahl.childNodes[0].style.top = (parseInt(Auswahl.childNodes[0].style.top) + roben) + "px";
					Auswahl.childNodes[0].style.left = (parseInt(Auswahl.childNodes[0].style.left) + rlinks) + "px";
					Auswahl.childNodes[0].className = '';
					Auswahl.childNodes[0].style.border = "";
					if (document.phpform.mobil.value=="1") {
						Auswahl.childNodes[0].setAttribute('ontouchend', 'auswaehlen(this);');
					} else {
						Auswahl.childNodes[0].setAttribute('onclick', 'auswaehlen(this);');
					}
					for (x = 0; x < Auswahl.childNodes[0].childNodes.length; x++) {
						try {
							if (Auswahl.childNodes[0].childNodes[x].id.substr(0,6) == "Deckel") {	
								if (document.phpform.mobil.value=="1") {
									Auswahl.childNodes[0].childNodes[x].setAttribute('ontouchend', 'auswaehlen(this);');
								} else {
									Auswahl.childNodes[0].childNodes[x].setAttribute('onclick', 'auswaehlen(this);');
								}
							}
						} catch (err) {}
					}
					document.getElementById("DH_Bereich").appendChild(Auswahl.childNodes[0]);
				}
				DH_Bereich.removeChild(Auswahl);
			}
		} catch (err) {}
	}
	//Alle Gruppen sind weg
	//Jetzt nur die DIVs mit ID direkt unterhalb des DIVs DH_Bereich in die Variable Text schreiben
	var neuDIV = document.createElement('div');
	neuDIV.style.display="none";
	var tempDIV = "";
	for (i = 1; i < DH_Bereich.childNodes.length; i++) {
		if (DH_Bereich.childNodes[i].id.length > 0) {
			tempDIV = DH_Bereich.childNodes[i].cloneNode();
			tempDIV.innerHTML = DH_Bereich.childNodes[i].innerHTML;
			neuDIV.appendChild(tempDIV);
		}
	}
	document.phpform.Inhalt.value = neuDIV.innerHTML;
	try {document.removeChild(neuDIV);} catch (err) {}
}

function Tagname_setzen(Feld){
	//undefined entfernen
	try {
		if (document.Element_Dialog.Ausdruck.value=="undefined") {
			document.Element_Dialog.Ausdruck.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.Pfad.value=="undefined") {
			document.Element_Dialog.Pfad.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.Point_ID.value=="undefined") {
			document.Element_Dialog.Point_ID.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.Tagname.value=="undefined") {
			document.Element_Dialog.Tagname.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.Tag_ID.value=="undefined") {
			document.Element_Dialog.Tag_ID.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.Pointname.value=="undefined") {
			document.Element_Dialog.Pointname.value = "";
		}
	} catch (err) {}
	//Wenn wir es mit einem Ausdruck zu tun haben, dann die anderen Felder leeren
	try {	
		if (Feld == "Ausdruck" && document.Element_Dialog.Ausdruck.value > "") {
			document.Element_Dialog.Point_ID.value = "";
			document.Element_Dialog.Tagname.value = "";
			document.Element_Dialog.Tag_ID.value = "";
			document.Element_Dialog.Pfad.value = "";
		}
	} catch (err) {}
	if ((Feld == "Tag_ID" && document.Element_Dialog.Tag_ID.value > "") || (Feld == "Point_ID" && document.Element_Dialog.Point_ID.value > "")) {
		document.Element_Dialog.Tagname.value = "";
		document.Element_Dialog.Pfad.value = "";
		document.Element_Dialog.Ausdruck.value = "";
	}
	//Wenn es sich um einen Tag handelt, dann das Feld Tag_ID fuellen und das Feld Ausdruck leer machen
	try {
		if (Feld == "Tagname" && document.Element_Dialog.Tagname.value > "") {
			//Sollte sich in dem Feld ein % Zeichen befinden, dann wird ein Tag gesucht. In diesem Fall die Aktion hier abbrechen.
			//Ist das Feld Pfad leer, dann ebenfalls hier abbrechen.
			if (document.Element_Dialog.Tagname.value.indexOf("%") > -1 || document.Element_Dialog.Pfad.value == "") {return 0;}
			if (document.Element_Dialog.Pfad.value.substr(document.Element_Dialog.Pfad.value.length - 1,1) != "/" && document.Element_Dialog.Pfad.value.length > 1) {document.Element_Dialog.Pfad.value = document.Element_Dialog.Pfad.value + "/";}
			jQuery.ajax({
				url: "./DH_Tag_Tag_ID.php?Tagname=" + document.Element_Dialog.Pfad.value + document.Element_Dialog.Tagname.value,
				success: function (html) {
					if (html > "") {
	   				document.Element_Dialog.Tag_ID.value = html;
	   				try {document.Element_Dialog.Ausdruck.value = "";} catch (err) {}
	   			}
				},
   			async: false
   		});			
		} else {
		//Wenn Tag_ID belegt ist, dann die Felder Pfad und Tagname fuellen und Ausdruck leeren
			if (Feld == "Tag_ID" && document.Element_Dialog.Tag_ID.value > "") {
				jQuery.ajax({
					url: "./DH_Tag_Tag_ID.php?Tag_ID=" + document.Element_Dialog.Tag_ID.value,
					success: function (html) {
						Ergebnis = html.split(",");
	   				document.Element_Dialog.Pfad.value = Ergebnis[0];						
	   				document.Element_Dialog.Tagname.value = Ergebnis[1];
						try {document.Element_Dialog.Ausdruck.value = "";} catch (err) {}
					},
   				async: false
   			});			
			}
		}
		jQuery.ajax({
			url: "./DH_Tag_Point_ID.php?Tag_ID="+document.Element_Dialog.Tag_ID.value,
			success: function (html) {
  				document.Element_Dialog.Point_ID.value = html;
			},
			async: false
		});
	} catch (err) {}
	try {
		if (document.Element_Dialog.Tag_ID.value == "") {
			jQuery.ajax({
				url: "./DH_Point_ID_Tag_ID.php?Point_ID="+document.Element_Dialog.Point_ID.value,
				success: function (html) {
  					document.Element_Dialog.Tag_ID.value = html;
				},
				async: false
			});
			jQuery.ajax({
				url: "./DH_Tag_Tag_ID.php?Tag_ID="+document.Element_Dialog.Tag_ID.value,
				success: function (html) {
  					if(html > ""&& html != ","){document.Element_Dialog.Tagname.value = html;}
				},
				async: false
			});
		}	
	} catch (err) {}
	einheit_setzen();
}

function einheit_setzen(){
	jQuery.ajax({
		url: "./DH_Eigenschaft_Point_ID.php?Eigenschaft=EUDESC&Tag_ID="+document.Element_Dialog.Tag_ID.value,
		success: function (html) {
   		document.Element_Dialog.Einheit.value = html;
		},
  		async: false
  	});
}

function auswaehlen(){
	var Auswahl = Element_aus_Fenster();
	if (Auswahl != 1) {
		try {Auswahl_beenden();} catch (err) {}
	}
	// Feld neu fuellen
	var id = arguments[0].attributes['id'].value;
	if (id.substring(0, 6) == "Deckel") {
		id = id.substring(6,);
	}
	var Auswahl=document.getElementById(id);
	Auswahl.className = 'context-menu-two';
	try {
		var tempoben = Auswahl.style.top;
		tempoben = tempoben.replace("px","")
		var templinks = Auswahl.style.left;
		templinks = templinks.replace("px","")
		Auswahl.style.removeProperty("position");
		Auswahl.style.removeProperty("left");
		Auswahl.style.removeProperty("top");
		jsPanel.create({
			id: 'ausgewaehlt',
			header: false,
			footerToolbar: '<span id="btn-close"><b>X</b></span>',
			position: "left-top " + templinks + " " + tempoben,
			content: Auswahl,
   		contentSize: Auswahl.style.width + " " + Auswahl.style.height,
   		contentOverflow: 'hidden',
	   	theme: 'none',
   		dragit: {handles: '.jsPanel-ftr', grid: [parseFloat(document.phpform.Raster.value)]},
   		callback: function (panel) {
   			panel.footer.style.background = '#0398E2';
				jsPanel.pointerup.forEach(function (item) {
					panel.footer.querySelector('#btn-close').addEventListener(item, function () {
						Auswahl_beenden();
         	   });
	        });
   		}
		});
	} catch (err) {}
	//einrichten();
	lesen();
}

function Elementtyp_aussuchen(){
	try {Auswahl_beenden();} catch (err) {}
	var Inhalt = "<div style='position: absolute; top: 10px; left: 100px;'>" + T_Text[65] + ":<br><br><select class='Auswahl_Liste_Element' id='Typauswahlliste' name='Typauswahlliste' size='10'>";
	var Liste = document.getElementById("Typauswahl").value.split(";");
	for (i=0; i < Liste.length; i++) {
		Liste_Name = Liste[i].split(",");
		Inhalt=Inhalt + "<option>" + Liste_Name[0] + "</option>";
	}
	Inhalt=Inhalt + "</select><br><br><input name='schliessen' value='" + T_Text[66] + "' type='button' onclick='Element_bauen();' style='width: 75px;border-left: 0px;'></div>";
	//e.preventDefault();
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'neues_Element',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '340 340',
		headerTitle: T_Text[67],
		position: 'left-top 300 30',
		content: Inhalt
	});
}

function Baustein_aussuchen(){
	//Falls noch ein Element ausgewaehlt ist, dann dieses zuerst schliessen
	var Auswahl = Element_aus_Fenster();
	if (Auswahl != 1) {
		try {Auswahl_beenden();} catch (err) {}
	}
	//Jetzt kommt erst die Auswahl des Bausteins
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px; width: 200px'>" + T_Text[68] + ":<br><br><select id='Bausteinauswahlliste' name='Bausteinauswahlliste' size='10' onchange = 'Baustein_laden();'>";
	var Liste = document.getElementById("Bausteinauswahl").value.split(";");
	for (i=0; i < Liste.length; i++) {
		if (Liste[i].length > 0) {
			Eintrag = Liste[i].split(",");
			Inhalt = Inhalt + "<option value = '" + Eintrag[0] + "'>" + Eintrag[1] + "</option>";
		}
	}
	Inhalt=Inhalt + "</select><div id='Bausteinvorschau' style='position: absolute; top: 10px; left: 220px; width: 400px'></div><br><br><input name='schliessen' value='" + T_Text[66] + "' type='button' onclick='Baustein_einfuegen();' style='width: 75px;border-left: 0px;'></div>";
	//e.preventDefault();
	jsPanel.create({
		dragit: {
        	snap: true
		},
		headerControls: {
			size: 'xs'
		},
		id: 'Baustein_aussuchen',
		theme: 'info',
		contentSize: '620 340',
		headerTitle: T_Text[69],
		content: Inhalt
	});
}

function Element_bauen(ElementTyp){
	var Sprache = document.getElementById("sprache").value;
	ElementTyp = document.getElementById('Typauswahlliste').value;
	ElementTyp = DBQ("unidb","","DE","Elementvorlagen","`" + Sprache + "` = '" + ElementTyp + "'");
	var Auswahl = Element_aus_Fenster();
	if (Auswahl != 1) {
		var Elemente_ID = Auswahl.attributes["elemente_id"].value;
		var Eltern_ID = Auswahl.id;
	} else {
		var Elemente_ID = 0;
		var Eltern_ID = "DH_Bereich";
	}
	var Eigenschaften;
	try {
		document.getElementById('neues_Element').close();
	} catch (err) {}
  	jQuery.ajax({
		url: "./Elementvorlage_einlesen.php?alle=0&Bezeichnung=" + ElementTyp,
		success: function (html) {
	   	Eigenschaften = html;
		},
   	async: false
   });
   Eigenschaften = Eigenschaften.split("@@@");
	if (Eigenschaften != undefined && Eigenschaften != null) {
		Eigenschaften2 = JSON.parse(Eigenschaften[0]);
		var jetzt = new Date();
		Bez = Eigenschaften2.Vorlage + jetzt.getTime().toString();
		if (Eigenschaften2.Vorlage == "berechnet" || Eigenschaften2.Vorlage == "Feld_anzeigen" || Eigenschaften2.Vorlage == "Optionsgruppe" || Eigenschaften2.Vorlage == "Textarea" || Eigenschaften2.Vorlage == "Text" || Eigenschaften2.Vorlage == "Instrument 2" || Eigenschaften2.Vorlage == "Sparkline" || Eigenschaften2.Vorlage == "Instrument" || Eigenschaften2.Vorlage == "Rechteck" || Eigenschaften2.Vorlage == "Tabelle" || Eigenschaften2.Vorlage == "vert_Balken" || Eigenschaften2.Vorlage == "Multistate" || Eigenschaften2.Vorlage == "Trend" || Eigenschaften2.Vorlage == "Tag" || Eigenschaften2.Vorlage == "Link") {
			var newdiv = document.createElement('div');
			newdiv.innerHTML = Eigenschaften2.Vorlage;
		} else {
			if (Eigenschaften2.Vorlage == "Grafik") {
				var newdiv = document.createElement('img');
			} else {
				if (Eigenschaften2.Vorlage == "Listenfeld" || Eigenschaften2.Vorlage == "Kombinationsfeld") {
					var newdiv = document.createElement('select');
				} else {
					var newdiv = document.createElement('input');
					newdiv.setAttribute('value', "");
					if (Eigenschaften2.Vorlage == "Schalter") {newdiv.setAttribute('type', "button");}
					if (Eigenschaften2.Vorlage == "Textfeld") {newdiv.setAttribute('type', "Text");}
					if (Eigenschaften2.Vorlage == "Checkbox") {newdiv.setAttribute('type', "checkbox");}
				}
			}
		}
		newdiv.setAttribute('id',Bez);
		newdiv.setAttribute('name',Bez);
		newdiv.setAttribute('neu','1');
		newdiv.style.backgroundColor = "#FF0000";
		if (ElementTyp == "Option") {newdiv.setAttribute('type','radio');}
		if (ElementTyp=="Instrument") {newdiv.innerHTML="<object data='Instrument.svg' type='image/svg+xml'></object>";}
		Stil = "position : absolute; ";
		for (x=0; x < Eigenschaften.length; x++) {
			Eigenschaften2 = JSON.parse(Eigenschaften[x]);
			if (Eigenschaften2.Attributtyp == "style") {
				Stil += Eigenschaften2.orig_Name + ":" + Eigenschaften2.Standardwert + "; ";
			} else {
				if (Eigenschaften2.Eigenschaft == "feldtyp" || Eigenschaften2.Eigenschaft == "Feldtyp") {
					newdiv.setAttribute(Eigenschaften2.orig_Name, Eigenschaften2.Vorlage) + "; ";
					Eigenschaften2.Wert = Eigenschaften2.Vorlage;
				} else {
					if (Eigenschaften2.Eigenschaft != "disabled") {
						newdiv.setAttribute(Eigenschaften2.orig_Name, Eigenschaften2.Standardwert) + "; ";
						Eigenschaften2.Wert = Eigenschaften2.Standardwert;
					}
				}
			}
		}
		newdiv.setAttribute("style", Stil);
		newdiv.setAttribute('Bezeichnung', Bez);
		if (document.phpform.mobil.value == "1") {
			newdiv.setAttribute('ontouchend', 'auswaehlen(this);');
		} else {
			newdiv.setAttribute('onclick', 'auswaehlen(this);');
		}
		if (ElementTyp == "Textarea") {newdiv.setAttribute('onclick1','Text_bearbeiten("' + Bez + '");');}
		//neues Element markieren
		newdiv.style.zIndex = 1000;
		newdiv.setAttribute('neu','1');
		newdiv.style.backgroundColor = "#FF0000";
		document.getElementById(Eltern_ID).appendChild(newdiv);
	}
	if (ElementTyp == "Instrument 2") {
		newdiv.className = "gauge-container two element context-menu-two";
		Instr2[newdiv.id] = Gauge(
			document.getElementById(newdiv.id), {
  				dialStartAngle: 180,
   	      dialEndAngle: 0,
        		value: 0,
		     	viewBox: "0 0 100 57",
        		color: function(value) {return "#5ee432";}
			}
		);
	}
}

function Pfade_Dialog_oeffnen(){
	Tagliste = "";
	for (i=0; i < DH_Bereich.childNodes.length; i++) {
		Tag_ID = 0;
		try {Tag_ID = DH_Bereich.childNodes[i].attributes["tag_id"].value;} catch (err) {}
		if (Tag_ID > "") {
			if (Tagliste == "") {
				Tagliste = Tagliste + Tag_ID;
			} else {			
				Tagliste = Tagliste + "," + Tag_ID;
			}
		}
	}

	jQuery.ajax({
		url: "DH_Tagliste_ermitteln.php?Tagliste=" + Tagliste + "&Pfad=" + document.Bild_Dialog.Tags_Pfad.value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	
	jsPanel.create({
		id: 'PfadeDialog',
		headerControls: {
			size: 'xs'
		},
		theme: 'info',
		contentSize: '600 300',
		content: strReturn,
	});
}

function Pfadaenderung_uebernehmen() {
	i = 0;
	Feld = document.Dialog_Tagaustausch[0];
	try{	
		while (Feld.type != "button"){
			if (Feld.checked == true) {
				Tag_alt_neu = Feld.value.split(",");
				for (k=0; k < DH_Bereich.childNodes.length; k++) {
					Tag_ID = 0;
					try {Tag_ID = DH_Bereich.childNodes[k].attributes["tag_id"].value;} catch (err) {}
					if (Tag_ID == Tag_alt_neu[0]) {
						DH_Bereich.childNodes[k].attributes["tag_id"].value = Tag_alt_neu[1];
						DH_Bereich.childNodes[k].attributes["tagname"].value = Tag_alt_neu[2];
					}
				}
			}
			i = i + 1;
			Feld = document.Dialog_Tagaustausch[i];
		}
	} catch (err) {}
	try {PfadeDialog.close();} catch (err) {}
}

function Eingabefeld_sichtbar_dialog() {
	jQuery.ajax({
		url: "Test_Tag_suchen.php?Suchtext=" + document.Element_Dialog.Tagname.value,
		success: function (html) {
   		strReturn = html;
		},
  		async: false
  	});
	
	jsPanel.create({
		id: 'Tagsuche',
		headerControls: {
			size: 'xs'
		},
		theme: 'info',
		contentSize: '500 275',
		content: strReturn,
  		contentOverflow: 'hidden',
  		callback: function (panel) {
  			jsPanel.pointerup.forEach(function (item) {
           	panel.footer.querySelector('#btn-close').addEventListener(item, function () {
           		Auswahl_beenden();
	         });
        });
	  	}
	});
}

function Element_Dialog_uebernehmen() {
	var Sprache = document.getElementById("sprache").value;
	var Auswahl = Element_aus_Fenster();
	var id = Auswahl.id;
	Eigenschaften2_temp = [];
	Eigenschaften3_temp = [];
	Formular = document.getElementById("Element_Dialog");
	Stil = "";
	Eigenschaften = document.getElementById(id).attributes;
	jQuery.ajax({
		url: "./Elementvorlage_einlesen.php?alle=0&Bezeichnung=" + Formular.Feldtyp.value,
		success: function (html) {
			Eigenschaften_temp = html;
		},
  		async: false
  	});
	Eigenschaften_temp = Eigenschaften_temp.split("@@@");
	for (x=0; x < Eigenschaften_temp.length; x++) {
		tempa = JSON.parse(Eigenschaften_temp[x]);
		Eigenschaften2_temp[tempa["Eigenschaft"]] = tempa;
		Eigenschaften3_temp[tempa["orig_Name"]] = Eigenschaften2_temp["Eigenschaft"];
	}
	for (i=1; i < Formular.elements.length; i++) {
		try {
			if (Formular[i].type == "checkbox") {
				if (Formular[i].checked == true) {
					Formular[i].value = 1;
				} else {
					Formular[i].value = 0;
				}
			}
		} catch (err) {}
		if ((Formular[i].id == "border-color" && Formular[i].value == "") || (Formular[i].id == "color" && Formular[i].value == "") || (Formular[i].id == "background-color" && Formular[i].value == "")) {
			try {
				if (Formular[i].id == "border-color" && Formular.class.value > "") {Auswahl.removeAttribute("border-color");}
			} catch (err) {}
			try {
				if (Formular[i].id == "color" && Formular.class.value > "") {Auswahl.removeAttribute("color");}
			} catch (err) {}
			try {
				if (Formular[i].id == "background-color" && Formular.class.value > "") {Auswahl.removeAttribute("background-color");}
			} catch (err) {}
		}
		try {
			Eigenschaft = Eigenschaften2_temp[Formular[i].id];
			if (Eigenschaft == undefined) {Eigenschaft = Eigenschaften3_temp[Formular[i].id];}
			if (Eigenschaft.Attributtyp == "style") {
				if (Eigenschaft["orig_Name"] != "top" && Eigenschaft["orig_Name"] !="left") {
					if (Formular.elements[Formular[i].id].value > "") {Stil += Eigenschaft["orig_Name"] + ":" + Formular.elements[Formular[i].id].value + "; ";}
				}
			} else {
				if (Formular[i].id != "onclick") {
					if (Formular[i].id == "css_Stil" || Formular[i].id == "css_stil" || Formular[i].id == "class") {
						if (Formular.elements[Formular[i].id].value > "") {
							Auswahl.setAttribute("class1", Formular[i].value);
						} else {
							delete Auswahl.removeAttribute("class1");
						}
					}
					if (Formular.elements[Formular[i].id].value > "") {
						Auswahl.setAttribute(Eigenschaft.orig_Name, Formular[i].value);
					} else {
						delete Auswahl.removeAttribute(Eigenschaft.orig_Name);
					}
				} else {
					Auswahl.setAttribute("onclick1", Formular[i].value);
				}
			}
		} catch (err) {}
	}
	//Verlinkung einbauen verlinken wird auf 2 gesetzt, damit die Links im Entwurfsmodus noch nicht aktiv sind
	try {if (Auswahl.attributes.verlinken.value == "1") {Auswahl.attributes.verlinken.value = "2";}} catch (err) {}

	//Falls es sich um einen Text handelt, dann den Textinhalt in das DIV einsetzen
	try {Auswahl.innerHTML = document.Element_Dialog.textinhalt.value;} catch (err) {}
	//Falls es sich um einen Link handelt, dann den Textinhalt und den URL in das DIV einsetzen
	try {Auswahl.innerHTML = "<a href='" + document.Element_Dialog.link.value + "'>" + document.Element_Dialog.Textinhalt.value + "</a>";} catch (err) {}
	try {Auswahl.innerHTML=document.Element_Dialog.inhalt.value;} catch (err) {}
	Auswahl.setAttribute("style", Stil);
	if (Auswahl.style.display == "none") {
		Auswahl.setAttribute("display1","none");
		Auswahl.style.display = "block";
	}
	if (Formular.Feldtyp.value == "Textarea") {Auswahl.style.overflow = "scroll";}
	Auswahl.parentElement.style.height = Auswahl.style.height;
	Auswahl.parentElement.style.width = Auswahl.style.width;
	kann_Eltern = DBQ('unidb', '','kann_Eltern','Elementvorlagen','Elementvorlage_ID = ' + tempa.Elementvorlage_ID);
	Auswahl.setAttribute("kann_eltern",kann_Eltern);
	if (Auswahl.hasAttribute("textinhalt") == true && Auswahl.attributes["typ"].value != "Link") {Auswahl.innerHTML = Auswahl.attributes.textinhalt.value;}
	try {Elementeinstellungen.close();} catch (err) {}
	einrichten();
	lesen();
}

function Element_Dialog_oeffnen() {
	try {
		Elementeinstellungen.close();
		mehrere_Elementeinstellungen.close();
		return;
	} catch (err) {}
	var Auswahl = Element_aus_Fenster();
	if (Auswahl != 1) {
		try {
			if (Auswahl.attributes.neu.value == "1") {
				Auswahl.style.backgroundColor = "";
				Auswahl.removeAttribute("neu");
			}
		} catch (err) {}
		var id = Auswahl.attributes["id"].value;
		var Sprache = document.getElementById("sprache").value;
		var DialogHoehe=120;
		var Zeilen = {};
		Zeilen[1] = 0;
		Zeilen[2] = 0;
		Zeilen[3] = 0;
		var Inhalt = "<div style=\"position: absolute; top: 10px; left: 10px;\">\n";
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[109] + "' type='button' onclick='Tab_umschalten(1);'>\n";
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[110] + "' type='button' onclick='Tab_umschalten(2);'>\n";
		Inhalt = Inhalt + "<input class='Schalter_Element' value='" + T_Text[111] + "' type='button' onclick='Tab_umschalten(3);'><br><br>\n";
		Inhalt = Inhalt + "<div id='Tabs'>\n<form id='Element_Dialog' name='Element_Dialog'>\n";
		var Inhalt1 = "<div id='Tab1' style='display: block;'>\n";
		Inhalt1 = Inhalt1 + "<table cellpadding='2px'>\n";
		Inhalt1 += "<input id='elemente_id' name='Elemente_ID' value='" + id + "' type='hidden'>\n";
		var Inhalt2 = "<div id='Tab2' style='display: none;'><table cellpadding='2px'>\n";
		var Inhalt3 = "<div id='Tab3' style='display: none;'><table cellpadding='2px'>\n";
		
		var Eigenschaften = "";
		if (document.getElementById(id).hasAttribute("feldtyp") == false) {document.getElementById(id).setAttribute("feldtyp", document.getElementById(id).attributes.typ.value);}
		if (document.getElementById(id).hasAttribute("typ") == false) {document.getElementById(id).setAttribute("typ", document.getElementById(id).attributes.feldtyp.value);}
		Eigenschaften = document.getElementById(id).attributes;
		Inhalt1 += "<input id='Feldtyp' value='" + Eigenschaften["feldtyp"].value + "' type='hidden'>\n";
		jQuery.ajax({
			url: "./Elementvorlage_einlesen.php?alle=0&Bezeichnung=" + Eigenschaften["feldtyp"].value,
			success: function (html) {
	   		Eigenschaften1 = html;
			},
   		async: false
   	});
   	Eigenschaften1 = Eigenschaften1.split("@@@");
		if (Eigenschaften1 != undefined && Eigenschaften1 != null) {
			for (x=0; x < Eigenschaften1.length; x++) {
				Eigenschaften2 = JSON.parse(Eigenschaften1[x]);
				if (Eigenschaften2.Eigenschaft != 'feldtyp' && Eigenschaften2.Eigenschaft != 'Feldtyp') {
					Eigenschaften2.Elemente_ID = id;
					if (Eigenschaften2.Tab == 'allgemein') {
						var Inhalt_temp = Inhalt1;
						var Karte = 1;
						Zeilen[1] = Zeilen[1] + 1;
					} else {
						if (Eigenschaften2.Tab == 'Scripte') {
							var Inhalt_temp = Inhalt2;
							var Karte = 2;
							Zeilen[2] = Zeilen[2] + 1;
						} else {
							if (Eigenschaften2.Tab == 'Format') {
								var Inhalt_temp = Inhalt3;
								var Karte = 3;
								Zeilen[3] = Zeilen[3] + 1;
							}
						}
					}
					if (Eigenschaften2.Eigenschaft == "zeitraum" || Eigenschaften2.Eigenschaft == "Zeitraum") {
						Inhalt_temp += "<tr><td align='right'><input class='Text_Element' type='button' name='Zeitraum_Schalter' value = '" + T_Text[31] + "' onclick='Zeitraum_dialog()'" + "></td><td>";
					} else {
						Inhalt_temp += "<tr><td align='right'>" + Eigenschaften2[Sprache] + "</td><td>";
					}
					if (Eigenschaften2.Darstellung_Dialog == "Textarea") {
						Position = Auswahl.innerHTML;
						if (Position == null) {Position = Eigenschaften2.Standardwert;}
						Inhalt_temp += "<Textarea class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "'>" + Position + "</Textarea>";
					}
					if (Eigenschaften2.Darstellung_Dialog == "Schalter") {
						Zusatz = "";
						Inhalt_temp += "<input class='Schalter_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='bearbeiten' type='button'" + Zusatz + ">";
					}
					if (Eigenschaften2.Darstellung_Dialog == "Textfeld") {
						Zusatz = "";
						if (Auswahl.attributes[Eigenschaften2.orig_Name] == undefined) {
							if (Eigenschaften2.Attributtyp == "style") {
								if (Auswahl.style[Eigenschaften2.orig_Name] == undefined) {
									Position = Eigenschaften2.Standardwert;
								} else {
									Position = Auswahl.style[Eigenschaften2.orig_Name];
								}
							} else {Position = Eigenschaften2.Standardwert;}
						} else {
							Position = Auswahl.attributes[Eigenschaften2.orig_Name].value;
							if (Position == "context-menu-two context-menu-active") {Position = "";}
						}
						if (Eigenschaften2.orig_Name.indexOf("color") > -1) {
							try {
								Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + rgbToHex(Eigenschaften2.Wert) + "' type='text' style='width: 40px;'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "_h' name='" + Eigenschaften2.Eigenschaft + "_h' value='" + rgbToHex(Eigenschaften2.Wert) + "' type='color' onchange='document.getElementById(\"" + Eigenschaften2.Eigenschaft + "\").value = document.getElementById(\"" + Eigenschaften2.Eigenschaft + "_h\").value;'>";
							} catch (err) {
								Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='' type='text' style='width: 40px;'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "_h' name='" + Eigenschaften2.Eigenschaft + "_h' value='' type='color' onchange='document.getElementById(\"" + Eigenschaften2.Eigenschaft + "\").value = document.getElementById(\"" + Eigenschaften2.Eigenschaft + "_h\").value;'>";
							}
						} else {
							if (Eigenschaften2.Eigenschaft == "Elementname") {Position = id;} 
							if (Position == null) {Position = Eigenschaften2.Standardwert;}
							if (Eigenschaften2.Eigenschaft == "oben" || Eigenschaften2.Eigenschaft == "links") {
								if (Eigenschaften2.Eigenschaft == "links") {Position = Auswahl.parentElement.parentElement.offsetLeft.toString() + "px";}
								if (Eigenschaften2.Eigenschaft == "oben") {Position = Auswahl.parentElement.parentElement.offsetTop.toString() + "px";}
								Zusatz = " onchange='Pos_sync();'";
							}
							Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Position + "' type='Text'" + Zusatz + ">";
						}
					}
					if (Eigenschaften2.Darstellung_Dialog == "Auswahl") {
						if (Eigenschaften2.Eigenschaft == "Feld") {
							SelectListe = Eigenschaften2.Eigenschaft.toLocaleLowerCase() + "liste";
							SelectListe = document.getElementById(SelectListe).value.split("@@@");
							Inhalt_temp += "<select class='Auswahl_Liste_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Eigenschaften2.Wert + "'>";
							try {
								for (i = 0; i < SelectListe.length; i++) {
									if(Eigenschaften.hasOwnProperty("feld") == false) {Eigenschaften.feld = "";}
									if(Eigenschaften.feld.value == SelectListe[i]) {
										Inhalt_temp += "<option selected>";
									} else {
										Inhalt_temp += "<option>";
									}
									Inhalt_temp += SelectListe[i] + "</option>\n";
								}
								Inhalt_temp += "</select>";
							} catch (err) {}
						} else {
							if (Eigenschaften2.Eigenschaft == "Gruppe") {
								Liste = document.getElementById("multistates").value.split(";");
								Optionen = "";
								for (i = 0; i < Liste.length; i++) {
									Listenzeile = Liste[i].split(",");
									Optionen += "<option value='" + Listenzeile[0] + "'";
									if(Eigenschaften.Gruppe.value == Listenzeile[0]) {
										Optionen += " selected>";
									} else {
										Optionen += ">";
									}
									if (Listenzeile[1] == undefined) {
										Optionen += "</option>\n";
									} else {
										Optionen += Listenzeile[1] + "</option>\n";
									}
								}
							} else {
								jQuery.ajax({
									url: "./Optionen_lesen.php?Vorlageneigenschaften_ID=" + Eigenschaften2.Vorlageneigenschaften_ID,
									success: function (html) {
	   							Optionen = html;
								},
   							async: false
	   						});
	   						if (Eigenschaften2.Wert == undefined) {
	   							links = Optionen.indexOf("<option value=''></option>");
		   						rechts = Optionen.length - links -26;
		   						Optionen = Optionen.substr(0,links) + "<option value='' selected></option>" + Optionen.substr(Optionen.length - rechts);
	   						}
	   					}
							Inhalt_temp += "<select class='Auswahl_Liste_Element' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Eigenschaften2.Wert + "'>";
							Inhalt_temp += Optionen;
							Inhalt_temp += "</select>";
						}
					}
					if (Eigenschaften2.Darstellung_Dialog == "Checkbox") {
						if (Eigenschaften2.Wert == "1") {
							Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Eigenschaften2.Wert + "' type='checkbox' checked>";
						} else {
							Inhalt_temp += "<input class='Text_Element' title='" + Eigenschaften2["Hinweis_" + Sprache] + "' id='" + Eigenschaften2.Eigenschaft + "' name='" + Eigenschaften2.Eigenschaft + "' value='" + Eigenschaften2.Wert + "' type='checkbox'>";
						}
					}
					Inhalt_temp += "</td></tr>\n";
					if (Karte == 1) {
						Inhalt1 = Inhalt_temp;
					} else {
						if (Karte == 2) {
							Inhalt2 = Inhalt_temp;
						} else {
							if (Karte == 3) {Inhalt3 = Inhalt_temp;}
						}
					}
					Inhalt_temp = "";
				}
			}
		}
		Inhalt1 += "</table></div>\n";
		Inhalt2 += "</table></div>\n";
		Inhalt3 += "</table></div>\n";
		Inhalt += Inhalt1 + Inhalt2 + Inhalt3;
		Inhalt += "</form><br><table><tr><td><input class='Text_Element' type='button' name='uebernehmen' value='" + T_Text[46] + "' onclick='Element_Dialog_uebernehmen();'></td><td>";
		Inhalt += "<td><input class='Text_Element' type='button' name='Tag_suchen_Schalter' value='" + T_Text[45] + "' onclick='Eingabefeld_sichtbar_dialog()'></td><td>";
		Inhalt += "<td><input class='Text_Element' type='button' name='cssdialog' value='" + T_Text[98] + "' onclick='CSS_Dialog_oeffnen();'></td></tr></table>";
		Inhalt += "</div></div>\n";
		var Multiplikator = 0;
		if (Zeilen[1] > Multiplikator) {Multiplikator = Zeilen[1];}
		if (Zeilen[2] > Multiplikator) {Multiplikator = Zeilen[2];}
		if (Zeilen[3] > Multiplikator) {Multiplikator = Zeilen[3];}
		DialogHoehe = DialogHoehe + Multiplikator * 29;
		jsPanel.create({
			onclosed: [
				function() {
					try {CSSauswahl.close();} catch (err) {}
					try {Dialog_CSSRegel.close();} catch (err) {}
				}
			],
			dragit: {
      		 snap: true
	       },
			id: 'Elementeinstellungen',
			theme: 'info',
			headerControls: {
				size: 'xs'
			},
			contentSize: '370 ' + DialogHoehe.toString(),
			headerTitle: T_Text[70],
			position: 'left-top 65 5',
			contentOverflow: 'scroll scroll',
			content: Inhalt
		});
		try {
			document.getElementById("Tagname").setAttribute("onfocusout","Tagname_setzen(\"Tagname\")");
			document.getElementById("Tagname").setAttribute("onblur","Tagname_setzen(\"Tagname\")");
			document.getElementById("Pfad").setAttribute("onfocusout","Tagname_setzen(\"Pfad\")");
			document.getElementById("Pfad").setAttribute("onblur","Tagname_setzen(\"Pfad\")");
			document.getElementById("Tag_ID").setAttribute("onchange","Tagname_setzen(\"Tag_ID\")");
//			document.getElementById("Tag_ID").setAttribute("onblur","Tagname_setzen(\"Tag_ID\")");
			document.getElementById("Ausdruck").setAttribute("onfocusout","Tagname_setzen(\"Ausdruck\")");
			document.getElementById("Ausdruck").setAttribute("onblur","Tagname_setzen(\"Ausdruck\")");
			document.getElementById("Point_ID").setAttribute("onchange","Tagname_setzen(\"Point_ID\")");
//			document.getElementById("Point_ID").setAttribute("onblur","Tagname_setzen(\"Point_ID\")");
		} catch (err) {}
		Formular = document.getElementById("Element_Dialog");
		Eigenschaften3 = [];
		for (x=0; x < Eigenschaften1.length; x++) {
			Eigenschaften1[x] = JSON.parse(Eigenschaften1[x]);
			Eigenschaften3[Eigenschaften1[x]["Eigenschaft"].toLocaleLowerCase()] = Eigenschaften1[x];
		}
		try {
			Eigenschaften["class"].value = Eigenschaften["class1"].value;
			delete Eigenschaften["class"];
		} catch (err) {}
		for (x=0; x < Eigenschaften.length; x++) {
			try{
				if (Eigenschaften[x].name != "style") {
					if (Eigenschaften3[Eigenschaften[x].name].Darstellung_Dialog == "Checkbox") {
						if (Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value > "0") {Formular[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].checked = true;}
					}
					if (Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value == undefined) {
						Formular[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value = Eigenschaften3[Eigenschaften[x].name].Standardwert;
					} else {
						if (Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value == "context-menu-two context-menu-active") {Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value = "";}
						if (Eigenschaften[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].name == "onclick") {
							Formular[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value = Eigenschaften["onclick1"].value;
						} else {
							Formular[Eigenschaften3[Eigenschaften[x].name].Eigenschaft].value = Eigenschaften[Eigenschaften3[Eigenschaften[x].name].orig_Name].value;
						}
					}
				}
			} catch (err) {}
		}
		Stile = Eigenschaften.style.value.split(";");
		for (x=0; x < Stile.length; x++) {
			if (Stile[x] != "" && Stile[x] != " ") {
				Stil = Stile[x].split(":");
				if (Stil[0][0] == " ") {Stil[0] = Stil[0].substr(1);}
				if (Stil[0] == "height") {Stil[0] = "höhe";}
				if (Stil[0] == "width") {Stil[0] = "breite";}
				if (Stil[1][0] == " ") {Stil[1] = Stil[1].substr(1);}
				if (Stil[1].substr(0,4) == "rgb(") {Stil[1] = rgbToHex(Stil[1]);}
				try{
					document.getElementById(Stil[0]).value = Stil[1];
				} catch (err) {}
			}
		}
	}
	if (Auswahl.hasAttribute("display1") == true) {Element_Dialog.elements.sichtbar.value = Auswahl.attributes["display1"].value;}
}

function Element_kopieren(Auswahl) {
	var Auswahl = Element_aus_Fenster();
	var cln = Auswahl.cloneNode(true);
	DH_Bereich.appendChild(cln);
	var jetzt = new Date();
	var Zeitpunkt = jetzt.getTime();
	try {
		var id =  Auswahl.attributes.typ.value + Zeitpunkt.toString();
	} catch (err) {
		var id = 'Gruppe' + Zeitpunkt.toString();
	}
	cln.setAttribute('id',id);
	cln.setAttribute('name',id);
	cln.style.top = "60px";
	Auswahl_beenden();
	auswaehlen(cln);
	Auswahl_beenden();
	//neues Element markieren
	cln.setAttribute('neu','1');
	cln.setAttribute('hintergrundfarbe_orig',cln.style.backgroundColor);
	cln.style.backgroundColor = "#FF0000";
	Auswahl.className = 'context-menu-one';
	var erledigt = 0;
	var temp = cln;
	var Kopieliste = [];
	var Kopie = [];
	Kopie["erledigt"] = 0;
	Kopie["id"] = temp.id;
	Kopie["orig_id"] = Auswahl.id;
	Kopieliste[0] = Kopie;
	var Kopie = [];
	x = 1;
	while (erledigt == 0) {
		erledigt = 1;
		temp = null;
		for (z = 0; z < Kopieliste.length; z++) {
			if (Kopieliste[z]["erledigt"] == 0) {
				Kopieliste[z]["erledigt"] = 1;
				if (Kopieliste[z].id > "" && Kopieliste[z].id != undefined) {
					temp = document.getElementById(Kopieliste[z].id);
					z = Kopieliste.length
				}
			}
		}
		if (temp != null) {
			for (i = 0; i < temp.childNodes.length; i++) {
				Kopie["orig_id"] = temp.childNodes[i].id;
				if(temp.childNodes[i].id > "" && temp.childNodes[i].id != undefined) {
					temp.childNodes[i].id = temp.childNodes[i].id + Zeitpunkt.toString();
				} else {
					temp.childNodes[i].id = "";
				}
				try {
					if (Kopie["orig_id"] > "" && Kopie["orig_id"] != undefined) {
						if (temp.childNodes[i].name != undefined) {temp.childNodes[i].setAttribute('name',temp.childNodes[i].name + Zeitpunkt.toString());}
						if (temp.childNodes[i].elemente_id != undefined) {temp.childNodes[i].setAttribute('elemente_id',temp.childNodes[i].elemente_id + Zeitpunkt.toString());}
					}
				} catch (err) {}
				if (temp.childNodes[i].childNodes.length > 0) {
					Kopie["erledigt"] = 0;
				} else {
					Kopie["erledigt"] = 1;
				}
				Kopie["id"] = temp.childNodes[i].id;
				Kopieliste.push(x);
				Kopieliste[x] = Kopie;
				Kopie = [];
				x = x + 1;
			}
			for (z = 0; z < Kopieliste.length; z++) {
				if (Kopieliste[z]["erledigt"] == 0) {
					erledigt = 0;
					z = Kopieliste.length
				}
			}
		}
	}
}

function Bild_Dialog_oeffnen() {
	var Inhalt = '<div style="background: #FCEDD9; width: 720px; height: 470px;"><div style="position: absolute; top: 10px; left: 10px"><form name="Bild_Dialog"><table>\n';
	Inhalt = Inhalt + '<tr height="40px"><td class="Text_einfach" style="text-align: right">' + T_Text[47] + '</td><td colspan="2"><input class="Text_Element" name="Bildname" size="40" value="' + document.phpform.Bezeichnung.value + '" type="text"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="40px"><td class="Text_einfach" style="text-align: right">' + T_Text[103] + '</td><td colspan="2"><input class="Text_Element" id="hintergrundfarbe_dialog" name="Hintergrundfarbe" value="' + document.phpform.Hintergrundfarbe.value + '" type="text" style="width: 40px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="Text_Element" id="hintergrundfarbe_dialog_h" name="Hintergrundfarbe_h" value="' + document.phpform.Hintergrundfarbe.value + '" type="color" onchange="document.getElementById(\'hintergrundfarbe_dialog\').value = document.getElementById(\'hintergrundfarbe_dialog_h\').value;"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="40px"><td class="Text_einfach" style="text-align: right">' + T_Text[48] + '</td><td colspan="2"><input class="Text_Element" id="hintergrundbild_dialog" name="Hintergrundbild" size="60" value="' + document.phpform.Hintergrundbild.value + '" type="text"></td>\n';
	Inhalt = Inhalt + '<td>' + T_Text[49] + '<input class="Schalter_Element" name="Bilddialog2" value="' + T_Text[50] + '" type="button" onclick="Bild_Dialog2()"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="40px"><td class="Text_einfach" style="text-align: right">' + T_Text[51] + '</td><td colspan="2"><input class="Text_Element" name="Tags_Pfad" size="60" value="' + document.phpform.Tags_Pfad.value + '" type="text"></td></tr>\n';
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">' + T_Text[112] + '</td><td colspan="3"><textarea class="Text_Element" name="Headererweiterung_Dialog" style="height: 60px; width: 560px;">' + document.phpform.headererweiterung.value + '</textarea></td></tr>\n';
	var js = document.phpform.Bei_Start.value.replace(/§§§/g,"'");
	js = js.replace(/@@@/g,'"');
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">Js - onload</td><td colspan="3"><textarea class="Text_Element" name="Bei_Start_Dialog" style="height: 60px; width: 560px;">' + js + '</textarea></td></tr>\n';
	Inhalt = Inhalt + '<tr style="height: 40px;"><td class="Text_einfach" style="text-align: right">' + T_Text[52] + '</td><td><input class="Schalter_Element" name="übernehmen" value="' + T_Text[53] + '" type="button" onclick="Bildeinstellungen_uebernehmen()"></td><td><input class="Schalter_Element" name="Pfade_editieren" value="' + T_Text[54] + '" type="button" onclick="Pfade_Dialog_oeffnen();"></td></tr>\n';
	Inhalt = Inhalt + '</table></form></div></div>\n';
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Bildeinstellungen',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '720 470',
		headerTitle: T_Text[55],
		position: 'left-top 30 30',
		contentOverflow: 'hidden',
		content: Inhalt,
	});
}

function Bild_Dialog2() {
	var Inhalt = '<div style="background: #FCEDD9; width: 540px; height: 80px;"><div style="position: absolute; top: 10px; left: 10px"><table>\n';
	Inhalt = Inhalt + '<tr><td class="Text_einfach" style="text-align: right">' + T_Text[56] + '</td><td colspan ="2"><select class="Auswahl_Liste_Element" size="1" id="hintergrundbild2" name="Hintergrundbild2" onchange="Bild_hier_zeigen();">';
	Dateien = document.getElementById("bilderliste").value.split(";");
	for (x = 0; x < Dateien.length; x++) {
		if (Dateien[x] !="." && Dateien[x] !=".." && Dateien[x] !="Hilfe") {
			Inhalt = Inhalt + "<option";
			if (Dateien[x] == document.phpform.Hintergrundbild.value) {
				Inhalt = Inhalt + " selected";
			}
			Inhalt = Inhalt + ">" + Dateien[x] + "</option>";
		}
	}
	Inhalt = Inhalt + "</select></td></tr>";
	Inhalt = Inhalt + '<tr height="40px"><td class="Text_einfach" style="text-align: right">' + T_Text[57] + '</td><td><input class="Schalter_Element" name="übernehmen" value="' + T_Text[53] + '" type="button" onclick="Bildeinstellungen2_uebernehmen()"></td><td align="right"><input class="Schalter_Element" name="abbrechen" value="' + T_Text[58] + '" type="button" onclick="Bildeinstellungen2_abbrechen()"></td>\n';
	Inhalt = Inhalt + '</table></div></div>\n';
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Bildeinstellungen2',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '445 80',
		headerTitle: T_Text[59],
		position: 'left-top 265 255',
		content:  Inhalt,
		contentOverflow: 'hidden'
	});
}

function Bildeinstellungen2_uebernehmen() {
	document.getElementById("hintergrundbild_dialog").value = "./Bilder/" + document.getElementById("hintergrundbild2").value;
	try {Bildeinstellungen2.close();} catch (err) {}
}

function Bildeinstellungen2_abbrechen() {
	try {Bildeinstellungen2.close();} catch (err) {}
}

function Bildeinstellungen_uebernehmen() {
	document.phpform.Bezeichnung.value=document.Bild_Dialog.Bildname.value;
	document.phpform.Hintergrundfarbe.value=document.Bild_Dialog.Hintergrundfarbe.value;
	document.phpform.Hintergrundbild.value=document.Bild_Dialog.Hintergrundbild.value;
	document.phpform.Tags_Pfad.value=document.Bild_Dialog.Tags_Pfad.value;
	temp_Text = document.Bild_Dialog.Headererweiterung_Dialog.value.replace(/'/g,"\"");
	document.phpform.Headererweiterung.value = temp_Text;
	temp_Text = document.Bild_Dialog.Bei_Start_Dialog.value.replace(/'/g,"§§§");
	temp_Text = temp_Text.replace(/"/g,"@@@");
	document.phpform.Bei_Start.value = temp_Text;
	if (document.Bild_Dialog.Hintergrundbild.value > "") {document.body.style.background = "url('" + document.Bild_Dialog.Hintergrundbild.value + "') no-repeat top 60px left 0px";}
	try {Bildeinstellungen.close();} catch (err) {}
	try {Bildeinstellungen2.close();} catch (err) {}
}

function Element_entfernen() {
	var Auswahl = Element_aus_Fenster();
	var Fenster = Auswahl.parentElement.parentElement;
	Auswahl.parentElement.removeChild(Auswahl);
	Fenster.close();
	ausgewaehlt = 0;
}

function Auswahl_beenden() {
	//if (Elternelement == null) {Elternelement = document.getElementById("DH_Bereich");}
 	try{
 		Auswahl = Element_aus_Fenster();
 		try {Auswahl.style.opacity = "";} catch (err) {}
		try {if (Auswahl.attributes.feldtyp.value == "Unterformular") {Auswahl.style.border = "";}} catch (err) {}
 		for (i=0; i < Auswahl.childNodes.length; i++) {
			try {Auswahl.childNodes[i].style.opacity = "";} catch (err) {}
		}
 		var Fenster = Auswahl.parentElement.parentElement;
 		try {Auswahl.removeAttribute("class");} catch (err) {}
 		try {Auswahl.setAttribute("class", Auswahl.attributes.class1.value);} catch (err) {}
 		Auswahl.style.position = "absolute";
 		if (Elternelement.id == "DH_Bereich") {
 			var tempoben = 0;
			var templinks = 0;
 		} else {
			var oberstesElement = Elternelement;
			var tempoben = 0;
			var templinks = 0;
			while (oberstesElement.id != "DH_Bereich") {
				try {
					if (oberstesElement.style.left.length > 0) {templinks = templinks + parseInt(oberstesElement.style.left);}
					if (oberstesElement.style.top.length > 0) {tempoben = tempoben + parseInt(oberstesElement.style.top);}
				} catch (err) {}
 				oberstesElement = oberstesElement.parentElement;
 			}
 		}
 		//tempoben = tempoben + parseInt(oberstesElement.style.top);
		//templinks = templinks + parseInt(oberstesElement.style.left);
 		if ((Auswahl.parentElement.parentElement.offsetTop - tempoben) < 0) {
 			Auswahl.style.top = "0px";
 		} else {
			Auswahl.style.top = (Auswahl.parentElement.parentElement.offsetTop - tempoben).toString() + "px";
		}
		if ((Auswahl.parentElement.parentElement.offsetLeft - templinks) < 0) {
 			Auswahl.style.left = "0px";
 		} else {
			Auswahl.style.left = (Auswahl.parentElement.parentElement.offsetLeft - templinks).toString() + "px";
		}
		Elternelement.appendChild(Auswahl);
	} catch (err) {}
	i = 0;
	while (i < Elternelement.childNodes.length) {
		try {
			Elternelement.childNodes[i].style.border = Elternelement.childNodes[i].attributes["rahmen_orig"].value;
			Elternelement.childNodes[i].setAttribute("rahmen_orig","");
		} catch (err) {}
		i = i + 1;
	}
	try {Fenster.close();} catch (err) {}
	try {Elementeinstellungen.close();} catch (err) {}
	ausgewaehlt = 0;
}

function Groesse_anpassen() {
	try{
		var Auswahl = Element_aus_Fenster();
		Auswahl.style.width = Auswahl.parentElement.parentElement.style.width;
		Auswahl.style.height = Auswahl.parentElement.parentElement.content.clientHeight.toString() + "px";
		if (Auswahl.firstChild.localName != "label") {
			Auswahl.firstChild.style.width = Auswahl.parentElement.parentElement.style.width;
			Auswahl.firstChild.style.height = Auswahl.parentElement.parentElement.content.clientHeight.toString() + "px";
		} else {
			Auswahl.firstChild.style.width = (parseInt(Auswahl.style.width) - parseInt(Auswahl.attributes["feldbreite"].value)).toString() + "px";
			Auswahl.childNodes[1].style.height = Auswahl.parentElement.parentElement.content.clientHeight.toString() + "px";
		}
		Auswahl = "undefinied";
	} catch (err) {}
}

function Element_aus_Fenster() {
	var Auswahl = 1;
	try{
		var panels = jsPanel.getPanels(function () {
			if (this.id == "ausgewaehlt") {return this;}
		}).map(function (panel) {
   	if (panel.id == "ausgewaehlt"){
  	 		Auswahl = panel.content.childNodes[0];
		}
		});
	} catch (err) {}	
	return Auswahl;
}

function einrichten() {
	DH_Elemente=[];
	var ausgewaehlt;
	//Elementliste als Objekt - Array erstellen
	for (i=0;i<DH_Bereich.childNodes.length;i++) {
		ausgewaehlt=DH_Bereich.childNodes[i];
		einrichten2();
	}
	try {
		ausgewaehlt = Element_aus_Fenster();
		einrichten2();
	} catch (err) {}
	function einrichten2() {
		if (ausgewaehlt.nodeName=="DIV") {
			try {
				var feine_Teilung="";
				try {feine_Teilung=ausgewaehlt.attributes.feine_teilung.value;} catch (err) {}
				var Einheit="";
				try {Einheit=ausgewaehlt.attributes.einheit.value;} catch (err) {}
				var Ausdruck="";
				try {Ausdruck=ausgewaehlt.attributes.ausdruck.value;} catch (err) {}
				var Gruppe="";
				try {Gruppe=ausgewaehlt.attributes.gruppe.value;} catch (err) {}
				var aktualisieren="";
				try {aktualisieren=ausgewaehlt.attributes.aktualisieren.value;} catch (err) {}
				var Punkt = 0;
				try {Punkt=ausgewaehlt.attributes.tag_id.value;} catch (err) {}
				var Typ = ausgewaehlt.attributes.typ.value;
				var Min=0;
				var Max=0;
				var Teilung=0;
				var Einheit="";
				try {Einheit=ausgewaehlt.attributes.Einheit.value;} catch (err) {}
				var wert_anzeigen=""
				try {wert_anzeigen=ausgewaehlt.attributes.wert_anzeigen.value;} catch (err) {}
				var Breite=ausgewaehlt.style.width;
				Breite = Breite.substr(0,Breite.length-2);
				var Hoehe=ausgewaehlt.style.height;
				Hoehe = Hoehe.substr(0,Hoehe.length-2);
				var Zeitraum=0;
				try {Zeitraum=ausgewaehlt.attributes.zeitraum.value;} catch (err) {}
				try {Max=parseFloat(ausgewaehlt.attributes.max.value);} catch (err) {}
				try {Min=parseFloat(ausgewaehlt.attributes.min.value);} catch (err) {}
				try {Teilung = (Max-Min)/10;} catch (err) {}
				var Eigenschaften = {
					id: ausgewaehlt.id,
					Punkt: Punkt,
					Typ: Typ,
					Min: Min,
					Max: Max,
					Teilung: Teilung,
					Hoehe: Hoehe,
					Breite: Breite,
					Zeitraum: Zeitraum,
					Einheit: Einheit,
					aktualisieren: aktualisieren,
					Gruppe: Gruppe,
					Ausdruck: Ausdruck,
					Einheit: Einheit,
					feine_Teilung: feine_Teilung,
					Element: ausgewaehlt,
					wert_anzeigen: wert_anzeigen
				}
				DH_Elemente.push(Eigenschaften);
			} catch (err) {}
		}
	} 
	
	//Elemente einrichten, wenn noetig
	for (i = 0; i < DH_Elemente.length; i++) {
		if (DH_Elemente[i].Typ == "Instrument") {
			var Instrument = DH_Elemente[i].Element.firstElementChild.contentDocument;
			Instrument.documentElement.attributes.height.value=DH_Elemente[i].Hoehe+"px";
			Instrument.documentElement.attributes.width.value=DH_Elemente[i].Breite+"px";
			//nicht zu viele Nachkommastellen
			if (parseInt(DH_Elemente[i].Teilung*1000)/1000 != 0) {
				var Hilfswert = 1000;
			} else {
				var Hilfswert = 10000;
			}
			DH_Elemente[i].Teilung=Math.round(DH_Elemente[i].Teilung*Hilfswert)/Hilfswert;
			DH_Elemente[i].Min=Math.round(DH_Elemente[i].Min*Hilfswert)/Hilfswert;
			for (x=0; x < 11; x++) {
				Skalenwert=Math.round((x*DH_Elemente[i].Teilung+DH_Elemente[i].Min)*Hilfswert)/Hilfswert;
				Instrument.getElementById("Beschriftung" + x.toString()).textContent = Skalenwert;
			}
			Instrument.getElementById("svg").attributes.height.value = DH_Elemente[i].Element.style.height;
			Instrument.getElementById("svg").attributes.width.value = DH_Elemente[i].Element.style.width;
			Instrument.getElementById("Einheit").textContent = DH_Elemente[i].Einheit;
			Instrument.getElementById("Link").attributes["xlink:href"].value="./Trend.php?Tag_ID="+DH_Elemente[i].Punkt;
			if (DH_Elemente[i].feine_Teilung=="1") {
				Instrument.getElementById("feine_Teilung").style.display = "inline";
			} else {
				Instrument.getElementById("feine_Teilung").style.display = "none";
			}
			
			//Deckel darüber, damit man das Element darüber auswählen kann
			var Deckel = document.getElementById('Deckel'+ DH_Elemente[i].id);
			if (Deckel == null) {
				var newdiv = document.createElement('div');
				newdiv.setAttribute('id','Deckel'+ DH_Elemente[i].id);
				newdiv.style.position="absolute";
				newdiv.style.top="0px";
				newdiv.style.left="0px";
				newdiv.style.height=DH_Elemente[i].Element.style.height;
				newdiv.style.width=DH_Elemente[i].Element.style.width;
				newdiv.style.zIndex=parseFloat(DH_Elemente[i].Element.style.zIndex) + 1;
				newdiv.innerHTML="<img src='Multistates/leer.png' width='" +DH_Elemente[i].Element.style.width + "' height='" +DH_Elemente[i].Element.style.height +"'>";
				if (document.phpform.mobil.value=="1") {
					newdiv.setAttribute('ontouchend', 'auswaehlen(this);');
				} else {
					newdiv.setAttribute('onclick', 'auswaehlen(this);');
				}
				DH_Elemente[i].Element.appendChild(newdiv);
			}
		}
		if (DH_Elemente[i].Typ == "Instrument 2") {
			try {document.getElementById(DH_Elemente[i].id).removeChild(document.getElementById(DH_Elemente[i].id).firstChild);} catch (err) {}
			Instr2[DH_Elemente[i].id] = Gauge(
				document.getElementById(DH_Elemente[i].id), {
  					dialStartAngle: 180,
  			      dialEndAngle: 0,
  		   	   min: DH_Elemente[i].Min,
  		      	max: DH_Elemente[i].Max,
        			value: 0,
		   	  	viewBox: "0 0 100 57",
  	     			color: function(value) {return "#5ee432";}
				}
			); 
		}
	}
}

function lesen() {
	for (i = 0; i < DH_Elemente.length; i++) {
		if (DH_Elemente[i].aktualisieren == "1"){		
			var strReturn = "";
			try {var ausdruck = encodeURIComponent(DH_Elemente[i].Ausdruck);} catch (err) {ausdruck = "";}
			if (DH_Elemente[i].Typ == "Instrument 2") {
				var strURL = "Wert.php?Tag_ID=" + DH_Elemente[i].Punkt + "&Einheit=" + DH_Elemente[i].Einheit + "&Typ=Tag&Zeitpunkt=jetzt&Ausdruck="+ausdruck;
			} else {
				var strURL = "Wert.php?Tag_ID="+DH_Elemente[i].Punkt+"&Einheit="+DH_Elemente[i].Einheit+"&wert_anzeigen="+DH_Elemente[i].wert_anzeigen+"&Typ="+DH_Elemente[i].Typ+"&Zeitpunkt=jetzt&Breite="+DH_Elemente[i].Breite+"&Hoehe="+DH_Elemente[i].Hoehe+"&Zeitraum="+DH_Elemente[i].Zeitraum+"&Gruppe="+DH_Elemente[i].Gruppe+"&Ausdruck="+ausdruck+"&min="+DH_Elemente[i].Min+"&max="+DH_Elemente[i].Max;
			}
			jQuery.ajax({
				url: strURL,
				success: function (html) {
         	   strReturn = html;
	        	},
   	     	async: false
    		});
			strReturn = strReturn.split("@");
			if (DH_Elemente[i].Typ == "Instrument") {
				try {document.getElementById(DH_Elemente[i].id).attributes.title.value=strReturn[1];} catch (err) {}
				var wert = parseFloat(strReturn[0]);
				var Instrument = DH_Elemente[i].Element.firstChild.contentDocument.firstChild;
				Instrument.getElementById("Wert").innerHTML = wert;
				if (wert < DH_Elemente[i].Min) {wert = parseFloat(DH_Elemente[i].Min);}
				if (wert > DH_Elemente[i].Max) {wert = parseFloat(DH_Elemente[i].Max);}
				var Winkel = ((wert-DH_Elemente[i].Min)/DH_Elemente[i].Teilung)*25;
				Instrument.getElementById("Zeiger").attributes.transform.value = "rotate(" + Winkel.toString() + " 100 100)";
			} else {
				if (DH_Elemente[i].Typ == "Instrument 2") {
					var wert = parseFloat(strReturn[0]);
					Instr2[DH_Elemente[i].id].setValue(wert);
				} else {
					document.getElementById(DH_Elemente[i].id).innerHTML=strReturn[0];
				}
				try {document.getElementById(DH_Elemente[i].id).attributes.title.value=strReturn[1];} catch (err) {}
			}
		}
	}
}

function uebertragen() {
	try {
		var Ergebnis = document.Tag_finden.Ergebnis.value.split(" - ");
		document.Element_Dialog.Pfad.value = Ergebnis[0];
		document.Element_Dialog.Tagname.value = Ergebnis[1];
		Tagname_setzen("Tagname");
	}
	catch (err) {}
}

function Pos_sync(){
document.getElementById("ausgewaehlt").style.top = document.getElementById("Element_Dialog").top.value;
document.getElementById("ausgewaehlt").style.left = document.getElementById("Element_Dialog").left.value;
}

function Zeitraum_dialog() {
	var Zeit = 0;
	try {Zeit = parseInt(document.getElementById("zeitraum").value);} catch (err) {}
	var Tage = 0;
	var Stunden = 0;
	var Minuten = 0;
	var Sekunden = 0;
	if (Zeit > 0){
		Tage = parseInt(Zeit / 86400);
		Zeit = Zeit - Tage * 86400;
		Stunden = parseInt(Zeit / 3600);
		Zeit = Zeit - Stunden * 3600;
		Minuten = parseInt(Zeit / 60);
		Zeit = Zeit - Minuten * 60;
		Sekunden = parseInt(Zeit);
	}
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px'><form><table>\n";
	Inhalt += "<tr><td><input class='Text_Element' type='text' size='3' id='FeldTage' name='Tage' value='" + Tage + "' oninput='Zeitraumrechnen();'></td><td>" + T_Text[60] + "</td></tr>\n";
	Inhalt += "<tr><td><input class='Text_Element' type='text' size='3' id='FeldStunden' name='Stunden' value='" + Stunden + "' oninput='Zeitraumrechnen();'></td><td>" + T_Text[61] + "</td></tr>\n";
	Inhalt += "<tr><td><input class='Text_Element' type='text' size='3' id='FeldMinuten' name='Minuten' value='" + Minuten + "' oninput='Zeitraumrechnen();'></td><td>" + T_Text[62] + "</td></tr>\n";
	Inhalt += "<tr><td><input class='Text_Element' type='text' size='3' id='FeldSekunden' name='Sekunden' value='" + Sekunden + "' oninput='Zeitraumrechnen();'></td><td>" + T_Text[63] + "</td></tr>\n";
	Inhalt += "</table></form></div>\n";
	jsPanel.create({
		id: 'Zeitraum_dialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '200 200',
		content: Inhalt,
  		contentOverflow: 'hidden',
	});
}

function Zeitraumrechnen() {
	var Tage = document.getElementById("FeldTage").value;
	var Stunden = document.getElementById("FeldStunden").value;
	var Minuten = document.getElementById("FeldMinuten").value;
	var Sekunden = document.getElementById("FeldSekunden").value;
	var Zeit = 0;
	try {Zeit = Zeit + parseInt(Tage) * 86400;} catch (err) {}
	try {Zeit = Zeit + parseInt(Stunden) * 3600;} catch (err) {}
	try {Zeit = Zeit + parseInt(Minuten) * 60;} catch (err) {}
	try {Zeit = Zeit + parseInt(Sekunden);} catch (err) {}
	document.getElementById("Zeitraum").value = Zeit;
}

function Baustein_laden() {
	//DIV zuerst leeren
	var Auswahl = document.getElementById("Bausteinvorschau");
	while (Auswahl.childNodes.length > 0) {
		Auswahl.removeChild(Auswahl.firstChild);
	}
	var strURL = "./Baustein_Vorschau.php?Baustein_ID=" + document.getElementById("Bausteinauswahlliste").value;
	var Inhalt = ""; 
	jQuery.ajax({
		url: strURL,
		success: function (html) {
     	   Inhalt = html;
     	},
     	async: false
	});
	Auswahl.innerHTML = Inhalt;
	//Jetzt noch alle Elemente um 60px hochrücken
	for (i=0; i < Auswahl.childNodes.length; i++) {
		Element = Auswahl.childNodes[i];
		try {
			Element.style.top = (parseInt(Element.style.top) - 60).toString() + "px";
		} catch (err) {}
	}
}

function Baustein_einfuegen() {
	var Auswahl = document.getElementById("Bausteinvorschau");
	Auswahl.style.removeProperty("width");
	var jetzt = new Date();
	var id = jetzt.getTime();
	Auswahl.setAttribute('id',"Gruppe" + id);
	if (document.phpform.mobil.value=="1") {
		Auswahl.setAttribute('ontouchend', 'auswaehlen(this);');
	} else {
		Auswahl.setAttribute('onclick', 'auswaehlen(this);');
	}
	//Alle Elemente in der neuen Gruppierung mit einer neuen ID versorgen und das breiteste und höchste Element finden
	var Hoehe = 0;
	var Breite = 0;
	for (i=0; i < Auswahl.childNodes.length; i++) {
		id = id + 1;
		Auswahl.childNodes[i].setAttribute('id', id);
		if (parseInt(Auswahl.childNodes[i].style.height) > Hoehe) {Hoehe = parseInt(Auswahl.childNodes[i].style.height);}
		if (parseInt(Auswahl.childNodes[i].style.width) > Breite) {Breite = parseInt(Auswahl.childNodes[i].style.width);}
	}
	//Maße der Gruppierung anpassen
	Auswahl.style.height = Hoehe.toString() + "px";
	Auswahl.style.width = Breite.toString() + "px";
	//Jetzt die Gruppierung dem DH_Bereich hinzufügen
	document.getElementById("DH_Bereich").appendChild(Auswahl);
	document.getElementById('Baustein_aussuchen').close();
}

function rgbToHex(Wert) {
	Wert = Wert.substr(4,Wert.length - 5)
	Wert = Wert.split(", ");
	return "#" + ((1 << 24) + (parseInt(Wert[0]) << 16) + (parseInt(Wert[1]) << 8) + parseInt(Wert[2])).toString(16).slice(1);
}

function hexToRgb(Wert){
	if (Wert.substr(0,1) == "#") {Wert = Wert.substr(1,Wert.length);}
	var r = parseInt((Wert).substring(0,2),16);
	var g = parseInt((Wert).substring(2,4), 16);
	var b = parseInt((Wert).substring(4,6),16);
	return "rgb(" + r.toString() + "," + g.toString() + "," + b.toString() + ")";
}

function Bild_zeigen(Datei) {
  	jsPanel.create({
		dragit: {
			snap: true
      },
		id: 'Vorschau',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '200 200',
		headerTitle: T_Text[64],
		position: 'left-top 100 30',
		content:  "<img id='bild_angezeigt' src='" + Datei + "'>",
	});
	//nur wegen dem async, denn sonnst ist das Bild nicht komplett da wenn die nächsten Zeilen bearbeitet werden
	jQuery.ajax({
		url: Datei,
		success: function (html) {
   		Bild = html;
		},
 		async: false
 	});
	//Ende des JS Wahnsinns
	if (document.getElementById("bild_angezeigt").width > 150) {
		document.getElementById("Vorschau").style.width = (document.getElementById("bild_angezeigt").width).toString() + "px";
	} else {
		document.getElementById("Vorschau").style.width = "150px";
	}
	if (document.getElementById("bild_angezeigt").height > 140) {
		document.getElementById("Vorschau").style.height = (document.getElementById("bild_angezeigt").height + 50).toString() + "px";
	} else {
		document.getElementById("Vorschau").style.height = "150px";
	}
}

function Vers_wiederherstellen(Variante) {
	if (Variante == "wiederherstellen") {
		document.forms["phpform"].aktion.value = "wiederherstellen";
	} else {
		document.forms["phpform"].aktion.value = "löschen";
	}
	document.forms["phpform"].submit();
}
	
function bed_Format_Dialog() {
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table cellspan='3px' cellpadding='2px' id='bed_Tab'>";
	Inhalt += '<tr class="Tabellenzeile"><td class="Tabelle_Ueberschrift">' + T_Text[80] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[75] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[76] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[78] + '</td><td class="Tabelle_Ueberschrift">' + T_Text[79] + '</td><td></td><td align="right"><input type="button" name="Hilfe" class="Schalter_Element" value="' + T_Text[12] + '" onclick="Hilfe_Fenster(\'41\');"></td></tr>';
	i = 1;
	while (Bedingungen[i] != undefined) {
		Inhalt += '<tr class="Tabellenzeile"><td>' + i.toString() + '</td><td>' + Bedingungen[i].Bedingung + '</td><td>' + Bedingungen[i].Element + '</td><td>' + Bedingungen[i].Kommentar + '</td><td><div style="' + Bedingungen[i].Stil + '" class="' + Bedingungen[i].class + '">' + T_Text[86] + '</div></td>';
		Inhalt += '<td><input class="Schalter_Element" type="button" value="' + T_Text[87] + '" onclick="bed_Dialog(\'' + i.toString() + '\');"></td>';
		Inhalt += '<td><input class="Schalter_Element" type="button" value="' + T_Text[9] + '" onclick="bed_entfernen(\'' + i.toString() + '\');"></td></tr>';
		i = i + 1;
	}
	Inhalt += '</table><table><tr style="height: 50px;"><td colspan="2"><input class="Schalter_Element" type="button" name="uebernehmen" value="' + T_Text[0] + '" onclick="Bed_Format_uebernehmen();"></td>';
	Inhalt += '<td colspan="2"><input class="Schalter_Element" type="button" name="neu" value="' + T_Text[77] + '" onclick="bed_Dialog(\'neu\');"></td></tr>';
	Inhalt += '</table></div>';

	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'Bedingte_Formatierung',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '480 360',
		headerTitle: T_Text[88],
		position: 'left-top 10 60',
		content: Inhalt
	});
}

function bed_Dialog(BedNr) {
	var Inhalt = "<div style='position: absolute; top: 10px; left: 10px;'><table>";
	Inhalt += '<tr><td align="right">' + T_Text[75] + '</td><td colspan="2"><textarea style="width: 210px; height: 16px;" class="Text_Element" id="bedingung_feld" name="Bedingung_Feld"></textarea></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[76] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" id="element_feld" name="Element_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[78] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" id="kommentar_feld" name="Kommentar_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td colspan="3"><hr></td></tr>';
	Inhalt += '<tr style="height: 30px;"><td align="right">' + T_Text[89] + '</td><td><select class="Auswahl_Liste_Element" id="sichtbar_feld" name="sichtbar_Feld" value=""><option value="" selected></option><option value="none">none</option><option value="block">block</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[90] + '</td><td><select class="Auswahl_Liste_Element" id="border-style_feld" name="border-style_Feld" value="undefined"><option value="solid">durchgezogen</option><option value="dotted">gepunktet</option><option value="dashed">gestrichelt</option><option value="" selected=""></option><option value="double">doppelt</option><option value="groove">Rille</option><option value="ridge">Grat</option><option value="inset">innen gesetzt</option><option value="outset">außen gesetzt</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[91] + '</td><td><input class="Text_Element" id="rahmenfarbe_feld" name="Rahmenfarbe_Feld" value="" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[92] + '</td><td><input style="width: 40px;" class="Text_Element" id="rahmenbreite_feld" name="Rahmenbreite_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[39] + '</td><td colspan="2"><select class="Auswahl_Liste_Element" id="font-family_feld" name="font-family_Feld" value=""><option value="arial, helvetica, sans-serif">arial, helvetica, sans-serif</option><option value="roman, times new roman, times, serif">roman, times new roman, times, serif</option><option value="courier, fixed, monospace">courier, fixed, monospace</option><option value="western, fantasy">western, fantasy</option><option value="Zapf-Chancery, cursive">Zapf-Chancery, cursive</option><option value="serif">serif</option><option value="sans-serif">sans-serif</option><option value="cursive">cursive</option><option value="fantasy">fantasy</option><option value="monospace">monospace</option><option value=""></option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[93] + '</td><td><select class="Auswahl_Liste_Element" id="font-style_feld" name="font-style_Feld" value=""><option value="" selected></option><option value="normal">normal</option><option value="italic">kursiv</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[37] + '</td><td><input class="Text_Element" id="color_feld" name="color_Feld" value="" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[94] + '</td><td><input class="Text_Element" id="background-color_feld" name="background-color_Feld" value="#FFFFFF" type="color"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[95] + '</td><td><select class="Auswahl_Liste_Element" id="font-weight_feld" name="font-weight_Feld" value=""><option value=""></option><option value="normal">normal</option><option value="bold">fett</option><option value="bolder">fetter</option><option value="lighter">dünner</option><option value="100">100</option><option value="200">200</option><option value="300">300</option><option value="400">400</option><option value="500">500</option><option value="600">600</option><option value="700">700</option><option value="800">800</option><option value="900">900</option></select></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[40] + '</td><td><input style="width: 40px;" class="Text_Element" id="font-size_feld" name="font-size_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr><td align="right">' + T_Text[96] + '</td><td colspan="2"><input style="width: 210px;" class="Text_Element" title="' + T_Text[97] + '" id="class_feld" name="class_Feld" value="" type="Text"></td></tr>';
	Inhalt += '<tr style="height: 50px;"><td align="right"><input class="Schalter_Element" type="button" name="uebernehmen_Feld" value="' + T_Text[0] + '" onclick="Bedingung_uebernehmen(\'' + BedNr.toString() + '\');"></td>';
	Inhalt += '<td align="left"><input class="Schalter_Element" type="button" name="cssdialog_Feld" value="' + T_Text[98] + '" onclick="CSS_Dialog_oeffnen();"></td></tr>';
	Inhalt += '</table></div>';

	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'bed_Format_Einstellung',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '340 440',
		headerTitle: T_Text[99],
		position: 'left-top 340 60',
		content: Inhalt
	});
	if (BedNr != "neu") {
		Zeile = document.getElementById("bed_Tab").firstChild.childNodes[BedNr];
		document.getElementById("bedingung_feld").innerHTML = Bedingungen[BedNr]["Bedingung"];
		document.getElementById("element_feld").value = Bedingungen[BedNr]["Element"];
		document.getElementById("kommentar_feld").value = Bedingungen[BedNr]["Kommentar"];
		document.getElementById("font-family_feld").value = Bedingungen[BedNr]["font-family"];
		document.getElementById("font-style_feld").value = Bedingungen[BedNr]["font-style"];
		document.getElementById("color_feld").value = Bedingungen[BedNr]["color"];
		document.getElementById("font-size_feld").value = Bedingungen[BedNr]["font-size"];
		document.getElementById("font-weight_feld").value = Bedingungen[BedNr]["font-weight"];
		document.getElementById("class_feld").value = Bedingungen[BedNr]["class"];
		document.getElementById("sichtbar_feld").value = Bedingungen[BedNr]["sichtbar"];
		document.getElementById("rahmenfarbe_feld").value = Bedingungen[BedNr]["Rahmenfarbe"];
		document.getElementById("border-style_feld").value = Bedingungen[BedNr]["border-style"];
		document.getElementById("rahmenbreite_feld").value = Bedingungen[BedNr]["Rahmenbreite"];
		document.getElementById("background-color_feld").value = Bedingungen[BedNr]["background-color"];
	}
}

function Bedingung_uebernehmen(BedNr) {
	BedNr_orig = BedNr;
	if (BedNr == "neu") {
		Zeile = document.createElement("tr");
		Zeile.setAttribute("class","Tabellenzeile");
		BedNr = document.getElementById("bed_Tab").firstChild.childNodes.length;
	} else {
		Zeile = document.getElementById("bed_Tab").firstChild.childNodes[BedNr];
	}
	Bedingungen[BedNr] = {};
	Bedingungen[BedNr].orig_Stil = "";
	Bedingungen[BedNr].orig_class = "";
	Zellen = "<td>" + BedNr.toString() + "</td>";
	Zellen = Zellen + '<td>' + document.getElementById("bedingung_feld").value + '</td>';
	Bedingungen[BedNr]["Bedingung"] = document.getElementById("bedingung_feld").value;
	Zellen = Zellen + '<td>' + document.getElementById("element_feld").value + '</td>';
	Bedingungen[BedNr]["Element"] = document.getElementById("element_feld").value;
	Zellen = Zellen + '<td>' + document.getElementById("kommentar_feld").value + '</td>';
	Bedingungen[BedNr]["Kommentar"] = document.getElementById("kommentar_feld").value;
	Bedingungen[BedNr]["sichtbar"] = document.getElementById("sichtbar_feld").value;
	Bedingungen[BedNr]["font-family"] = document.getElementById("font-family_feld").value;
	Bedingungen[BedNr]["font-style"] = document.getElementById("font-style_feld").value;
	Bedingungen[BedNr]["color"] = document.getElementById("color_feld").value;
	Bedingungen[BedNr]["font-size"] = document.getElementById("font-size_feld").value;
	Bedingungen[BedNr]["font-weight"] = document.getElementById("font-weight_feld").value;
	Bedingungen[BedNr]["class"] = document.getElementById("class_feld").value;
	Bedingungen[BedNr]["Rahmenfarbe"] = document.getElementById("rahmenfarbe_feld").value;
	Bedingungen[BedNr]["border-style"] = document.getElementById("border-style_feld").value;
	Bedingungen[BedNr]["Rahmenbreite"] = document.getElementById("rahmenbreite_feld").value;
	Bedingungen[BedNr]["background-color"] = document.getElementById("background-color_feld").value;
	Stil = "";
	if (Bedingungen[BedNr]["sichtbar"] == "none") {Stil = Stil + "display: none; ";}
	if (Bedingungen[BedNr]["font-family"] != "") {Stil = Stil + "font-family: " + Bedingungen[BedNr]["font-family"] + "; ";}
	if (Bedingungen[BedNr]["font-style"] != "") {Stil = Stil + "font-style: " + Bedingungen[BedNr]["font-style"] + "; ";}
	if (Bedingungen[BedNr]["color"] != "") {Stil = Stil + "color: " + hexToRgb(Bedingungen[BedNr]["color"]) + "; ";}
	if (Bedingungen[BedNr]["font-size"] != "") {Stil = Stil + "font-size: " + Bedingungen[BedNr]["font-size"] + "; ";}
	if (Bedingungen[BedNr]["font-weight"] != "") {Stil = Stil + "font-weight: " + Bedingungen[BedNr]["font-weight"] + "; ";}
	if (Bedingungen[BedNr]["Rahmenfarbe"] != "") {Stil = Stil + "border-color: " + hexToRgb(Bedingungen[BedNr]["Rahmenfarbe"]) + "; ";}
	if (Bedingungen[BedNr]["border-style"] != "") {Stil = Stil + "border-style: " + Bedingungen[BedNr]["border-style"] + "; ";}
	if (Bedingungen[BedNr]["Rahmenbreite"] != "") {Stil = Stil + "border-width: " + Bedingungen[BedNr]["Rahmenbreite"] + "; ";}
	if (Bedingungen[BedNr]["background-color"] != "") {Stil = Stil + "background-color: " + hexToRgb(Bedingungen[BedNr]["background-color"]) + "; ";}
	Klasse = "";
	if (Bedingungen[BedNr]["class"] != "") {Klasse = "class='" + Bedingungen[BedNr]["class"] + "' ";}
	Bedingungen[BedNr]["Stil"] = Stil;
	Zellen = Zellen + '<td><div ' + Klasse + 'style="' + Stil + '">' + T_Text[86] + '</div></td>';
	Zellen = Zellen + '<td><input class="Schalter_Element" type="button" value="' + T_Text[87] + '" onclick="bed_Dialog(\'' + BedNr.toString() + '\');"></td>';
	Zellen = Zellen + '<td><input class="Schalter_Element" type="button" value="' + T_Text[9] + '" onclick="bed_entfernen(\'' + BedNr.toString() + '\');"></td>';
	Zeile.innerHTML = Zellen;

	if (BedNr_orig == "neu") {
		document.getElementById("bed_Tab").firstChild.appendChild(Zeile);
	}
	
	bed_Format_Einstellung.close();
}

function bed_entfernen(BedNr) {
	document.getElementById("bed_Tab").firstChild.removeChild(document.getElementById("bed_Tab").firstChild.childNodes[BedNr]);
	delete Bedingungen[BedNr];
}

function Bed_Format_uebernehmen() {
	i = 1;
	while(Bedingungen[i] != undefined) {
		Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\'/g,"µµµ");
		Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\"/g,"µµµ");
		i = i + 1;
	}
	document.getElementById("bed_Format").value = JSON.stringify(Bedingungen);
	Bedingte_Formatierung.close();
	try {bed_Format_Einstellung.close();} catch (err) {}
}

function CSS_Dialog_oeffnen() {
	var Inhalt = '<div style="position: absolute; top: 10px; left: 10px;"><form name="CSS_Dialog">\n';
	Inhalt = Inhalt + "<div class='dtree' id='css_Baum'></div>";
	dcss = new dTree('dcss');
	dcss.add(0,-1,'Style Sheets');
	for (i = 0; i < document.styleSheets.length; i++) {
		Dateiname = document.styleSheets[i].href;
		if (Dateiname != null) {
			while (Dateiname.indexOf("/") > -1) {
				Dateiname = Dateiname.substr(Dateiname.indexOf("/") + 1,Dateiname.length);
			}
			dcss.add(i+1,0,Dateiname);
			for (x = 0; x < document.styleSheets[i].cssRules.length; x++) {
				dcss.add((i+1).toString() + "_" + x.toString(),(i+1).toString(),document.styleSheets[i].cssRules[x].selectorText,"javascript: style_zeigen(" + i + "," + x + ");");
			}
		}
	}
	Inhalt = Inhalt + "</form></div>\n";
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'CSSauswahl',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '610 345',
		headerTitle: T_Text[100],
		position: 'left-top 30 30',
		content: Inhalt,
	});
	document.getElementById("css_Baum").innerHTML= dcss.toString();
}

function style_zeigen(i,x) {
	var tempText = document.styleSheets[i].cssRules[x].cssText;
	var Text = "<div style='position: absolute; top: 10px; left: 10px;'><table cellpadding = '5' cellspaceing = '10'>";
	var Stil = tempText.substr(0,tempText.indexOf("{"));
	while (Stil.substr(0,1) == " " || Stil.substr(0,1) == ".") {
		Stil = Stil.substr(1, Stil.length);
	}
	while (Stil.substr(Stil.length - 1,Stil.length) == " ") {
		Stil = Stil.substr(0, Stil.length - 1);
	}
	tempText = tempText.substr(tempText.indexOf("{") + 1,tempText.length);
	while (tempText.length > 5) {
		tempText2 = tempText.substr(0, tempText.indexOf(";") + 1);
		tempText2 = tempText2.split(":");
		Text = Text + "<tr class='Tabellenzeile'><td>" + tempText2[0] + "</td><td>" + tempText2[1] + "</td></tr>"; 
		tempText = tempText.substr(tempText.indexOf(";") + 1,tempText.length)
	}
	Text = Text + "</table><br><br><input type='button' value='" + T_Text[0] + "' class='Schalter_Element' onclick='Stil_hinzufuegen(\"" + Stil + "\");'></div>";
	try {document.getElementById("Dialog_CSSRegel").close();} catch (err) {}

	jsPanel.create({
		dragit: {
     		snap: true
     	},
		id: 'Dialog_CSSRegel',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '330 345',
		headerTitle: T_Text[101],
		position: 'left-top 640 30',
		content: Text,
	});
}

function Stil_hinzufuegen(Stil) {
	if (document.forms.Element_Dialog.class.value.length > 0) {document.forms.Element_Dialog.class.value = document.forms.Element_Dialog.class.value + " ";}
	if(Stil.substr(-1) == " ") {Stil = Stil.substr(0,Stil.length - 1);}
	document.forms.Element_Dialog.class.value = document.forms.Element_Dialog.class.value + Stil;
}

function DBQ(DB,Funktion,Feld,Tabelle,Bedingung) {
	strReturn = "Error";
	jQuery.ajax({
		url: "DBQ.php",
		success: function (html) {
   		strReturn = html;
		},
		type: 'POST',
		data: {DB: DB,Fun: Funktion,Fel: Feld, Tab: Tabelle, Bed: Bedingung},
		async: false
	});
	return strReturn
}

function umschalten(Tab) {
	if (Tab == 1) {
		if (document.getElementById("bild").style.display == "block") {
			document.getElementById("bild").style.display = "none"
			document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("bild").style.display = "block"
			document.getElementById("schaltfl_1").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("bild").style.display = "none";
		document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 2) {
		if (document.getElementById("elemente").style.display == "block") {
			document.getElementById("elemente").style.display = "none"
			document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("elemente").style.display = "block"
			document.getElementById("schaltfl_2").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("elemente").style.display = "none";
		document.getElementById("schaltfl_2").style.backgroundColor = "#FCEDD9";
	}
	if (Tab == 3) {
		if (document.getElementById("sonstiges").style.display == "block") {
			document.getElementById("sonstiges").style.display = "none"
			document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("sonstiges").style.display = "block"
			document.getElementById("schaltfl_3").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("sonstiges").style.display = "none";
		document.getElementById("schaltfl_3").style.backgroundColor = "#FCEDD9";
	}
}

function Tab_umschalten(zeigen) {
	for (i=1; i < 4; i++) {
		if (i == zeigen) {
			document.getElementById("Tab" + i).style.display="block";
		} else {
			document.getElementById("Tab" + i).style.display="none";
		}
	}

}
function Code_Dialog(){
	var js = document.getElementById("js_code").value.replace(/§§§/g,"'");
	js = js.replace(/@@@/g,'"');
	var Inhalt = "<textarea id='code_text' style='width: 100%;'>" + js + "</textarea><input class='Schalter_Element' name='Code_übernehmen' value='" + T_Text[46] + "' type='button' onclick='Code_uebernehmen()'>";
	jsPanel.create({
		dragit: {
        	snap: true
        },
		id: 'js_code_dialog',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '1000 600',
		headerTitle: 'Code',
		position: 'left-top 300 30',
		content: Inhalt,
	});
	document.getElementById("code_text").style.height = (parseInt(document.getElementById("js_code_dialog").content.style.height) - 30) + "px";
}

function Code_uebernehmen(){
	document.getElementById("js_code").value = document.getElementById("code_text").value;
	document.getElementById("js_code").value = document.getElementById("js_code").value.replace(/'/g,"§§§");
	document.getElementById("js_code").value = document.getElementById("js_code").value.replace(/"/g,'@@@');
	document.getElementById("js_code_dialog").close();
}


