#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import time, MySQLdb, unidb_biblio2

unidb_biblio2.Initialisierung()
db = MySQLdb.connect(unidb_biblio2.dbHost, unidb_biblio2.dbUser, unidb_biblio2.dbpwd, unidb_biblio2.dbdatabase)
db_local = MySQLdb.connect("localhost", unidb_biblio2.dbUser, unidb_biblio2.dbpwd, unidb_biblio2.dbdatabase)
Pufferintervall = 300
while 0 < 1:
    try:
        db_local.query("SELECT COUNT(`Puffer_ID`) FROM `Puffer`;")
    except:
        db_local = MySQLdb.connect(unidb_biblio2.dbHost, unidb_biblio2.dbUser, unidb_biblio2.dbpwd, unidb_biblio2.dbdatabase)
        db_local.query("SELECT COUNT(`Puffer_ID`) FROM `Puffer`;")
    M=db_local.store_result()
    Anzahl_Werte = int(M.fetch_row()[0][0])
    if Anzahl_Werte  > 0:
        db_local.query("SELECT DISTINCT `Server` FROM `Puffer`;")
        M=db_local.store_result()
        Serveradressen = M.fetch_row(maxrows=0, how=1)
        i = 0
        while i < len(Serveradressen):
            x = 0
            while x < len(unidb_biblio2.Verbindungen):
                if unidb_biblio2.Verbindungen[x]["IP"] == Serveradressen[i]["Server"]:
                    Verb_Nr = x
                x = x + 1
            try:
                db_Puffer = MySQLdb.connect(unidb_biblio2.Verbindungen[Verb_Nr]["IP"], unidb_biblio2.Verbindungen[Verb_Nr]["User"], unidb_biblio2.Verbindungen[Verb_Nr]["Password"], unidb_biblio2.Verbindungen[Verb_Nr]["Database"])
                db_local.query("SELECT * FROM `Puffer` WHERE `Server` = '" + Serveradressen[i]["Server"] + "' ORDER BY Timestamp ASC;")
                M=db_local.store_result()
                Saetze = M.fetch_row(maxrows=0, how=1)
                for Satz in Saetze:
                    db_Puffer.query("START TRANSACTION;")
                    db_Puffer.query(Satz["SQL_Text"])
                    db_Puffer.query("COMMIT;")
                    db_local.query("START TRANSACTION;")
                    db_local.query("DELETE FROM `Puffer` WHERE `Puffer_ID` = " + str(Satz["Puffer_ID"]) + ";")
                    db_local.query("COMMIT;")
            except:
                pass
            i = i + 1
    time.sleep(Pufferintervall)

db_local.close
