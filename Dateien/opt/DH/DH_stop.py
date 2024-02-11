#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import os, MySQLdb, time, re
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
db = MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)

#lfd Scripte stoppen
# temp_Ordner ermitteln
db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'Serverkomponenten';")
M = db.store_result()
Einstellungen = M.fetch_row(maxrows=0, how=1)
Eltern_ID = Einstellungen[0]['Einstellung_ID']
db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'temp Verzeichnis' AND `Eltern_ID` = " + str(Eltern_ID) + ";")
M = db.store_result()
Einstellungen = M.fetch_row(maxrows=0, how=1)
temp_Ordner = Einstellungen[0]['Wert']

Text = os.popen('ps -e|grep DH_ >&1')
Inhalte = Text.read()
Inhalt = Inhalte.split("\n")
for x in range(len(Inhalt)-1):
    Werte = Inhalt[x].split()
    Ergebnis = re.search(".",Werte[3])
    if Ergebnis.span()[1] >3:
        Werte[3] = Werte[3][:Ergebnis.span()[1]]
    if Werte[3][3:7] != "stop":
        Dateiname = temp_Ordner + "/DH_ce_" + Werte[3][3:] + str(int(round(time.time(), 0)))
        Datei_s = open(Dateiname,"w")
        Datei_s.write("abschalten")
        Datei_s.close()
        os.popen('chmod 666 ' + Dateiname)
    
#Warten bis alle Scripte gestopt sind
start = time.time()
while len(Inhalt) > 2 and time.time() - start < 20:
    Text = os.popen('ps -e|grep DH_ >&1')
    Inhalte = Text.read()
    Inhalt = Inhalte.split("\n")
if len(Inhalt) > 2:
    Text = os.popen('ps -e|grep DH_ >&1')
    Inhalte = Text.read()
    Inhalt = Inhalte.split("\n")
    for x in range(len(Inhalt)-1):
        Werte = Inhalt[x].split()
        Ergebnis = re.search(".",Werte[3])
        if Ergebnis.span()[1] >3:
            Werte[3] = Werte[3][:Ergebnis.span()[1]]
        if Werte[3][3:7] != "stop":
            os.popen('kill ' + Werte[0])
print ("Alle Scripte wurden angehalten.")
print ("Sie können den Server nun herunterfahren.")
