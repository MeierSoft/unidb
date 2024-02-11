#!/usr/bin/python

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import MySQLdb, time,  os
import sys

Verbindungen = []
Einstellungen=""
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
db=MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)
db_local = MySQLdb.connect("localhost", dbUser, dbpwd, dbdatabase)
def meldung(Interface):
    global Intervall, Tags, TL,  Wert,  T, Dateien,  db
    dirList = os.listdir('/opt/DH/tmp/.')
    dirList.sort()
    gefunden=""
    for Datei in dirList:
        if Datei[6:len(Interface)+6]==Interface:
            gefunden=Datei
    if gefunden=="":
        return "weiter"
    else:        
        Datei= open("/opt/DH/tmp/"+gefunden, "r")
        Inhalt=Datei.read()
        os.remove("/opt/DH/tmp/"+gefunden)
        if Inhalt=="Tags einlesen" or Inhalt=="Points einlesen":
            if Interface[:4]=="comp":
                try:
                    db.query("SELECT * FROM `Points` WHERE `scan` = 1 AND `archive` = 1;")
                except:
                    db=MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)
                    db.query("SELECT * FROM `Points` WHERE `scan` = 1 AND `archive` = 1;")
            else:
                try:
                    db.query("SELECT * FROM `Points` WHERE `Interface` LIKE '" + Interface + "' AND `scan` = 1 ORDER BY `Property_1` ASC;")
                except:
                    db=MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)
                    db.query("SELECT * FROM `Points` WHERE `Interface` LIKE '" + Interface + "' AND `scan` = 1 ORDER BY `Property_1` ASC;")
            T=db.store_result()
            TL = T.fetch_row(maxrows=0, how=1)
            log_schreiben(Interface, "Tags neu eingelesen.")
        if Inhalt=="Intervall":
            db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'Intervall' AND `Zusatz` = '" + Interface + "';")
            M=db.store_result()
            Einstellungen=M.fetch_row(maxrows=0, how=1)
            Intervall=int(Einstellungen[0]['Wert'])
            log_schreiben(Interface, "neuer Intervall: " + str(Intervall))
        if Inhalt=="Initialisierung":
            Initialisierung(Interface)
            log_schreiben(Interface, "neu initialisiert")
            return "Initialisierung"
        if Inhalt=="abschalten":
            return "abschalten"
        else:
            return "weiter"
    
def Initialisierung(Interface):
    global Intervall, Tags, TL, Wert, T, Einstellungen, Zwangsintervall, Werte_pro_h_Tag, Werte_pro_h, letzte_Zeitzahl_Werte_pro_h, Verbindungen, Zeit_letzter_Wert_Tag
    letzte_Zeitzahl_Werte_pro_h = time.time()
    Werte_pro_h=0
    sys.stdout = open(Ordner_log_Dateien + "/" + Interface + '.log', 'w')
    sys.stderr = open(Ordner_log_Dateien + "/" + Interface + '.log', 'w')
    db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'Intervall' AND `Zusatz` = '" + Interface + "';")
    M=db.store_result()
    Einstellungen=M.fetch_row(maxrows=0, how=1)
    Intervall=int(Einstellungen[0]['Wert'])
    Einstellung_Parent=Einstellungen[0]['Eltern_ID']
    #Anzahl der abzufragenden Tags (Fuehler) abfragen und in der Variablen "Fuehler" speichern
    if Interface[:4]=="comp":
        db.query("SELECT COUNT(`Point_ID`) FROM `Points` WHERE `scan` = 1;")
    else:
        db.query("SELECT COUNT(`Point_ID`) FROM `Points` WHERE `Interface` ='" + Interface + "' AND `scan` = 1;")
    M=db.store_result()
    Tags = int(M.fetch_row()[0][0])
    if Interface[:4] != "comp":
        #Werte_pro_h_Tag ermitteln
        db.query("SELECT `Point_ID` FROM `Points` WHERE `Info` = '" + Interface + ": Werte_pro_h_Tag';")
        T=db.store_result()
        TL = T.fetch_row(maxrows=0, how=1)
        try:
            Werte_pro_h_Tag = str(TL[0]['Point_ID'])
        except:
            Werte_pro_h_Tag = ""
        #Zeitpunkt letzter Wert_Tag ermitteln
        db.query("SELECT `Point_ID` FROM `Points` WHERE `Info` = '" + Interface + ": Zeitpunkt_letzter_Wert';")
        T=db.store_result()
        TL = T.fetch_row(maxrows=0, how=1)
        try:
            Zeit_letzter_Wert_Tag = str(TL[0]['Point_ID'])
        except:
            Zeit_letzter_Wert_Tag = ""
    #Tags fuer das Interface suchen und deren Eigenschaften in einem Variablenfeld ablegen
    if Interface[:4]=="comp":
        db.query("SELECT * FROM `Points` WHERE `scan` = 1;")
    else:
        db.query("SELECT * FROM `Points` WHERE `Interface` LIKE '" + Interface + "' AND `scan` = 1 ORDER BY `Property_1` ASC;")
    T=db.store_result()
    TL = T.fetch_row(maxrows=0, how=1)
    if Interface[:4]!="comp":
        #Felder fuer die Values vorbelegen
        global Wert_alt,  Zeitstempel_alt,  Liste
        Wert_alt=list(range(Tags+1))
        Zeitstempel_alt=list(range(Tags+1))
        i=0
        Liste=[]
        for Tag in TL:
            db.query("select `Value`, `Timestamp` from `akt` where `Point_ID` = " + str(TL[i]['Point_ID']) + " ORDER BY `Timestamp` DESC LIMIT 1")
            Ergebnis = db.store_result()
            temp = Ergebnis.fetch_row(maxrows=0, how=1)
            Liste.append(TL[i]['Point_ID'])
            Wert_alt[i] = float(temp[0]["Value"])
            Zeitstempel_alt[i] =time.mktime(time.strptime(str(temp[0]["Timestamp"]), "%Y-%m-%d %H:%M:%S"))
            i=i+1
    #weitere individuelle Einstellungen aus der Datenbank lesen
    db.query("SELECT * FROM `Einstellungen` WHERE `Eltern_ID`= " + str( Einstellung_Parent) + ";")
    M=db.store_result()
    Einstellungen=M.fetch_row(maxrows=0, how=1)
    for i in range(len(Einstellungen)):
        try:
            Einstellungen[i]['Parameter']=Einstellungen[i]['Parameter']
        except:
            pass
        try:
            Einstellungen[i]['Wert']=Einstellungen[i]['Wert']
        except:
            pass
        try:
            Einstellungen[i]['Zusatz']=Einstellungen[i]['Zusatz']
        except:
            pass  
    #Zwangsintervall einlesen
    db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'Zwangsintervall';")
    M=db.store_result()
    Ergebnis=M.fetch_row()
    try:
        Zwangsintervall = float(Ergebnis[0][2])
    except:
        db.query("SELECT * FROM `Einstellungen` WHERE `Parameter` = 'Zwangsintervall' AND `Eltern_ID` = 0;")
        M=db.store_result()
        temp = M.fetch_row(maxrows=0, how=1)
        Zwangsintervall=int(float(temp[0]['Wert']))
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
    
