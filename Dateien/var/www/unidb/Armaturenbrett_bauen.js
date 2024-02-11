jsPanel.defaults.resizeit.minWidth = 16;
jsPanel.defaults.resizeit.minHeight = 16;
var markierte_elem = [];
var DH_Elemente=[];
var T_Text = new Array;
var Bedingungen = {};
var Auswahl = "";
var Instr2 = [];
$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	Bedingungen = JSON.parse(document.getElementById("bed_Format").value);
	i = 1;
	while(Bedingungen[i] != undefined) {
		Bedingungen[i].Bedingung = Bedingungen[i].Bedingung.replace(/\µµµ/g,"'");
		i = i + 1;
	}
	einrichten();
	var refreshId = setInterval(function() {lesen();}, 60000);
});

$(function() {
	zellen();
});

function zellen() {
	$(".column").sortable({
     	connectWith: ".column",
     	handle: ".portlet-header",
     	cancel: ".portlet-toggle",
     	placeholder: "portlet-placeholder ui-corner-all"
	});
	$(".portlet")
     	.addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
     	.find(".portlet-header")
		.addClass( "ui-widget-header ui-corner-all" )
		.prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");
	$(".portlet-toggle").on("click", function() {
     	var icon = $( this );
     	if (this.classList.contains("ui-icon-minusthick")) {
     		this.parentElement.parentElement.setAttribute("orig_hoehe",this.parentElement.parentElement.style.height);
     		this.parentElement.parentElement.style.height = "28px";
     	} else {
     		this.parentElement.parentElement.style.height = this.parentElement.parentElement.attributes["orig_hoehe"].value;
     	}
     	icon.toggleClass("ui-icon-minusthick ui-icon-plusthick");
     	icon.closest(".portlet").find(".portlet-content").toggle();
  	});
 	$(function() {
		$(".resizable").resizable();
	});
}
	
$(function(){
	T_Text = JSON.parse(document.getElementById("translation").value);
	$.contextMenu({
		selector: '.context-menu-two', 
		callback: function(key, options) {
			if (key == "Eigenschaften") {Element_Dialog_oeffnen();}
			if (key == "entfernen") {Element_entfernen();}
		},
		items: {
			"Eigenschaften": {"name": T_Text[24], "icon": "edit"},
			"entfernen": {"name": T_Text[25], "icon": "delete"}
		}
	});
});
	
function abspeichern() {
	try {Auswahl_beenden();} catch (err) {}
	for (i = 0; i < DH_Elemente.length; i++) {
		if (DH_Elemente[i].Typ == "Instrument") {
			try {document.getElementById(DH_Elemente[i].id).removeChild(document.getElementById("Deckel" + DH_Elemente[i].id));} catch (err) {}
		}
	}
	document.phpform.Inhalt.value = document.getElementById("DH_Bereich").innerHTML;
}

function Tagname_setzen(Feld){
	//undefined entfernen
	try {
		if (document.Element_Dialog.ausdruck.value=="undefined") {
			document.Element_Dialog.ausdruck.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.pfad.value=="undefined") {
			document.Element_Dialog.pfad.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.point_id.value=="undefined") {
			document.Element_Dialog.point_id.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.tagname.value=="undefined") {
			document.Element_Dialog.tagname.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.tag_id.value=="undefined") {
			document.Element_Dialog.tag_id.value = "";
		}
	} catch (err) {}
	try {
		if (document.Element_Dialog.pointname.value=="undefined") {
			document.Element_Dialog.pointname.value = "";
		}
	} catch (err) {}
	//Wenn wir es mit einem Ausdruck zu tun haben, dann die anderen Felder leeren
	try {	
		if (Feld == "ausdruck" && document.Element_Dialog.ausdruck.value > "") {
			document.Element_Dialog.point_id.value = "";
			document.Element_Dialog.tagname.value = "";
			document.Element_Dialog.pointname.value = "";
			document.Element_Dialog.tag_id.value = "";
			document.Element_Dialog.pfad.value = "";
		}
	} catch (err) {}
	//Wenn es sich um einen Tag handelt, dann das Feld Tag_ID fuellen und das Feld Ausdruck leer machen
	try {
		if (Feld == "tagname") {
			//Sollte sich in dem Feld ein % Zeichen befinden, dann wird ein Tag gesucht. In diesem Fall die Aktion hier abbrechen.
			//Ist das Feld Pfad leer, dann ebenfalls hier abbrechen.
			if (document.Element_Dialog.tagname.value.indexOf("%") > -1 || document.Element_Dialog.pfad.value == "") {return 0;}
			if (document.Element_Dialog.pfad.value.substr(document.Element_Dialog.pfad.value.length - 1,1) != "/" && document.Element_Dialog.pfad.value.length > 1) {document.Element_Dialog.pfad.value = document.Element_Dialog.pfad.value + "/";}
			jQuery.ajax({
				url: "./DH_Tag_Tag_ID.php?Tagname=" + document.Element_Dialog.pfad.value + document.Element_Dialog.tagname.value,
				success: function (html) {
					if (html > "") {
	   				document.Element_Dialog.tag_id.value = html;
	   				try {document.Element_Dialog.ausdruck.value = "";} catch (err) {}
	   			}
				},
   			async: false
   		});			
		} else {
		//Wenn Tag_ID belegt ist, dann die Felder Pfad und Tagname fuellen und Ausdruck leeren
			if (Feld == "tag_id") {
				jQuery.ajax({
					url: "./DH_Tag_Tag_ID.php?Tag_ID=" + document.Element_Dialog.tag_id.value,
					success: function (html) {
						Ergebnis = html.split(",");
	   				document.Element_Dialog.pfad.value = Ergebnis[0];						
	   				document.Element_Dialog.tagname.value = Ergebnis[1];
						try {document.Element_Dialog.ausdruck.value = "";} catch (err) {}
					},
   				async: false
   			});			
			}
		}
		jQuery.ajax({
			url: "./DH_Tag_Point_ID.php?Tag_ID="+document.Element_Dialog.tag_id.value,
			success: function (html) {
  				document.Element_Dialog.point_id.value = html;
			},
			async: false
		});
	} catch (err) {}
	try {
		if (document.Element_Dialog.tag_id.value == "") {
			jQuery.ajax({
				url: "./DH_Point_ID_Tag_ID.php?Point_ID="+document.Element_Dialog.point_id.value,
				success: function (html) {
  					document.Element_Dialog.tag_id.value = html;
				},
				async: false
			});
			jQuery.ajax({
				url: "./DH_Tag_Tag_ID.php?Tag_ID="+document.Element_Dialog.tag_id.value,
				success: function (html) {
  					if(html > ""&& html != ","){document.Element_Dialog.tagname.value = html;}
				},
				async: false
			});
		}	
	} catch (err) {}
	einheit_setzen();
}

