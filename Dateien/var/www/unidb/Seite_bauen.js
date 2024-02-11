jsPanel.defaults.resizeit.minWidth = 30;
jsPanel.defaults.resizeit.minHeight = 30;
var T_Text = new Array;
var editnr = 0;
var ausgew = "";
var MausStartX = 0;
var MausStartY = 0;

$(window).on('load',function() {
	T_Text = JSON.parse(document.getElementById("translation").value);
	document.getElementById("Inhalt").setAttribute("ondragover","allowDrop(event)");
//	document.getElementById("Inhalt").setAttribute("ondblclick","Auswahl_beenden()");
	document.getElementById("Inhalt").style.height = "96vh";
});

document.addEventListener('dragstart', function(event) {
	event.dataTransfer.setData('Text', event.target.id);
	MausStartY = event.clientY;
	MausStartX = event.clientX;
});

document.addEventListener('drop', function (event) {
	event.preventDefault();
	let data = event.dataTransfer.getData('text');
	document.getElementById(data).style.top = (parseInt(document.getElementById(data).style.top) + event.clientY - MausStartY).toString() + "px";
	document.getElementById(data).style.left = (parseInt(document.getElementById(data).style.left) + event.clientX - MausStartX).toString() + "px";
});

//document.addEventListener('jspanelbeforeclose', handler, false);

function allowDrop(ev) {
	ev.preventDefault();
}

$(function(){
	$.contextMenu({
		selector: '.context-menu',
		callback: function(key, options) {
			if (key == "bearbeiten") {Dialog_oeffnen();}
			if (key == "entfernen") {entfernen();}
		},
		items: {
			"bearbeiten": {"name": T_Text[2], "icon": "edit"},
			"entfernen": {"name": T_Text[3], "icon": "delete"},
		}
	});
});

function speichern() {
	try {Auswahl_beenden();} catch (err) {}
	document.phpform.HTML_Text.value = document.getElementById('Inhalt').innerHTML;
}

function entfernen() {
	document.getElementById("Inhalt").removeChild(document.getElementById(ausgew));
}

function Element_bauen() {
	// die bisherige Markierung entfernen
	try {
		if (document.phpform.ausgewaehlt.value > '') {
			var Auswahl=document.getElementById("Objekt_"+document.phpform.ausgewaehlt.value);
			Auswahl.style.border="1px black dotted";
		}
	} catch (err) {}
	var Inhalt=document.getElementById('Inhalt');
	var newdiv = document.createElement('div');
	Inhalt.appendChild(newdiv);
	newdiv.style.width="300px";
	newdiv.style.position="absolute";
	newdiv.style.top="100px";
	newdiv.style.left="500px";
	newdiv.style.height="300px";
	newdiv.setAttribute('onclick', 'auswaehlen(this);');
	newdiv.setAttribute('ontouchend', 'auswaehlen(this);');
	document.phpform.max_ID.value=Number(document.phpform.max_ID.value)+1;
	newdiv.setAttribute('id', 'Objekt_'+document.phpform.max_ID.value);
	newdiv.style.border="1px black dotted";
	document.phpform.HTML_Text.value=Inhalt.innerHTML;
	document.phpform.ausgewaehlt.value=document.phpform.max_ID.value;
	auswaehlen();
	Dialog_oeffnen();
}

function Dialog_oeffnen() {
	Auswahl = document.getElementById(ausgew);
	try {
		jsPanel.create({
			dragit: {snap: true},
			id: 'bearbeiten',
			footerToolbar: '<span id="btn-close"><b>X</b></span>',
			content: '<textarea id="editor' + editnr + '">' + Auswahl.innerHTML + '</textarea>',
   		contentOverflow: 'hidden',
			header: false,
	   	theme: 'info',
	   	contentSize: '950 550',
   		callback: function (panel) {
   			panel.footer.style.cursor = "default";
   			panel.footer.style.background = '#0398E2';
				jsPanel.pointerup.forEach(function (item) {
					panel.footer.querySelector('#btn-close').addEventListener(item, function () {
						Auswahl_beenden();
         	   });
	        });
   		}
		});
		tinymce.init({
			selector: 'textarea#editor' + editnr,
			language: 'de',
			plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link codesample table charmap nonbreaking anchor insertdatetime advlist lists help charmap quickbars',
			menubar: 'edit view insert format tools table help',
			toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | charmap | fullscreen  preview print | insertfile image link anchor codesample',
			toolbar_sticky: true,
			quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
			toolbar_mode: 'sliding',
			contextmenu: 'link image table'
		});
		editnr = editnr + 1;
		try{jsPanel.activePanels.getPanel(jsPanel.activePanels.list[0]).close();} catch (err) {}
	} catch (err) {}
}
	
function Auswahl_beenden() {
 	try{
 		Auswahl = document.getElementById(ausgew);
		try{document.getElementById(ausgew).innerHTML = document.getElementsByClassName("tox-edit-area")[0].firstChild.contentDocument.getElementById("tinymce").innerHTML;}  catch (err) {}
		document.getElementById(ausgew).style.border = "1px dotted black";
		document.getElementById(ausgew).style.height = null;
		document.getElementById(ausgew).style.width = null;
		ausgew = "";
		try {bearbeiten.close();} catch (err) {}
	}  catch (err) {}
}

function auswaehlen(){
	try {var id = arguments[0].attributes['id'].value;} catch (err) {var id = "Objekt_" + document.phpform.ausgewaehlt.value;}
	try {
		Auswahl = document.getElementById(id);
	} catch (err) {
		var Auswahl = document.getElementById("Objekt_" + document.phpform.ausgewaehlt.value);
	}
	try {Auswahl_beenden();} catch (err) {}
	ausgew = Auswahl.id;
	Auswahl.setAttribute('draggable','true');
//	Auswahl.setAttribute('ondblclick','Dialog_oeffnen();');
	Auswahl.style.border = "1px solid black";
	Auswahl.className = 'context-menu';
//	Dialog_oeffnen();
}

function Vers_wiederherstellen(Variante) {
	if (Variante == "wiederherstellen") {
		document.forms["phpform"].aktion.value = "abspeichern";
	} else {
		document.forms["phpform"].aktion.value = "löschen";
	}
	document.forms["phpform"].submit();
}

function umschalten(Tab) {
	if (Tab == 1) {
		if (document.getElementById("seite").style.display == "block") {
			document.getElementById("seite").style.display = "none"
			document.getElementById("schaltfl_1").style.backgroundColor = "#FCEDD9";
		} else {
			document.getElementById("seite").style.display = "block"
			document.getElementById("schaltfl_1").style.backgroundColor = "#d6d6d6";
		}
	} else {
		document.getElementById("seite").style.display = "none";
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