def Wert_schreiben(Point_ID, Zeitstempel,Wert):
    global Werte_pro_h, letzte_Zeitzahl_Werte_pro_h, db, Zeit_letzter_Wert_Tag
    Zeitzahl= time.mktime(time.strptime(Zeitstempel, "%Y-%m-%d %H:%M:%S"))
    try:
        i=Liste.index(Point_ID)
    except:
        i=Liste.index(int(Point_ID))
    if Zeitzahl >= Zeitstempel_alt[i] + Zwangsintervall or Wert_alt[i] < Wert or Wert_alt[i] > Wert:
        SQL_Text = "INSERT INTO `akt` ( `Point_ID` , `Timestamp` , `Value` ) VALUES ('" + str(Point_ID) + "', '" + Zeitstempel + "', '" + str(Wert) + "');"
        schreiben(SQL_Text)
        SQL_Text = "INSERT INTO `akt` ( `Point_ID` , `Timestamp` , `Value` ) VALUES ('" + str(Zeit_letzter_Wert_Tag) + "', '" + time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time())) + "', '" + str(time.time()) + "');"
        schreiben(SQL_Text)
        Wert_alt[i]  = Wert
        Zeitstempel_alt[i]  = Zeitzahl
        if Werte_pro_h_Tag > "":
            try:
                Werte_pro_h = Werte_pro_h +1
            except:
                Werte_pro_h = 0
                letzte_Zeitzahl_Werte_pro_h = time.time()
            if int(letzte_Zeitzahl_Werte_pro_h) + 600 < int(time.time()):
                Werte_pro_h = float(Werte_pro_h) * 6
                SQL_Text = "INSERT INTO `akt` ( `Point_ID` , `Timestamp` , `Value` ) VALUES ('" + Werte_pro_h_Tag + "', '" + str(time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time()))) + "', '" + str(Werte_pro_h) + "');"
                schreiben(SQL_Text)
                Werte_pro_h = 0
                letzte_Zeitzahl_Werte_pro_h = time.time()
                
def log_schreiben(Interface, Meldung):
    Meldung = Meldung.replace('"', '')
    Meldung = Meldung.replace("'", "")
    SQL_Text = "INSERT INTO `Log` ( `Timestamp` , `Source` , `Text` ) VALUES ('" + str(time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time()))) + "','" + Interface + "','" + Meldung + "');"
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
            dbs[i].query("START TRANSACTION;")
            dbs[i].query(SQL_Text)
            dbs[i].query('commit;')
        except:
            try:
                #versuche neu zu verbinden
                dbs[i] = MySQLdb.connect(Verbindungen[i]["IP"], Verbindungen[i]["User"], Verbindungen[i]["Password"], Verbindungen[i]["Database"])
                dbs[i].query("START TRANSACTION;")
                dbs[i].query(SQL_Text)
                dbs[i].query('commit;')
            except:
                #Wenn alle Stricke reissen, dann in den Puffer schreiben
                try:
                    db_local.query("START TRANSACTION;")
                    db_local.query("INSERT INTO `Puffer` (`Server`, `SQL_Text`) VALUES ('" + Verbindungen[i]["IP"] + "', '" + db.escape_string(SQL_Text) + "');")
                    db_local.query('commit;')
                except:
                    db_local = MySQLdb.connect("localhost", dbUser, dbpwd, dbdatabase)
                    db_local.query("START TRANSACTION;")
                    try:
                        db_local.query("INSERT INTO `Puffer` (`Server`, `SQL_Text`) VALUES ('" + Verbindungen[i]["IP"] + "', '" + db.escape_string(SQL_Text) + "');")
                    except:
                        db_local.query("INSERT INTO `Puffer` (`Server`, `SQL_Text`) VALUES ('" + Verbindungen[i]["IP"] + "', '" + db.escape_string(SQL_Text).decode('utf-8', "ignore") + "');")
                    db_local.query('commit;')
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