function einheit_setzen(){
	jQuery.ajax({
		url: "./DH_Eigenschaft_Point_ID.php?Eigenschaft=EUDESC&Tag_ID="+document.Element_Dialog.tag_id.value,
		success: function (html) {
   		document.Element_Dialog.einheit.value = html;
		},
  		async: false
  	});
}

function auswaehlen(id,ausfuehren){
	if (ausfuehren == 1) {
		try {id = id.id;} catch (err) {}
		if (id == undefined) {id = Auswahl.id;}
		markiert = "";
		try {
			markiert = document.getElementsByClassName("ausgewaehlt")[0].id;
		} catch (err) {}
		if (markiert == id ) {
			Auswahl_beenden();
		} else {
			Auswahl_beenden();
			if (id.substring(0, 6) == "Deckel") {
				id = id.substring(6,);
			}
			Auswahl = document.getElementById(id);
			Auswahl.className = 'context-menu-two ausgewaehlt element ' + Auswahl.className;
			lesen();
		}
	}
}

function Elementtyp_aussuchen(){
	//Falls noch ein Element ausgewaehlt ist, dann dieses zuerst schliessen
	Auswahl = document.getElementsByClassName("ausgewaehlt")[0];
	if (Auswahl != 1) {
		try {Auswahl_beenden();} catch (err) {}
	}
	//Jetzt kommt erst die Auswahl des Elementtyps fuer das neue Element
	var Inhalt = "<div style='position: absolute; top: 10px; left: 40px;'>" + T_Text[65] + ":<br><br><select id='Typauswahlliste' name='Typauswahlliste' size='11'>";
	var Liste = document.getElementById("Typauswahl").value.split(";");
	for (i=0; i < Liste.length; i++) {
		Inhalt=Inhalt + "<option>" + Liste[i] + "</option>";
	}
	Inhalt=Inhalt + "</select><br><br><input name='schliessen' value='" + T_Text[66] + "' type='button' onclick='Element_bauen();' style='width: 75px;border-left: 0px;'></div>";
	//e.preventDefault();
	jsPanel.create({
		dragit: {
        	snap: true
		},
		headerControls: {
			size: 'xs'
		},
		id: 'neues_Element',
		theme: 'info',
		contentSize: '250 360',
		headerTitle: T_Text[67],
		position: 'left-top 300 50',
		content: Inhalt
	});
}

