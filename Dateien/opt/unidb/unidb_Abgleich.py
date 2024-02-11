#!/usr/bin/python

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import _mysql

Verbindungen = []
Einstellungen=""
i=0
Zeile=list(range(5))
fobj = open("/opt/unidb/unidb.ini")
for line in fobj:
    Zeile[i]=line.rstrip()
    i=i+1
fobj.close()
dbHost= Zeile[0]
dbUser= Zeile[1]
dbpwd= Zeile[2]
dbdatabase= Zeile[3]
Rechner= Zeile[4]
db=_mysql.connect(dbHost, dbUser, dbpwd, dbdatabase)

def Initialisierung():
    global Verbindungen
    #Kollektiv
    db.query("SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter`= 'Kollektiv';")
    M = db.store_result()
    Kollektiv = M.fetch_row()
    db.query("SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Eltern_ID`= " + str(Kollektiv[0][0]) + ";")
    M = db.store_result()
    Server = M.fetch_row(maxrows=0, how=1)
    Verbindung = {}
    Verbindungen = []
    i = 0
    while i < len(Server):
        db.query("SELECT * FROM `Einstellungen` WHERE `Eltern_ID`= " + str(Server[i]["Einstellung_ID"]) + ";")
        M = db.store_result()
        Saetze = M.fetch_row(maxrows=0, how=1)
        for Satz in Saetze:
            Verbindung[Satz["Parameter"].decode('utf-8', "ignore")] = Satz["Wert"].decode('utf-8', "ignore")
        Verbindungen.append(Verbindung.copy())
        i = i + 1
    Kollektiv_verbinden(Verbindungen)
    db.close
    
def log_schreiben(Meldung):
    Meldung = Meldung.replace('"', '')
    Meldung = Meldung.replace("'", "")
    SQL_Text = "INSERT INTO `Log` (`Eintrag`) VALUES ('" + Meldung + "');"
    schreiben(SQL_Text)

def schreiben(SQL_Text):
    global Verbindungen
    i = 0
    while i < len(Verbindungen):
        try:
            dbs[i].query(SQL_Text)
        except:
            pass
        i = i + 1

def Kollektiv_verbinden(Verbindungen):
    global dbs
    dbs = list(range(len(Verbindungen)))
    i = 0
    while i < len(Verbindungen):
        try:
            if Verbindungen[i]["IP"] != Rechner:
                dbs[i] = _mysql.connect(Verbindungen[i]["IP"], Verbindungen[i]["User"], Verbindungen[i]["Password"], Verbindungen[i]["Database"],  connect_timeout=5)
        except:
            dbs[i] = ""
        i = i + 1

Initialisierung()
for Verbindung in Verbindungen:
    #ID des letzten gelesenen Datensatzes abfragen
    if Verbindung["IP"] != Rechner:
        db.query("SELECT `Zusatz` FROM `Status` WHERE `Eigenschaft`= 'Kollektiv' AND `Wert`= '" + str(Verbindung["IP"]) + "';")
        M = db.store_result()
        Ergebnis = M.fetch_row()
        letzte_ID = Ergebnis[0][0].decode('utf-8', "ignore")
        
    
