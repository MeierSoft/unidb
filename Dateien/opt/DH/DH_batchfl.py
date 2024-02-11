#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import os, time, DH_biblio2, sys, MySQLdb
Script = sys.argv[0]
Interface_Name = Script[Script.find("DH_") + 3:len(Script) - 3]

#Initialisierung
Initialisierung = DH_biblio2.Initialisierung(Interface_Name)
Pfad=DH_biblio2.Einstellung_lesen('Pfad')
letzte_Zeitzahl_Werte_pro_h = time.time()
letzte_Zeitzahl_Zeit_letzter_Wert_Tag = 0
#Zeitpunkt letzter Wert_Tag ermitteln
db=MySQLdb.connect("localhost", DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
db.query("SELECT `Point_ID` FROM `Points` WHERE `Info` = '" + Interface_Name + ": Zeitpunkt_letzter_Wert';")
T=db.store_result()
TL = T.fetch_row(maxrows=0, how=1)
try:
    Zeit_letzter_Wert_Tag = str(TL[0]['Point_ID'])
except:
    Zeit_letzter_Wert_Tag = ""
#Werte_pro_h_Tag ermitteln
db.query("SELECT `Point_ID` FROM `Points` WHERE `Info` = '" + Interface_Name + ": Werte_pro_h_Tag';")
T=db.store_result()
TL = T.fetch_row(maxrows=0, how=1)
try:
    Werte_pro_h_Tag = str(TL[0]['Point_ID'])
except:
    Werte_pro_h_Tag = ""
Werte_pro_h = 0
Meldung="Start"

while Meldung != "abschalten":
    #Auf das message subsystem horchen
    if Meldung != "Start":
        try:
            Meldung = DH_biblio2.meldung(Interface_Name)
        except:
            Meldung = "weiter"
    if Meldung != "abschalten":
        Meldung = "weiter"
    #Lebenszeichen senden
    if int(letzte_Zeitzahl_Zeit_letzter_Wert_Tag) + 600 < int(time.time()):
        SQL_Text = "INSERT INTO `akt` ( `Point_ID` , `Timestamp` , `Value` ) VALUES ('" + str(Zeit_letzter_Wert_Tag) + "', '" + time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time())) + "', '" + str(time.time()) + "');"
        DH_biblio2.schreiben(SQL_Text)
        SQL_Text = "INSERT INTO `akt` ( `Point_ID` , `Timestamp` , `Value` ) VALUES ('" + Werte_pro_h_Tag + "', '" + str(time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time()))) + "', '0');"
        DH_biblio2.schreiben(SQL_Text)
        letzte_Zeitzahl_Zeit_letzter_Wert_Tag = int(time.time())
    for t in os.walk(Pfad, False):
        x= t [2]
        for z in x:
            if z[-4:] == ".dat":
                datei = open(Pfad + "/" +  z, 'r')
                Text = datei.read()
                Texte=Text.split("\n")
                i=0
                for x in Texte:
                    Teile=Texte[i].split(",")
                    i+=1
                    if Teile[0]!="":
                        try:
                            SQL_Text = "INSERT INTO `akt` ( `Point_ID` , `Timestamp` , `Value` ) VALUES ('" + str(Teile[0]) + "', '" + str(Teile[1]) + "', '" + str(Teile[2]) + "');"
                            DH_biblio2.schreiben(SQL_Text)
                        except:
                            DH_biblio2.log_schreiben(Interface_Name, "Der Wert hat offenbar das falsche Format.")
                    if Werte_pro_h_Tag > "":
                        try:
                            Werte_pro_h = Werte_pro_h +1
                        except:
                            Werte_pro_h = 0
                            letzte_Zeitzahl_Werte_pro_h = time.time()
                        if int(letzte_Zeitzahl_Werte_pro_h) + 600 < int(time.time()):
                            Werte_pro_h = float(Werte_pro_h) * 6
                            SQL_Text = "INSERT INTO `akt` ( `Point_ID` , `Timestamp` , `Value` ) VALUES ('" + Werte_pro_h_Tag + "', '" + str(time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time()))) + "', '" + str(Werte_pro_h) + "');"
                            DH_biblio2.schreiben(SQL_Text)
                            Werte_pro_h = 0
                            letzte_Zeitzahl_Werte_pro_h = time.time()
                #Datei umbenennen
                os.rename(Pfad+"/"+ z, Pfad+"/"+z[:-3] +'xxx')
    #Auf das message subsystem horschen
    
    horchen_bis=time.time()+DH_biblio2.Intervall
    SQL_Text = "INSERT INTO `akt` ( `Point_ID` , `Timestamp` , `Value` ) VALUES ('" + str(Zeit_letzter_Wert_Tag) + "', '" + time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time())) + "', '" + str(time.time()) + "');"
    DH_biblio2.schreiben(SQL_Text)
    while time.time()<horchen_bis:
        Meldung = DH_biblio2.meldung(Interface_Name)
        if Meldung!="weiter":
            horchen_bis=time.time()-1000000
        else:
            time.sleep(5)

DH_biblio2.log_schreiben(Interface_Name, "Interface gestoppt")