function Element_bauen(){
	var jetzt = new Date();
	//neue Zelle bauen
	//zuerst die Spalte mit den wenigsten Zellen suchen und dann dort eine neue Zelle anhaengen.
	Spalten = document.getElementsByClassName("column");
	Zellen = 10000;
	for (i=0; i < Spalten.length; i++) {
		if (Spalten[i].childNodes.length < Zellen) {
			Spalten_id = Spalten[i].id;
			Zellen = Spalten[i].childNodes.length;
		}
	}
	neue_Zelle = document.createElement('div');
	neue_Zelle.id = Spalten_id + "_zelle_" + (Zellen + 1).toString() + "_" + jetzt.getTime();
	neuer_Kopf = document.createElement('div');
	neuer_Kopf.setAttribute('onclick', 'auswaehlen(this,1);');
	neuer_Kopf.id = Spalten_id + "_kopf_" + (Zellen + 1).toString() + "_" + jetzt.getTime();
	neuer_Inhalt = document.createElement('div');
	neues_Resize1 = document.createElement('div');
	neues_Resize2 = document.createElement('div');
	neues_Resize3 = document.createElement('div');
	neues_Resize1.style.zIndex = "90";
	neues_Resize2.style.zIndex = "90";
	neues_Resize3.style.zIndex = "90";
	neues_Resize1.className = "ui-resizable-handle ui-resizable-e";
	neues_Resize2.className = "ui-resizable-handle ui-resizable-s";
	neues_Resize3.className = "ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se";
	neue_Zelle.className = "portlet resizable ui-widget ui-widget-content ui-helper-clearfix ui-corner-all ui-resizable";
	neuer_Kopf.className = "portlet-header ui-sortable-handle ui-widget-header ui-corner-all";
	neuer_Kopf.innerHTML = T_Text[106];
	neuer_Inhalt.className = "portlet-content";
	neuer_Inhalt.id = Spalten_id + "_inhalt_" + (Zellen + 1).toString() + "_" + jetzt.getTime();
	neuer_Inhalt.innerHTML = "<center></center>";
	neue_Zelle.appendChild(neuer_Kopf);
	neue_Zelle.appendChild(neuer_Inhalt);
	neue_Zelle.appendChild(neues_Resize1);
	neue_Zelle.appendChild(neues_Resize2);
	neue_Zelle.appendChild(neues_Resize3);
	document.getElementById(Spalten_id).appendChild(neue_Zelle);
	zellen();
	//Die Zelle ist fertig
	var Sprache = document.getElementById("sprache").value;
	var ElementTyp=document.getElementById('Typauswahlliste').value;
	ElementTyp = DBQ("unidb","","DE","Vorlagen","`" + Sprache + "` = '" + ElementTyp + "' AND `Typ` = 'Element'")
	var Eigenschaften
  	jQuery.ajax({
		url: "./Vorlage_einlesen.php?Bezeichnung=" + ElementTyp,
		success: function (html) {
	   	Eigenschaften = html;
		},
   	async: false
   });
  	
	if (Eigenschaften != undefined && Eigenschaften != null) {
		document.getElementById('neues_Element').close();
		if (ElementTyp == "Grafik") {
			var newdiv = document.createElement('img');
		} else {
			var newdiv = document.createElement('div');
		}
		newdiv.className = 'element context-menu-two';
		newdiv.setAttribute('typ', ElementTyp);
		newdiv.setAttribute('id',jetzt.getTime());
		if (ElementTyp == "Instrument") {
			newdiv.innerHTML = "<object data='Instrument.svg' type='image/svg+xml'></object>";
		}
		if (document.phpform.mobil.value == "1") {
			newdiv.setAttribute('ontouchend', 'auswaehlen(this,1);');
		} else {
			newdiv.setAttribute('onclick', 'auswaehlen(this,1);');
		}
		Eigenschaften=JSON.parse(Eigenschaften);
		//Zuerst die speziellen Eigenschaften
		for (i=0; i < Object.keys(Eigenschaften).length; i++) {
			if (Object.keys(Eigenschaften)[i].substring(0, 5) != "stil-") {
				Eigenschaft = Object.keys(Eigenschaften)[i];
				newdiv.setAttribute(Eigenschaft,Eigenschaften[Eigenschaft]);
			}
		}
		//dann der Stil
		for (i=0; i < Object.keys(Eigenschaften).length; i++) {
			if (Object.keys(Eigenschaften)[i].substring(0, 5) == "stil-") {
				var Eigenschaftname = Object.keys(Eigenschaften)[i].substring(5);
				if (Eigenschaftname=="Hoehe") {
					newdiv.style.height=Eigenschaften["stil-Hoehe"] + "px";
					newdiv.setAttribute("hoehe",newdiv.style.height);
				} else {
					if (Eigenschaftname=="Breite") {
						newdiv.style.width=Eigenschaften["stil-Breite"] + "px";
						newdiv.setAttribute("Breite",newdiv.style.width);
					} else {
						if (Eigenschaftname=="Farbe") {
							newdiv.style.backgroundColor = Eigenschaften["stil-Farbe"];
						} else {
							if (Eigenschaftname=="Schriftgroesse") {
								newdiv.style.fontSize = Eigenschaften["stil-Schriftgroesse"] + "px";
							} else {
								if (Eigenschaftname=="unterstrichen") {
									if (Eigenschaften["stil-unterstrichen"] == "1") {
										newdiv.style.textDecoration = "underline";
									} else {
										newdiv.style.textDecoration = "none";
									}
								} else {
									if (Eigenschaftname=="fett") {
										if (Eigenschaften["stil-fett"] == "1") {
											newdiv.style.fontWeight = "bold";
										} else {
											newdiv.style.fontWeight = "normal";
										}
									} else {
										if (Eigenschaftname=="kursiv") {
											if (Eigenschaften["stil-kursiv"] == "1") {
												newdiv.style.fontStyle = "italic";
											} else {
												newdiv.style.fontStyle = "normal";
											}
										} else {
											if (Eigenschaftname=="Schriftart") {
												newdiv.style.fontFamily = Eigenschaften["stil-Schriftart"];
											} else {
												if (Eigenschaftname=="Schriftfarbe") {
													newdiv.style.color = Eigenschaften["stil-Schriftfarbe"];
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	neuer_Inhalt.firstChild.appendChild(newdiv);
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
	einrichten();
}

function Eingabefeld_sichtbar_dialog() {
	jQuery.ajax({
		url: "Test_Tag_suchen.php?Suchtext=" + document.Element_Dialog.tagname.value,
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
	ausgew = Auswahl;
	//div editieren
	for (i=0; i < ausgew.attributes.length; i++) {
		if (ausgew.attributes[i].name!="style" && ausgew.attributes[i].name!="class" && ausgew.attributes[i].name!="id" && ausgew.attributes[i].name!="typ" && ausgew.attributes[i].name!="onclick" && ausgew.attributes[i].name!="oncontextmenu" && ausgew.attributes[i].name!="ontouchend") {
			try {ausgew.setAttribute(ausgew.attributes[i].name, document.getElementById(ausgew.attributes[i].name).value);} catch (err) {}
		}
		if (ausgew.attributes[i].name == "style"){
			try {ausgew.style.backgroundColor = document.Element_Dialog.backgroundColor.value;} catch (err) {}
			try {ausgew.style.fontFamily = document.Element_Dialog.fontFamily.value;} catch (err) {}
			try {ausgew.style.fontWeight = document.Element_Dialog.fett.value;} catch (err) {}
			try {ausgew.style.fontSize = document.Element_Dialog.fontSize.value;} catch (err) {}
			try {ausgew.style.color = document.Element_Dialog.color.value;} catch (err) {}
			try {ausgew.style.fontStyle = document.Element_Dialog.fontStyle.value;} catch (err) {}
			try {ausgew.style.textDecoration = document.Element_Dialog.textDecoration.value;} catch (err) {}
		}
	}
	//Falls es sich um einen Text handelt, dann den Textinhalt in das DIV einsetzen
	try {ausgew.innerHTML = document.Element_Dialog.textinhalt.value;} catch (err) {}
	//Falls es sich um einen Link handelt, dann den Textinhalt und den URL in das DIV einsetzen
	try {ausgew.innerHTML = "<a href='" + document.Element_Dialog.link.value + "'>" + document.Element_Dialog.textinhalt.value + "</a>";} catch (err) {}
	try {ausgew.innerHTML=document.Element_Dialog.inhalt.value;} catch (err) {}
	for (i=0; i < document.Element_Dialog.elements.length; i++) {
		if (document.Element_Dialog.elements[i].type == "checkbox") {
			if (document.Element_Dialog.elements[i].checked == false){
				if (document.Element_Dialog.elements[i].name == "fontWeight" || document.Element_Dialog.elements[i].name == "fontStyle") {
					if (document.Element_Dialog.elements[i].name == "fontStyle") {					
						ausgew.style.fontStyle = "normal";
					} else {
						ausgew.style.fontWeight = "normal";
					}
				} else {
					try {ausgew.attributes.getNamedItem(document.Element_Dialog.elements[i].name).value = "0";} catch (err) {}
				}
			} else {
				if (document.Element_Dialog.elements[i].name == "verlinken") {
					//Verlinkung einbauen verlinken wird auf 2 gesetzt, damit die Links im Entwurfsmodus noch nicht aktiv sind
					ausgew.attributes.verlinken.value = "2";
				} else {
					if (document.Element_Dialog.elements[i].name == "fontWeight") {
						ausgew.style.fontWeight = "bold";
					} else {
						if (document.Element_Dialog.elements[i].name == "fontStyle") {
							ausgew.style.fontStyle = "italic";
						} else {
							ausgew.attributes.getNamedItem(document.Element_Dialog.elements[i].name).value = "1";
						}
					}
				}
			}
		}
	}
	if (ausgew.attributes.typ.value == "Instrument 2") {	
		Instr2[ausgew.id].setMaxValue(document.Element_Dialog.elements["max"].value);
		Instr2[ausgew.id].setMinValue(document.Element_Dialog.elements["min"].value);
	}
	einrichten();
	try {Elementeinstellungen.close();} catch (err) {}
}

function Kopfzeile_dialog_oeffnen() {
	var Inhalt = '<div style="position: relative; top: 10px; left: 10px;"><form id="bezeichnung_dialog" name="Bezeichnung_Dialog">' + T_Text[106] + ':&nbsp;<input class="Text_Element" name="ABname" size="40" value="' + document.getElementById(Auswahl.id).innerText + '" type="text"><br><br>';
	Inhalt = Inhalt + "<input class='Schalter_Element' type='button' value='" + T_Text[46] + "' onclick='Bezeichnung_uebernehmen()'></form></div>";
	jsPanel.create({
		dragit: {
   	 	snap: true
	  	},
		id: 'bezeichnungseinstellung',
		theme: 'info',
		headerControls: {
			size: 'xs'
		},
		contentSize: '420 115',
		headerTitle: T_Text[106],
		position: 'left-top 65 60',
		contentOverflow: 'scroll scroll',
		content: Inhalt
	});
}	

function Bezeichnung_uebernehmen() {
	document.getElementById(Auswahl.id).innerText = document.Bezeichnung_Dialog.ABname.value;
	bezeichnungseinstellung.close();
}

function Element_Dialog_oeffnen() {
	if (Auswahl.id.indexOf("kopf") > -1) {
		Kopfzeile_dialog_oeffnen();
	} else {
		ausgew = Auswahl;
		var DialogHoehe=0;
		var Inhalt = "<form id='Element_Dialog' name='Element_Dialog'><table style='width: 140px'>";
		//spezifische Eigenschaften
		try {
			//zuerst die speziellen Eigenschaften
			for (i=0; i < ausgew.attributes.length; i++) {
				if (ausgew.attributes[i].name!="style" && ausgew.attributes[i].name.substring(0, 5) != "stil-") {
					if (ausgew.attributes[i].name == "textinhalt" || ausgew.attributes[i].name == "link") {
						Feldname = "Text";
						if (ausgew.attributes[i].name == "link") {Feldname = "Link";}
						Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td><textarea class='Text_Element' style='width: 160px;' id='" + ausgew.attributes[i].name + "' name='" + ausgew.attributes[i].name + "'>" + ausgew.attributes[i].value + "</textarea></td></tr>";
						DialogHoehe=DialogHoehe + 31;
					} else {
						if (ausgew.attributes[i].name == "ausdruck" || ausgew.attributes[i].name == "tagname" || ausgew.attributes[i].name == "pfad"){
							if (ausgew.attributes[i].name == "ausdruck") {Feldname = T_Text[27];}
							if (ausgew.attributes[i].name == "tagname") {Feldname = "Tagname";}
							if (ausgew.attributes[i].name == "pfad") {Feldname = T_Text[28];}
							Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td><textarea class='Text_Element' style='width: 160px;' id='" + ausgew.attributes[i].name + "' name='" + ausgew.attributes[i].name + "' onfocusout='Tagname_setzen(\""+ausgew.attributes[i].name+"\")' onblur='Tagname_setzen(\""+ausgew.attributes[i].name+"\")'>" + ausgew.attributes[i].value + "</textarea></td></tr>";
							DialogHoehe = DialogHoehe + 31;
						} else {
							if (ausgew.attributes[i].name=="verlinken" || ausgew.attributes[i].name=="aktualisieren" || ausgew.attributes[i].name=="wert_anzeigen" || ausgew.attributes[i].name=="feine_teilung") {
								Feldname = ausgew.attributes[i].name;
								if (ausgew.attributes[i].name == "wert_anzeigen") {Feldname = T_Text[29];}
								if (ausgew.attributes[i].name == "feine_teilung") {Feldname = T_Text[30];}
								if (ausgew.attributes[i].name == "aktualisieren") {Feldname = T_Text[71];}
								if (ausgew.attributes[i].name == "verlinken") {Feldname = T_Text[72];}
								if (ausgew.attributes[i].name == "elementname") {Feldname = "Elementname";}
								Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td width = '50px'><input class='Text_Element' type='checkbox' id='" + ausgew.attributes[i].name + "' name='" + ausgew.attributes[i].name + "' value='" + ausgew.attributes[i].value + "'";
								if (ausgew.attributes[i].value > "0" && ausgew.attributes[i].value != "normal" && ausgew.attributes[i].value != "none"){
									Inhalt += " checked></checkbox></td></tr>";
								} else {
									Inhalt += "></checkbox></td></tr>";
								}
							} else {
								if (ausgew.attributes[i].name=="pointname" || ausgew.attributes[i].name=="point_id") {
									Inhalt += "<input class='Text_Element' id='" + ausgew.attributes[i].name+"' name='" + ausgew.attributes[i].name + "' value='" + ausgew.attributes[i].value + "' type='hidden'>";
								} else {
									if (ausgew.attributes[i].name=="zeitraum") {
										Inhalt += "<tr><td style='text-align: right'><input class='Text_Element' type='button' name='Zeitraum_Schalter' value = '" + T_Text[31] + "' onclick='Zeitraum_dialog()'" + "</td><td width = '50px'><input class='Text_Element' id='" + ausgew.attributes[i].name + "' name='" + ausgew.attributes[i].name + "' value='" + ausgew.attributes[i].value + "' type='text'></td></tr>";
									} else {
										if (ausgew.attributes[i].name=="class" || ausgew.attributes[i].name=="id" || ausgew.attributes[i].name=="typ" || ausgew.attributes[i].name=="onclick" || ausgew.attributes[i].name=="title" || ausgew.attributes[i].name=="ontouchend") {
											Inhalt += "<input class='Text_Element' id='" + ausgew.attributes[i].name + "' name='" + ausgew.attributes[i].name + "' value='" + ausgew.attributes[i].value + "' type='hidden'>";
										} else {
											if (DialogHoehe<661) {
												DialogHoehe=DialogHoehe+31;
											}
											if (ausgew.attributes[i].name=="gruppe") {
												Inhalt = Inhalt + "<tr><td align='right'>" + T_Text[32] + "</td><td><select class='ausgew_Liste_Element' size='1' id='gruppe' name='Gruppe'>";
												Multistates = document.getElementById("multistates").value.split(";");
												for (x = 0; x < Multistates.length; x++) {
													Multistate = Multistates[x].split(",");
													Inhalt = Inhalt + "<option value='" + Multistate[0] + "'";
													if (Multistate[0] == ausgew.attributes[i].value) {
														Inhalt = Inhalt + " selected";
													}
													Inhalt = Inhalt + ">" + Multistate[1] + "</option>";
												}
												Inhalt = Inhalt + "</select></td></tr>";
											} else {
												if (ausgew.attributes[i].name != "oben" && ausgew.attributes[i].name != "links" && ausgew.attributes[i].name != "farbe" && ausgew.attributes[i].name != "hoehe" && ausgew.attributes[i].name != "breite") {
													Feldname = ausgew.attributes[i].name;
													if (Feldname == "einheit") {Feldname = T_Text[33];}
													if (Feldname == "tag_id") {Feldname = "Tag_ID";}
													if (Feldname == "zeitraum") {Feldname = T_Text[31];}
													Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td width = '50px'><input class='Text_Element' style='width: 160px;' id='" + ausgew.attributes[i].name+"' name='" + ausgew.attributes[i].name + "' value='" + ausgew.attributes[i].value + "' type='text'></td></tr>";
													if (ausgew.attributes[i].name=="tag_id") {
														Inhalt = Inhalt.substr(0, Inhalt.length - 11);
														Inhalt += " onfocusout='Tagname_setzen(\""+ausgew.attributes[i].name+"\")' onblur='Tagname_setzen(\""+ausgew.attributes[i].name+"\")'></td></tr>";
													}	
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			//dann die Stil Eigenschaften
			Eigenschaften = ausgew.attributes.style.value.split("; ");
			var trifftzu = 0;
			for (i=0; i < Eigenschaften.length; i++) {
				if (Eigenschaften[i].substr(Eigenschaften[i].length - 1, 1) == ";") {Eigenschaften[i] = Eigenschaften[i].substr(0, Eigenschaften[i].length - 1);}
				if (DialogHoehe<661) {
					DialogHoehe=DialogHoehe+31;
				}
				Eigenschaft = Eigenschaften[i].split(": ");
				if (Eigenschaft[0]=="font-family") {Eigenschaft[0]="fontFamily";}
				if (Eigenschaft[0]=="font-weight") {Eigenschaft[0]="fontWeight";}
				if (Eigenschaft[0]=="font-size") {Eigenschaft[0]="fontSize";}
				if (Eigenschaft[0]=="font-style") {Eigenschaft[0]="fontStyle";}
				if (Eigenschaft[0]=="text-decoration") {Eigenschaft[0]="textDecoration";}
				if (Eigenschaft[0]=="background-color") {Eigenschaft[0]="backgroundColor";}
				if (Eigenschaft[0]=="fontWeight" || Eigenschaft[0]=="textDecoration" || Eigenschaft[0]=="fontStyle") {
					trifftzu = 0;		
					if (Eigenschaft[0]=="fontWeight") {
						Feldname = T_Text[34];
						if (Eigenschaft[1] == "bold") {trifftzu = 1;}						
					}
					if (Eigenschaft[0]=="textDecoration") {
						Feldname = T_Text[35];
						if (Eigenschaft[1] == "underline") {trifftzu = 1;}
					}
					if (Eigenschaft[0]=="fontStyle") {
						Feldname = T_Text[36];
						if (Eigenschaft[1] == "italic") {trifftzu = 1;}
					}
					if (trifftzu == 1) {
						Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td width = '50px'><input class='Text_Element' type='checkbox' id='" + Eigenschaft[0] + "' name='" + Eigenschaft[0] + "' value='1' checked></checkbox></td></tr>";
					} else {
						Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td width = '50px'><input class='Text_Element' type='checkbox' id='" + Eigenschaft[0] + "' name='" + Eigenschaft[0] + "' value='0'></checkbox></td></tr>";
					}
				} else {
					Feldname = Eigenschaft[0];
					if (Eigenschaft[0] == "backgroundColor" || Eigenschaft[0] == "color") {
						if (Eigenschaft[0] == "backgroundColor") {
							Feldname = T_Text[37];
						} else {
							Feldname = T_Text[38];
						}
						Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td width = '50px'><input class='Text_Element' id='" + Eigenschaft[0] + "' name='" + Eigenschaft[0] + "' value='" + rgbToHex(Eigenschaft[1]) + "' type='text' style='width: 40px;'>&nbsp;&nbsp;&nbsp;&nbsp;<input class='Text_Element' id='" + Eigenschaft[0] + "_h' name='" + Eigenschaft[0] + "_h' value='" + rgbToHex(Eigenschaft[1]) + "' type='color' onchange='document.getElementById(Eigenschaft[0]).value = document.getElementById(Eigenschaft[0] + \"_h\").value;'></td></tr>";
					} else {
						if (Eigenschaft[0] == "fontFamily") {
							Feldname = T_Text[39];
							if (Eigenschaft[1] == "arial, verdana, helvetica, sans-serif") {
								var Schriftliste = "<option value='arial, verdana, helvetica, sans-serif' selected>arial, verdana, helvetica, sans-serif</option>\n";
							} else {
								var Schriftliste = "<option value='arial, verdana, helvetica, sans-serif'>arial, verdana, helvetica, sans-serif</option>\n";
							}
							if (Eigenschaft[1] == "times new roman, times, serif") {
								Schriftliste += "<option value='times new roman, times, serif' selected>roman, 'times new roman', times, serif</option>\n";
							} else {
								Schriftliste += "<option value='times new roman, times, serif'>roman, 'times new roman', times, serif</option>\n";
							}
							if (Eigenschaft[1] == "courier, fixed, monospace") {
								Schriftliste += "<option value='courier, fixed, monospace' selected>courier, fixed, monospace</option>\n";
							} else {
								Schriftliste += "<option value='courier, fixed, monospace'>courier, fixed, monospace</option>\n";
							}
							if (Eigenschaft[1] == "western, fantasy") {
								Schriftliste += "<option value='western, fantasy' selected>western, fantasy</option>\n";
							} else {
								Schriftliste += "<option value='western, fantasy'>western, fantasy</option>\n";
							}
							if (Eigenschaft[1] == "Zapf-Chancery, cursive") {
								Schriftliste += "<option value='Zapf-Chancery, cursive' selected>Zapf-Chancery, cursive</option>\n";
							} else {
								Schriftliste += "<option value='Zapf-Chancery, cursive'>Zapf-Chancery, cursive</option>\n";
							}
							if (Eigenschaft[1] == "serif") {
								Schriftliste += "<option value='serif' selected>serif</option>\n";
							} else {
								Schriftliste += "<option value='serif'>serif</option>\n";
							}
							if (Eigenschaft[1] == "sans-serif") {
								Schriftliste += "<option value='sans-serif' selected>sans-serif</option>\n";
							} else {
								Schriftliste += "<option value='sans-serif'>sans-serif</option>\n";
							}
							if (Eigenschaft[1] == "monospace") {
								Schriftliste += "<option value='monospace' selected>monospace</option>\n";
							} else {
								Schriftliste += "<option value='monospace'>monospace</option>\n";
							}
 							Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td width = '50px'><select class='Text_Element' name='"+Eigenschaft[0]+"' value='" + Eigenschaft[1] + "'>" + Schriftliste + "</select></td></tr>";
						} else {
							if (Eigenschaft[0] == "fontSize") {Feldname = T_Text[40];}
							if (Eigenschaft[0] != "left" && Eigenschaft[0] != "top" && Eigenschaft[0] != "height" && Eigenschaft[0] != "width") {				
								Inhalt += "<tr><td style='text-align: right'>" + Feldname + "</td><td width = '50px'><input class='Text_Element' name='"+Eigenschaft[0]+"' value='" + Eigenschaft[1] + "' type='text'></td></tr>";
							}
						}
					}
				}
			}
		} catch (err) {}
		Inhalt += "<tr style='height: 40px;'><td><input class='Text_Element' type='button' name='Tag_suchen_Schalter' value='" + T_Text[45] + "' onclick='Eingabefeld_sichtbar_dialog()'></td><td><input class='Text_Element' type='button' name='uebernehmen' value='" + T_Text[46] + "' onclick='Element_Dialog_uebernehmen()'></td></tr></table></form>";
		jsPanel.create({
			dragit: {
     		 	snap: true
	     	},
			id: 'Elementeinstellungen',
			theme: 'info',
			headerControls: {
				size: 'xs'
			},
			contentSize: '350 ' + DialogHoehe.toString(),
			headerTitle: T_Text[70],
			position: 'left-top 65 60',
			contentOverflow: 'scroll scroll',
			content: Inhalt
		});
		//ggf ergaenzen aus den Voreinstellungen
		var Eigenschaften;
		var Meldung = 0;
		var bla;
		//ausgew = document.getElementsByClassName("jsPanel-content")[0].firstChild;
 		jQuery.ajax({
			url: "./Vorlage_einlesen.php?Bezeichnung=" + ausgew.attributes.typ.value,
			success: function (html) {
   			Eigenschaften = html;
			},
  			async: false
	 	});
		//$("#textinhalt").htmlarea();
		//Eventuell vordefinierte Eigenschaften dem Element hinzufügen, falls es diese noch nicht hat.
		if (Eigenschaften != undefined && Eigenschaften != null) {
			Meldung = 0;
			Eigenschaften=JSON.parse(Eigenschaften);
			for (i=0; i < Object.keys(Eigenschaften).length; i++) {
				var Eigenschaftname = Object.keys(Eigenschaften)[i];
				if (Eigenschaftname.substring(0, 5) == "stil-") {
					if (Eigenschaftname == "stil-fett") {
						try {
							if (ausgew.style.fontWeight == ""){
								ausgew.style.fontWeight = Eigenschaften[Eigenschaftname];
								Meldung = 1;
							};
						} catch (err) {
							ausgew.style.fontWeight = Eigenschaften[Eigenschaftname];
							Meldung = 1;
						}
					} else {
						if (Eigenschaftname == "stil-Farbe") {
							try {
								bla = ausgew.style.backgroundColor;
							} catch (err) {
								ausgew.style.backgroundColor = Eigenschaften[Eigenschaftname];
								Meldung = 1;
							}
						} else {
							if (Eigenschaftname == "stil-kursiv") {
								try {
									if (ausgew.style.fontStyle == ""){
										ausgew.style.fontStyle = Eigenschaften[Eigenschaftname];
										Meldung = 1;
									}
								} catch (err) {
									ausgew.style.fontStyle = Eigenschaften[Eigenschaftname];
									Meldung = 1;
								}
							} else {
								if (Eigenschaftname == "stil-Schriftart") {
									try {
										if (ausgew.style.fontFamily == ""){
											ausgew.style.fontFamily = Eigenschaften[Eigenschaftname];
											Meldung = 1;
										}
									} catch (err) {
										ausgew.style.fontFamily = Eigenschaften[Eigenschaftname];
										Meldung = 1;
									}
								} else {
									if (Eigenschaftname == "stil-Schriftfarbe") {
										try {
											if (ausgew.style.color == ""){
												ausgew.style.color = Eigenschaften[Eigenschaftname];
												Meldung = 1;
											}
										} catch (err) {
											ausgew.style.color = Eigenschaften[Eigenschaftname];
											Meldung = 1;
										}
									} else {
										if (Eigenschaftname == "stil-unterstrichen") {
											try {
												if (ausgew.style.textDecoration == ""){
													ausgew.style.textDecoration = Eigenschaften[Eigenschaftname];
													Meldung = 1;
												}
											} catch (err) {
												ausgew.style.textDecoration = Eigenschaften[Eigenschaftname];
												Meldung = 1;
											}
										} else {
											if (Eigenschaftname == "stil-Schriftgroesse") {
												try {
													if (ausgew.style.fontSize == ""){
														ausgew.style.fontSize = Eigenschaften[Eigenschaftname] + "px";
														Meldung = 1;
													}
												} catch (err) {
													ausgew.style.fontSize = Eigenschaften[Eigenschaftname] + "px";
													Meldung = 1;
												}
											}
										}
									}
								}
							}
						}
					}
				} else {
					erster_Buchst_gr = Eigenschaftname.substr(0,1).toUpperCase();
					erster_Buchst_kl = Eigenschaftname.substr(0,1).toLowerCase();
					Restname = Eigenschaftname.substr(1,Eigenschaftname.length - 1);
					Name_gr = erster_Buchst_gr + Restname;
					Name_kl = erster_Buchst_kl + Restname;
					try {
						bla = ausgew.attributes.getNamedItem(Name_kl).value;
					} catch (err) {
						try{
							bla = ausgew.attributes.getNamedItem(Name_gr).value;
						} catch (err) {
							ausgew.setAttribute(Eigenschaftname, Eigenschaften[Eigenschaftname]);
							Meldung = 1;
						}
					}
				}
			}
		}
		try {
			if ((ausgew.attributes.getNamedItem("tagname").value == "" || ausgew.attributes.getNamedItem("tag_id").value == "") && ausgew.attributes.getNamedItem("ausdruck").value == "") {
				try {
					jQuery.ajax({
						url: "./DH_Point_ID_Tag_ID.php?Point_ID=" + ausgew.attributes.getNamedItem("point_id").value,
						success: function (html) {
  							ausgew.attributes.getNamedItem("tag_id").value = html;
						},
 							async: false
 						});
					jQuery.ajax({
						url: "./DH_Tag_Tag_ID.php?Tag_ID="+ausgew.attributes.getNamedItem("tag_id").value,
						success: function (html) {
  							ausgew.attributes.getNamedItem("tagname").value = html;
						},
 							async: false
	 					});
				} catch (err) {}
			}
			if (Meldung == 1) {
				try {Elementeinstellungen.close();} catch (err) {}
				Element_Dialog_oeffnen();
			}
		} catch (err) {}
	}
}

function Bild_Dialog_oeffnen() {
	var Inhalt = '<div style="background: #FCEDD9; width: 720px; height: 250px;"><div style="position: absolute; top: 10px; left: 10px"><form name="Bild_Dialog"><table>\n';
	Inhalt = Inhalt + '<tr height="40px"><td class="Text_einfach" style="text-align: right">' + T_Text[106] + '</td><td colspan="2"><input class="Text_Element" name="Bildname" size="40" value="' + document.phpform.Bezeichnung.value + '" type="text"></td></tr>\n';
	Inhalt = Inhalt + '<tr height="40px"><td class="Text_einfach" style="text-align: right">' + T_Text[103] + '</td><td colspan="2"><input class="Text_Element" id="hintergrundfarbe_dialog" name="Hintergrundfarbe" value="' + document.phpform.Hintergrundfarbe.value + '" type="text" style="width: 40px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="Text_Element" id="hintergrundfarbe_dialog_h" name="Hintergrundfarbe_h" value="' + document.phpform.Hintergrundfarbe.value + '" type="color" onchange="document.getElementById(\'hintergrundfarbe_dialog\').value = document.getElementById(\'hintergrundfarbe_dialog_h\').value;"></td></tr>\n';
	Inhalt = Inhalt + '<tr style="height: 40px;"><td class="Text_einfach" style="text-align: right">' + T_Text[52] + '</td><td><input class="Schalter_Element" name="übernehmen" value="' + T_Text[53] + '" type="button" onclick="Bildeinstellungen_uebernehmen()"></td></tr>\n';
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
		contentSize: '420 200',
		headerTitle: T_Text[55],
		position: 'left-top 30 30',
		contentOverflow: 'hidden',
		content: Inhalt,
	});
}

function Bildeinstellungen_uebernehmen() {
	document.phpform.Bezeichnung.value=document.Bild_Dialog.Bildname.value;
	document.phpform.Hintergrundfarbe.value=document.Bild_Dialog.Hintergrundfarbe.value;
	try {Bildeinstellungen.close();} catch (err) {}
	try {Bildeinstellungen2.close();} catch (err) {}
}

function Element_entfernen() {
	Auswahl = document.getElementsByClassName("ausgewaehlt");
	if (Auswahl[0].id.indexOf("kopf") > -1) {
		Auswahl = document.getElementsByClassName("ausgewaehlt")[0].parentElement;
		Auswahl.parentElement.removeChild(Auswahl);
	} else {
		Auswahl[0].parentElement.removeChild(Auswahl[0]);
	}
}

function Auswahl_beenden() {
	try {
		ausgewaehlt = document.getElementsByClassName("jsPanel-content")[0];
		for (x = 0; x < ausgewaehlt.childNodes.length; x++) {
			document.getElementById("DH_Bereich").appendChild(ausgewaehlt.firstChild);
		}
		document.body.removeChild(document.getElementById("ausgewaehlt"));
	} catch (err) {}
 	try{
 		ausgewaehlt = document.getElementsByClassName("ausgewaehlt")
 		for (x = 0; x < ausgewaehlt.length; x++) {
			try {
				ausgewaehlt[x].style.height = (parseInt(ausgewaehlt[x].parentElement.parentElement.parentElement.style.height) - 45).toString() + "px";
				ausgewaehlt[x].style.width = (parseInt(ausgewaehlt[x].parentElement.parentElement.parentElement.style.width) - 20).toString() + "px";
				if (ausgewaehlt[x].hasAttribute("breite")) {
					ausgewaehlt[x].setAttribute("breite",ausgewaehlt[x].style.width);
					ausgewaehlt[x].setAttribute("hoehe",ausgewaehlt[x].style.height);
				}
			} catch (err) {}
			Liste = "";
			for (i = 0; i < ausgewaehlt[x].classList.length; i++) {
				if (ausgewaehlt[x].classList[i] != "ausgewaehlt" && ausgewaehlt[x].classList[i] != "context-menu-two") {
					Liste = Liste + ausgewaehlt[x].classList[i] + " ";
				}
			}
			ausgewaehlt[x].classList = Liste;
		}
	} catch (err) {}
	einrichten();
	try {Elementeinstellungen.close();} catch (err) {}
}

function einrichten() {
	DH_Elemente=[];
	var ausgewaehlt;
	//Elementliste als Objekt - Array erstellen
	for (i = 0;i < document.getElementsByClassName("element").length;i++) {
		ausgewaehlt = document.getElementsByClassName("element")[i];
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
			try {Punkt = ausgewaehlt.attributes.point_id.value;} catch (err) {try {Punkt = ausgewaehlt.attributes.tag_id.value;} catch (err) {}}
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
			if (Eigenschaften.Typ == "Instrument 2") {
				try {document.getElementById(Eigenschaften.id).removeChild(document.getElementById(Eigenschaften.id).firstChild);} catch (err) {}
				Instr2[Eigenschaften.id] = Gauge(
					document.getElementById(Eigenschaften.id), {
   	  				dialStartAngle: 180,
	   		      dialEndAngle: 0,
	   		      min: Eigenschaften.Min,
	   		      max: Eigenschaften.Max,
         			value: 0,
			   	  	viewBox: "0 0 100 57",
	        			color: function(value) {return "#5ee432";}
					}
				); 
			}
			DH_Elemente.push(Eigenschaften);
		} catch (err) {}
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
				newdiv.style = DH_Elemente[i].Element.style;
				newdiv.style.position = "absolute";
				newdiv.style.top = "25px";
				newdiv.style.left = "0px";
				newdiv.style.zIndex=parseFloat(DH_Elemente[i].Element.style.zIndex) + 1;
				newdiv.innerHTML="<img src='Multistates/leer.png' width='" + (DH_Elemente[i].Element.parentElement.clientWidth).toString() + "px" + "' height='" + (DH_Elemente[i].Element.parentElement.clientHeight).toString() + "px" + "'>";
				if (document.phpform.mobil.value == "1") {
					newdiv.setAttribute('ontouchend', 'auswaehlen(this);');
				} else {
					newdiv.setAttribute('onclick', 'auswaehlen(this);');
				}
				DH_Elemente[i].Element.appendChild(newdiv);
			} else {
				Deckel.style = DH_Elemente[i].Element.style;
				Deckel.style.position = "absolute";
				Deckel.style.top = "25px";
				Deckel.style.left = "0px";
				Deckel.style.width = (Deckel.parentElement.parentElement.parentElement.parentElement.clientWidth).toString() + "px";
				Deckel.style.height = (Deckel.parentElement.parentElement.parentElement.parentElement.clientHeight - 25).toString() + "px";
			}
		}
	}
	lesen();
}

function lesen() {
	for (i = 0; i < DH_Elemente.length; i++) {
		if (DH_Elemente[i].aktualisieren=="1"){		
			var strReturn = "";
			try {var ausdruck = encodeURIComponent(DH_Elemente[i].Ausdruck);} catch (err) {ausdruck = "";}
			if (DH_Elemente[i].Typ == "Instrument 2") {
				var strURL = "Wert.php?Point_ID=" + DH_Elemente[i].Punkt + "&Einheit=" + DH_Elemente[i].Einheit + "&Typ=Tag&Zeitpunkt=jetzt&Ausdruck="+ausdruck;
			} else {
				var strURL = "Wert.php?Point_ID="+DH_Elemente[i].Punkt+"&Einheit="+DH_Elemente[i].Einheit+"&wert_anzeigen="+DH_Elemente[i].wert_anzeigen+"&Typ="+DH_Elemente[i].Typ+"&Zeitpunkt=jetzt&Breite="+DH_Elemente[i].Breite+"&Hoehe="+DH_Elemente[i].Hoehe+"&Zeitraum="+DH_Elemente[i].Zeitraum+"&Gruppe="+DH_Elemente[i].Gruppe+"&Ausdruck="+ausdruck+"&min="+DH_Elemente[i].Min+"&max="+DH_Elemente[i].Max;
			}
			jQuery.ajax({
				url: strURL,
				success: function (html) {
         	   strReturn = html;
	        	},
   	     	async: false
    		});
			strReturn=strReturn.split("@");
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
					Instr2[DH_Elemente[i].id].setMaxValue(parseFloat(DH_Elemente[i].Max));
					Instr2[DH_Elemente[i].id].setMinValue(parseFloat(DH_Elemente[i].Min));
					Instr2[DH_Elemente[i].id].setValue(wert);
				} else {
					document.getElementById(DH_Elemente[i].id).innerHTML=strReturn[0];
				}
				try {document.getElementById(DH_Elemente[i].id).attributes.title.value=strReturn[1];} catch (err) {}
			}
		}
	}
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
	document.getElementById("zeitraum").value = Zeit;
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
