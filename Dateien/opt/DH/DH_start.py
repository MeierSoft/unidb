#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import MySQLdb, re, os, time
i=0
Zeile=list(range(6))
fobj = open("/opt/DH/DH.ini")
for line in fobj:
    Zeile[i]=line.rstrip()
    i=i+1
fobj.close()
dbHost= Zeile[0]
dbUser= Zeile[1]
dbpwd= Zeile[2]
dbdatabase= Zeile[3]
Rechner= Zeile[4]
Ordner_log_Dateien = Zeile[5]
#alte Stop Anweisungen aus dem temp Ordner entfernen
dirList = os.listdir(Ordner_log_Dateien + '/.')
dirList.sort()
for Datei in dirList:
    if Datei[:6] == "DH_ce_":
        os.remove(Ordner_log_Dateien + '/' + Datei)
#Verbindung zur Datenbank herstellen
Fehler = 12
while Fehler > 0:
    try:
        db = MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)
        Fehler = 0
    except:
        time.sleep (5)
        Fehler = Fehler - 1
#Scripte einlesen
#   zuerst die Eltern_ID der aktiven Scripte suchen
Erfolg = 0
while Erfolg == 0:
    try:
        db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'Schnittstellen';")
        M = db.store_result()
        Ergebnis = M.fetch_row(maxrows=0, how=1)
        Eltern_ID = Ergebnis[0]["Einstellung_ID"]
        Erfolg = 1
    except:
        Erfolg = 0
#   Dann die Liste der Scripte einlesen
db.query("SELECT * FROM `Einstellungen` WHERE `Eltern_ID`= " + str(Eltern_ID) + ";")
M = db.store_result()
Ergebnis = M.fetch_row(maxrows=0, how=1)
Liste=list(range(0))
for Eintrag in Ergebnis:
    db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'Script' AND (`Zusatz` = '" + Rechner + "' OR `Zusatz` = 'Standard' OR `Zusatz` = 'Server') AND `Eltern_ID` = " + str(Eintrag["Einstellung_ID"]) + ";")
    M=db.store_result()
    Resultat = M.fetch_row(maxrows=0, how=1)
    if len(Resultat) > 0:
        Liste.append(Resultat[0])
#allgemeine Einstellungen
os.chdir('/opt/DH/')
i = 0
while i < len(Liste):
    Script = Liste[i]['Wert']
    laeuft = 0
    Text = os.popen('ps -e|grep DH_ >&1')
    Inhalte = Text.read()
    Inhalt = Inhalte.split("\n")
    for x in range(len(Inhalt) - 1):
        Werte = Inhalt[x].split()
        Ergebnis = re.search(".",Werte[3])
        if Ergebnis.span()[1] >3:
            Werte[3] = Werte[3][:Ergebnis.span()[1]]
        if Werte[3] == Script:
            laeuft = 1
            break
    try:
        if laeuft == 0:
            os.popen('./' + Script + ' &')
    except:
        print (Liste[i]['Wert'] + " konnte nicht gestartet werden.")
    i=i+1
#doppelte Eintraege in der Tabelle akt entfernen
db.query("SELECT * FROM `akt` ORDER BY `Point_ID` ASC, `Timestamp` ASC;")
M=db.store_result()
Resultat = M.fetch_row(maxrows=0, how=1)
Satz_alt = Resultat[0]
for Satz in range(1, len(Resultat) - 1):
    if Resultat[Satz]["Point_ID"] == Satz_alt["Point_ID"] and Resultat[Satz]["Timestamp"] == Satz_alt["Timestamp"]:
        db.query("DELETE FROM `akt` WHERE `id` = " + str(Resultat[Satz]["id"]) + ";")
    else:
        Satz_alt = Resultat[Satz]
#Ende
db.close
