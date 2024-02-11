#!/usr/bin/python3
# *-* coding: utf-8 *-*

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

#import urllib2
import urllib.request
import time, re, DH_biblio2, sys
Script = sys.argv[0]
Interface_Name = Script[Script.find("DH_") + 3:len(Script) - 3]

Initialisierung = DH_biblio2.Initialisierung(Interface_Name)
i = 1
Meldung = "Start"
Lesefehler = 0
Wert = list(range(6))
for i in (Wert):
	Wert[i] = 0
def Lesefehler_merken():
    global Lesefehler
    if (Lesefehler == 0):
        Lesefehler = time.time()
    if (time.time() -  Lesefehler > 3600):
        DH_biblio2.log_schreiben(Interface_Name, "Problem mit den empfangenen Daten.")
        Lesefehler = 0

def Wert_ermitteln(Text, Suchtext, Zuschlag, EndeMarkierung):
    Ergebnis = re.search(Suchtext,Text)
    Text = Text[Ergebnis.span()[1] + Zuschlag:]
    Ergebnis = re.search(EndeMarkierung,Text)
    return float(Text[:Ergebnis.span()[0]])
    
while Meldung != "abschalten":
    #Auf das message subsystem horschen
    if Meldung != "Start":
        try:
            Meldung = DH_biblio2.meldung(Interface_Name)
        except:
            Meldung = "weiter"
    if Meldung != "abschalten":
        Meldung = "weiter"
        
        #Daten lesen
        try:
            url = urllib.request.urlopen('https://stationsweb.awekas.at/ajax_instruments.php?id=14063')
            Text = url.read().decode('ISO-8859-1')
            url.close()
            Fehler = 0
        except:
            Fehler = 1
        if Fehler == 0:
            try:
                Wert[0] = Wert_ermitteln(Text, "temp", 3,",")
                Wert[1] = Wert_ermitteln(Text, "baro", 3,",")
                Wert[2] = Wert_ermitteln(Text, "solar", 3,",")
                Wert[3] = Wert_ermitteln(Text, "hum\"", 2,",")
                Wert[4] = Wert_ermitteln(Text, "rate", 3,",")
                Wert[5] = Wert_ermitteln(Text, "percipitation", 3,"]")
            except:
                Lesefehler_merken()
            
         #Zeitstempel basteln
        Zeitzahl = time.time()
        Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitzahl))
        #in die Db schreiben
        for i in range(0, 6):
            DH_biblio2.Wert_schreiben( DH_biblio2.TL[i]['Point_ID'],  Zeitstempel,  Wert[i])
            Lesefehler=0
    #Auf das message subsystem horschen
    horchen_bis = time.time()+DH_biblio2.Intervall
    while time.time() < horchen_bis:
        Meldung = DH_biblio2.meldung(Interface_Name)
        if Meldung != "weiter":
            horchen_bis = time.time()-1000000
        else:
            time.sleep(5)
DH_biblio2.log_schreiben(Interface_Name, "Interface gestoppt")
