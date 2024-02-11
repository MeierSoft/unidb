#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import time, DH_biblio2, os, sys
from MySQLdb import _mysql
Script = sys.argv[0]
Interface_Name = Script[Script.find("DH_") + 3:len(Script) - 3]
Pufferzeit = time.time()
Pufferintervall = 60
db = _mysql.connect(DH_biblio2.dbHost, DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
# temp_Ordner ermitteln
db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'Serverkomponenten';")
M = db.store_result()
Einstellungen = M.fetch_row(maxrows=0, how=1)
Eltern_ID = Einstellungen[0]['Einstellung_ID']
db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'temp Verzeichnis' AND `Eltern_ID` = " + Eltern_ID.decode('utf-8', "ignore") + ";")
M = db.store_result()
Einstellungen = M.fetch_row(maxrows=0, how=1)
temp_Ordner = Einstellungen[0]['Wert'].decode('utf-8', "ignore")
db.close
def lokale_Initialisierung():
    db = _mysql.connect(DH_biblio2.dbHost, DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
    #Scripte einlesen
    try:
        db.query("SELECT * FROM `Einstellungen` WHERE `Parameter`= 'Script' AND `Zusatz` = '" + DH_biblio2.Rechner + "';")
    except:
        db=_mysql.connect(DH_biblio2.dbHost, DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
        db.query("SELECT * FROM `Einsteldlungen` WHERE `Parameter`= 'Script' AND `Zusatz` = '" + DH_biblio2.Rechner + "';")
    M=db.store_result()
    Liste=M.fetch_row(maxrows=0, how=1)
    i=0
    while i<len(Liste):
        Scriptliste.append (Liste[i]['Wert'].decode('utf-8', "ignore"))
        i=i+1
    i=0
    while i < len(DH_biblio2.TL):
        Point_ID.append(DH_biblio2.TL[i]['Point_ID'])
        Tagname.append(DH_biblio2.TL[i]['Pointname'])
        i=i+1
    db.close
def Meldungen():
    i = 0
    db = _mysql.connect(DH_biblio2.dbHost, DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
    while i < len(DH_biblio2.Verbindungen):
        try:
            DH_biblio2.dbs = _mysql.connect(DH_biblio2.Verbindungen[i]["IP"], DH_biblio2.Verbindungen[i]["User"], DH_biblio2.Verbindungen[i]["Password"], DH_biblio2.Verbindungen[i]["Database"])
            DH_biblio2.dbs.query("SELECT * FROM `Meldungen` WHERE `Rechner` = '" + DH_biblio2.Rechner + "' ORDER BY `Timestamp` ASC;")
            M=DH_biblio2.dbs.store_result()
            Meld = M.fetch_row(maxrows=0, how=1)
            x = 0
            while x < len(Meld):
                DH_biblio2.dbs.query("DELETE FROM `Meldungen` WHERE `Meldungen_ID` = " + Meld[x]['Meldungen_ID'].decode('utf-8', "ignore") + ";")
                #Falls ein Interface eingeschaltet werden soll, dann den ersten Teil ausfuehren, ansonsten eine Datei anlegen
                if Meld[x]['Meldung'].decode('utf-8', "ignore") == "einschalten":
                    os.system("/opt/DH/DH_" + Meld[x]['Schnittstelle'].decode('utf-8', "ignore")  + ".py > /dev/null &")
                else:
                    Dateiname = temp_Ordner + "/DH_ce_" + Meld[x]['Schnittstelle'].decode('utf-8', "ignore") + str(int(round(time.time(), 0)))
                    Datei_s = open(Dateiname,"w")
                    Datei_s.write(Meld[x]['Meldung'].decode('utf-8', "ignore"))
                    Datei_s.close()
                    os.popen('chmod 666 ' + Dateiname)
                x = x + 1
        except:
                pass
        i = i + 1
        try:
            DH_biblio2.dbs.close
        except:
            pass
    db.close

Scriptliste=list(range(0))
lfd_Scripte=list(range(0))
Point_ID=list(range(0))
Tagname=list(range(0))
temp_Liste=list(range(0))
Initialisierung = DH_biblio2.Initialisierung(Interface_Name)
x= lokale_Initialisierung()
Meldung = DH_biblio2.meldung(Interface_Name)
while Meldung != "abschalten":
    while len(temp_Liste)>0:
        temp_Liste.remove(temp_Liste[0])
    for x in range(len(Scriptliste)):
        temp_Liste.append(Scriptliste[x])
    try:
        Text = os.popen('ps -e|grep DH_ >&1')
        Inhalte=Text.read()
        Inhalt=Inhalte.split("\n")
    except:
        Inhalt=""
    
    for x in range(len(Inhalt)-1):
        Werte = Inhalt[x].split()
        try:
            for i in range(len(temp_Liste)):
                if temp_Liste[i][:15]==Werte[3]:
                    lfd_Scripte.append(temp_Liste[i])
                    temp_Liste.remove(temp_Liste[i])
        except:
            pass
    #Status fuer die lfd Scripte uebermitteln
    while len(lfd_Scripte)>0:
        try:
            Name="sy_" + lfd_Scripte[len(lfd_Scripte)-1][3:]
            Name=Name[:len(Name)-3] + "_status"
            Nr=Tagname.index(Name)
            #Status schreiben
            DH_biblio2.Wert_schreiben(Point_ID[Nr],  time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time())),1)
        except:
            pass
        #Eintrag aus der lfd_Scripte - Liste entfernen
        lfd_Scripte.remove(lfd_Scripte[len(lfd_Scripte)-1])
        
    #Status fuer die nicht lfd Scripte uebermitteln
    while len(temp_Liste)>0:
        try:
            Name="sy_" + temp_Liste[len(temp_Liste)-1][3:]
            Name=str(Name[:len(Name)-3] + "_status")
            Nr=Tagname.index(Name)
            #Status schreiben
            DH_biblio2.Wert_schreiben(Point_ID[Nr],  time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time())),0)
            try:
                #Nachschauen, ob es eine Meldung von dem Script gibt
                Laenge = len(temp_Liste[len(temp_Liste)-1]) - 3
                Schnittstelle = temp_Liste[len(temp_Liste)-1][3:Laenge]
                Dateiname =  DH_biblio2.Ordner_log_Dateien + "/" + Schnittstelle + ".log"
                Datei = open(Dateiname, "r")
            except:
                pass
            Inhalt = Datei.read()
            Datei.close()
            if len(Inhalt) > 0:
                DH_biblio2.log_schreiben(Schnittstelle, Inhalt)
                try:
                    os.remove(Dateiname)
                except:
                    pass
        except:
            pass
        #Eintrag aus der lfd_Scripte - Liste entfernen
        temp_Liste.remove(temp_Liste[len(temp_Liste)-1])
        
    #nach Ablauf der Pufferintervallzeit nachschauen, ob es Eintraege im Puffer gibt und ggf versuchen sie zu uebermitteln
    db = _mysql.connect(DH_biblio2.dbHost, DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
    if time.time() >= Pufferzeit:
        Pufferzeit = time.time() + Pufferintervall
        db_local = _mysql.connect(DH_biblio2.dbHost, DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
        db_local.query("SELECT COUNT(`Puffer_ID`) FROM `Puffer`;")
        M=db_local.store_result()
        Anzahl_Werte = int(M.fetch_row()[0][0])
        if Anzahl_Werte  > 0:
            db_local.query("SELECT DISTINCT `Server` FROM `Puffer`;")
            M=db_local.store_result()
            Serveradressen = M.fetch_row(maxrows=0, how=1)
            i = 0
            while i < len(Serveradressen):
                if type(Serveradressen[i]["Server"]) is bytes:
                    Serveradressen[i]["Server"] = Serveradressen[i]["Server"].decode('utf-8', "ignore")
                x = 0
                while x < len(DH_biblio2.Verbindungen):
                    if DH_biblio2.Verbindungen[x]["IP"] == Serveradressen[i]["Server"]:
                        Verb_Nr = x
                    x = x + 1
                try:
                    db_Puffer = _mysql.connect(DH_biblio2.Verbindungen[Verb_Nr]["IP"], DH_biblio2.Verbindungen[Verb_Nr]["User"], DH_biblio2.Verbindungen[Verb_Nr]["Password"], DH_biblio2.Verbindungen[Verb_Nr]["Database"])
                    db_local.query("SELECT * FROM `Puffer` WHERE `Server` = '" + Serveradressen[i]["Server"] + "' ORDER BY Timestamp ASC;")
                    M=db_local.store_result()
                    Saetze = M.fetch_row(maxrows=0, how=1)
                    for Satz in Saetze:
                        if type(Satz["SQL_Text"]) is bytes:
                            Satz["SQL_Text"] = Satz["SQL_Text"].decode('utf-8', "ignore")
                        if type(Satz["Puffer_ID"]) is bytes:
                            Satz["Puffer_ID"] = Satz["Puffer_ID"].decode('utf-8', "ignore")
                        db_Puffer.query(Satz["SQL_Text"])
                        db_local.query("DELETE FROM `Puffer` WHERE `Puffer_ID` = " + str(Satz["Puffer_ID"]) + ";")
                    db_Puffer.close
                except:
                    pass
                i = i + 1
        db_local.close
    db.close

    #Auf das message subsystem horschen
    horchen_bis=time.time()+DH_biblio2.Intervall
    while time.time()<horchen_bis:
        Meldungen()
        Meldung = DH_biblio2.meldung(Interface_Name)
        if Meldung== "Initialisierung":
            x= lokale_Initialisierung()
        if Meldung!="weiter":
            horchen_bis=time.time()-1000000
        else:
            time.sleep(5)
DH_biblio2.log_schreiben(Interface_Name, "Interface gestoppt")
db.close
