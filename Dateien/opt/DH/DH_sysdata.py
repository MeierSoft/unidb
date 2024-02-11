#!/usr/bin/python3
# -*- coding: utf-8 -*-

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import os, time, DH_biblio2, MySQLdb, sys
Script = sys.argv[0]
Interface_Name = Script[Script.find("DH_") + 3:len(Script) - 3]

Initialisierung = DH_biblio2.Initialisierung(Interface_Name)
db=MySQLdb.connect("localhost", DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)

Meldung="Start"
while Meldung != "abschalten":
    #Auf das message subsystem horschen
    if Meldung != "Start":
        try:
            Meldung = DH_biblio2.meldung(Interface_Name)
        except:
            Meldung = "weiter"
    if Meldung == "Tags einlesen" or Meldung == "Start":
        if Meldung != "abschalten":
            Meldung = "weiter"
    #Daten erfassen
    datei = open('/proc/loadavg','r')
    Text = datei.read()
    Werte=Text.split()
    Loadavg=float(Werte[1])*100
    datei = open('/proc/meminfo','r')
    Text = datei.read()
    Texte=Text.split("\n")
    Teile=Texte[0].split()
    MemTotal=float(Teile[1])    
    Texte=Text.split("\n")
    Teile=Texte[1].split()
    Memfree=float(Teile[1])
    Teile=Texte[2].split()
    Buffers=float(Teile[1])
    Teile=Texte[3].split()
    Cached=float(Teile[1])
    Teile=Texte[4].split()
    SwapCached=float(Teile[1])
    #Prozessortemperaturen auslesen
    #for Text in os.popen('/usr/bin/sensors | grep "Core 0" >&1'):
    #    Werte = Text.split()
    #    Werte2=Werte[2].split("°C")
    #    temp1=float(Werte2[0])
    #for Text in os.popen('/usr/bin/sensors | grep "Core 1" >&1'):
    #    Werte = Text.split()
    #    Werte2=Werte[2].split("°C")
    #    temp2=float(Werte2[0])          
    #for Text in os.popen('/usr/bin/sensors | grep "Core 2" >&1'):
    #    Werte = Text.split()
    #    Werte2=Werte[2].split("°C")
    #    temp3=float(Werte2[0])
    #for Text in os.popen('/usr/bin/sensors | grep "Core 3" >&1'):
    #    Werte = Text.split()
    #    Werte2=Werte[2].split("°C")
    #    temp4=float(Werte2[0])
    #Groesse der Tabellen akt und Puffer auslesen
    try:
        db.query("SELECT COUNT(`Point_ID`) FROM `akt`;")
    except:
        db=MySQLdb.connect("localhost", DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
        db.query("SELECT COUNT(`Point_ID`) FROM `akt`;")
    M=db.store_result()
    Anzahl_Werte_akt = int(M.fetch_row()[0][0])
    #Zeitstempel merken
    Zeitzahl=time.time()
    Zeitstempel=time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitzahl))
    #Daten in die Datenbank schreiben
    #Loadavg
    DH_biblio2.Wert_schreiben(38,  Zeitstempel,  Loadavg)
    #Memfree
    DH_biblio2.Wert_schreiben(37,  Zeitstempel,  Memfree)
    #Buffers
    DH_biblio2.Wert_schreiben(36,  Zeitstempel,  Buffers)
    #Cached
    DH_biblio2.Wert_schreiben(35,  Zeitstempel,  Cached)
    #SwapCached
    DH_biblio2.Wert_schreiben(34,  Zeitstempel,  SwapCached)
    #Speicherauslastung in Prozent
    Speicher_Proz =(Memfree + Buffers+Cached)/MemTotal*100
    DH_biblio2.Wert_schreiben(33,  Zeitstempel,  Speicher_Proz)
    #Speicherbedarf in MByte
    Speicherbedarf = (MemTotal - Memfree - Buffers - Cached) / 1024
    DH_biblio2.Wert_schreiben(32,  Zeitstempel,  Speicherbedarf)
    #temp1
    #try:
        #DH_biblio2.Wert_schreiben(29,  Zeitstempel,  temp1)
    #except:
    #    pass
    #temp2
    #try:
    #    DH_biblio2.Wert_schreiben(28,  Zeitstempel,  temp2)
    #except:
    #    pass
    #temp3
    #try:
    #    DH_biblio2.Wert_schreiben(27,  Zeitstempel,  temp3)
    #except:
    #    pass
    #temp4
    #try:
    #    DH_biblio2.Wert_schreiben(26,  Zeitstempel,  temp4)
    #except:
    #    pass
    #akt
    DH_biblio2.Wert_schreiben(17,  Zeitstempel,  Anzahl_Werte_akt)
    #Auf das message subsystem horschen
    horchen_bis=time.time()+DH_biblio2.Intervall
    while time.time()<horchen_bis:
        Meldung = DH_biblio2.meldung(Interface_Name)
        if Meldung!="weiter":
            horchen_bis=time.time()-1000000
        else:
            time.sleep(5)

DH_biblio2.log_schreiben(Interface_Name, "Interface gestoppt")

