#!/usr/bin/python

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import MySQLdb, time
Verbindungen = []
Einstellungen=""
i=0
Zeile=list(range(6))
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
Ordner_log_Dateien = Zeile[5]
db=MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)
db_local = MySQLdb.connect("localhost", dbUser, dbpwd, dbdatabase)
    
def Initialisierung():
    global TL, Wert, T, Einstellungen, Verbindungen
    #sys.stdout = open(Ordner_log_Dateien + "/" + Interface + '.log', 'w')
    #sys.stderr = open(Ordner_log_Dateien + "/" + Interface + '.log', 'w')

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
            Verbindung[Satz["Parameter"]] = Satz["Wert"]
        Verbindungen.append(Verbindung.copy())
        i = i + 1
    Kollektiv_verbinden(Verbindungen)
    db.close
    
def log_schreiben(Interface, Meldung):
    Meldung = Meldung.replace('"', '')
    Meldung = Meldung.replace("'", "")
    SQL_Text = "INSERT INTO `Log` ( `Timestamp` , `Source` , `Text` ) VALUES ('" + time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time())) + "','" + Interface + "','" + Meldung + "');"
    schreiben(SQL_Text)

def Einstellung_lesen(Parameter):
    for i in range(len(Einstellungen)):
        try:
            if Einstellungen[i]['Parameter']==Parameter:
                return str(Einstellungen[i]['Wert'])
        except:
            pass

def schreiben(SQL_Text):
    global db, Verbindungen, db_local
    i = 0
    while i < len(Verbindungen):
        try:
            dbs[i].query(SQL_Text)
        except:
            try:
                #versuche neu zu verbinden
                dbs[i] = MySQLdb.connect(Verbindungen[i]["IP"], Verbindungen[i]["User"], Verbindungen[i]["Password"], Verbindungen[i]["Database"])
                dbs[i].query(SQL_Text)
            except:
                #Wenn alle Stricke reissen, dann in den Puffer schreiben
                try:
                    db_local.query("INSERT INTO `Puffer` (`Server`, `SQL_Text`) VALUES ('" + Verbindungen[i]["IP"] + "', '" + db.escape_string(SQL_Text) + "');")
                except:
                    db_local = MySQLdb.connect("localhost", dbUser, dbpwd, dbdatabase)
                    db_local.query("INSERT INTO `Puffer` (`Server`, `SQL_Text`) VALUES ('" + Verbindungen[i]["IP"] + "', '" + db.escape_string(SQL_Text) + "');")
        i = i + 1

def Kollektiv_verbinden(Verbindungen):
    global dbs
    dbs = list(range(len(Verbindungen)))
    i = 0
    while i < len(Verbindungen):
        try:
            dbs[i] = MySQLdb.connect(Verbindungen[i]["IP"], Verbindungen[i]["User"], Verbindungen[i]["Password"], Verbindungen[i]["Database"])
        except:
            pass
        i = i + 1
