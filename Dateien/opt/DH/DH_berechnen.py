#!/usr/bin/python

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import time
import re
import MySQLdb
from optparse import OptionParser

Text=""
Archiv = ""
i=0
Ausdruck_orig=""
Wert = ""
rechts=""
links=""

# uebergebene Parameter auslesen
parser = OptionParser("Parametertest.py [Optionen]") 
parser.add_option("-a", dest="Ausdruck_orig", help="Der zu berechnende Ausdruck.") 
parser.add_option("-d", dest="Zeitstempel", help="Der Zeitpunkt, zu dem der Ausdruck berechnet werden soll. Angabe in der Form JJJJ-MM-TT hh:mm:ss, oder das Wort jetzt.") 
parser.add_option("-o", dest="offset", help="Verschiebung nach hinten in Sekunden, bezogen auf den Zeitstempel.") 
(optionen, args) = parser.parse_args() 
Bezugszeitstempel = optionen.Zeitstempel
Offset = 0
try:
    Offset = float(optionen.offset)
except:
    pass
Ausdruck = optionen.Ausdruck_orig

if Bezugszeitstempel == "jetzt":
    Ausdruck = Ausdruck.replace("AW(", "akt(")
    Bezugszeitpunkt = time.time()
    try:
        Bezugszeitpunkt = Bezugszeitpunkt - Offset
    except:
        pass
    Bezugszeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Bezugszeitpunkt))
else:
    try:
        Bezugszeitpunkt = time.mktime(time.strptime(Bezugszeitstempel, "%Y-%m-%d %H:%M:%S"))
        try:
            Bezugszeitpunkt = Bezugszeitpunkt - Offset
        except:
            pass
    except:
        try:
            Bezugszeitpunkt = float(Bezugszeitstempel)
            Bezugszeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Bezugszeitpunkt))
        except:
            print ("ungueltige Zeitangabe")
            raise SystemExit

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

def Archivtab(Zeitpunkt):
    db.query("SELECT `Archiv_Name` FROM `Archiv_Tab` WHERE `bis` >= '" + Zeitpunkt + "' ORDER BY `von` ASC LIMIT 1;")
    x=db.store_result()
    Ergebnis=x.fetch_row()
    return Ergebnis[0][0]
    
def Archivtabellen(Start,  Ende):
    db.query("SELECT `Archiv_Name` FROM `Archiv_Tab` WHERE `bis` >= '" + Start + "' AND `von` <= '" + Ende + "' ORDER BY `von` ASC;")
    x=db.store_result()
    Ergebnis = x.fetch_row(maxrows=0, how=1)
    return Ergebnis

def Zeitzahl_basteln(Zeitstempel):
    global Wert
    if Zeitstempel == "jetzt":
        Zeitzahl = time.time()
        Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitzahl))
    else:
        try:
            return time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time()))
        except:
            return 1

def Point_ID_ermitteln(Ausdruck,  pos):
    Ergebnis = ""
    nehmen = ord(Ausdruck[pos:pos + 1])
    while nehmen>47 and nehmen <58:
        Ergebnis = Ergebnis + chr(nehmen)
        pos = pos +1
        nehmen = ord(Ausdruck[pos:pos + 1])
    return Ergebnis

def Zeitpunkt_ermitteln(rechts):
    rel_Zeit = rechts
    if rel_Zeit =="jetzt":
        Zeitpunkt_Zahl = time.time()
        Text = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitpunkt_Zahl ))
        return Text,  Zeitpunkt_Zahl
    #Multiplikator ermitteln
    Multiplikator1 = ""
    pos = 0
    Text = ""
    rel = 1
    nehmen = 0
    if rechts[:1] == "," or rechts[:1] == "(":
        rechts = rechts[1:]
    while nehmen != 44 and nehmen != 41 and len(Text) < len(rel_Zeit):
        nehmen = ord(rechts[pos:pos + 1])
        if (nehmen < 48 or nehmen >57) and (nehmen != 32 and nehmen != 100 and nehmen != 115 and nehmen != 109 and nehmen != 104 and nehmen != 41 and nehmen != 44):
            rel = 0
        Text = Text + rechts[pos:pos + 1]
        pos = pos + 1
    while Text[:1] == " " or Text[:1] == ",":
        Text = Text[1:]
    while Text[-1:] == "," or Text[-1:] == " " or Text[-1:] == ")":
        Text = Text[:-1]
    Text = Text.replace('"', '')
    if rel == 1:
        pos = 0
        nehmen = ord(Text[pos:pos + 1])
        while nehmen>47 and nehmen <58:
            Multiplikator1=Multiplikator1 + chr(nehmen)
            pos = pos +1
            nehmen = ord(Text[pos:pos + 1])
        rel_Zeit = Text[-1:]
        Multiplikator1=int(Multiplikator1)
        if rel_Zeit== "s":
            Multiplikator2=1
        elif rel_Zeit== "m":
            Multiplikator2=60
        elif rel_Zeit== "h":
            Multiplikator2=3600
        elif rel_Zeit== "d":
            Multiplikator2=86400
        Zeitpunkt_Zahl = Bezugszeitpunkt-Multiplikator1*Multiplikator2
        return time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitpunkt_Zahl)),  Zeitpunkt_Zahl
    else:
        if Text == "jetzt":
            Zeitpunkt_Zahl = time.time()
            Text = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitpunkt_Zahl ))
        else:
            Zeitpunkt_Zahl = time.mktime(time.strptime(Text, "%Y-%m-%d %H:%M:%S"))
        return Text,  Zeitpunkt_Zahl
        
        
def interpolieren(Archiv,  Point_ID,  Zeitpunkt):
    db.query("select * from " + Archiv + " where Point_ID = " + Point_ID + " AND Timestamp = '" + Zeitpunkt + "' AND Art = 'rV' ORDER BY Timestamp DESC LIMIT 1")
    x=db.store_result()
    Ergebnis=x.fetch_row()
    if len(Ergebnis)==0:
        db.query("select * from " + Archiv + " where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' AND Art = 'rV' ORDER BY Timestamp DESC LIMIT 1")
        x=db.store_result()
        Ergebnis=x.fetch_row()
        Wert_vor = float(Ergebnis[0][2])
        Zeit_vor = Ergebnis[0][0]
        Zeit_vor = time.mktime(time.strptime(Zeit_vor, "%Y-%m-%d %H:%M:%S"))
        db.query("select * from " + Archiv + " where Point_ID = " + Point_ID + " AND Timestamp > '" + Zeitpunkt + "' AND Art = 'rV' ORDER BY Timestamp ASC LIMIT 1")
        x=db.store_result()
        Ergebnis=x.fetch_row()
        if len(Ergebnis)==0:
            #Den aktuellen Wert aus akt herauslesen und den Tag durch den Wert ersetzen
            db.query("select * from akt where Point_ID = " + Point_ID + " ORDER BY Timestamp DESC LIMIT 1")
            x=db.store_result()
            akt_Werte = x.fetch_row()
            Wert_nach = float(akt_Werte[0][2])
            Zeit_nach = akt_Werte[0][0]
            Zeit_nach = time.mktime(time.strptime(Zeit_nach, "%Y-%m-%d %H:%M:%S"))
        else:
            Wert_nach = float(Ergebnis[0][2])
            Zeit_nach = float(Ergebnis[0][3])
        Zeitdifferenz = Zeit_nach - Zeit_vor
        if Zeitdifferenz > 0:
            Steigung = (Wert_nach - Wert_vor) / Zeitdifferenz
        else:
            Steigung=0
        Zeitpunkt_Zahl = time.mktime(time.strptime(Zeitpunkt, "%Y-%m-%d %H:%M:%S"))
        return (Zeitpunkt_Zahl - Zeit_vor) * Steigung + Wert_vor
    else:
        return float(Ergebnis[0][2])



#Zeitpunkt Wert
Suchtext = "ZP\("
while re.search(Suchtext,Ausdruck):
    Zeichen = re.search(Suchtext,Ausdruck)
    links = links + Ausdruck[len(links):Zeichen.span()[0]]
    rechts = Ausdruck[Zeichen.span()[1]:len(Ausdruck)]
    Point_ID = Point_ID_ermitteln(Ausdruck,  Zeichen.span()[1])
    rechts = rechts[rechts.find(")")+1:]
    Zeit = Zeitpunkt_ermitteln(Bezugszeitstempel)
    Zeitpunkt = Zeit[0]
    Zeitpunkt_Zahl = Zeit[1]
    db.query("select * from akt where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' ORDER BY Timestamp DESC LIMIT 1")
    x=db.store_result()
    Ergebnis=x.fetch_row()
    if len(Ergebnis)==0:
        Archiv = Archivtab(Zeitpunkt).decode('UTF-8')
        db.query("select * from " + Archiv + " where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' AND Art = 'rV' ORDER BY Timestamp DESC LIMIT 1")
        x=db.store_result()
        Ergebnis=x.fetch_row()
        try:
            Wert = Ergebnis[0][3]
        except:
            Wert = "Fehler"
    else:
        Wert = time.mktime(time.strptime(Ergebnis[0][1], "%Y-%m-%d %H:%M:%S"))
    #Ausdruck aktualisieren
    Ausdruck=links + str(Wert) +rechts

#Zeitstempel Wert
Suchtext = "ZS\("
while re.search(Suchtext,Ausdruck):
    Zeichen = re.search(Suchtext,Ausdruck)
    links = links + Ausdruck[len(links):Zeichen.span()[0]]
    rechts = Ausdruck[Zeichen.span()[1]:len(Ausdruck)]
    Point_ID = Point_ID_ermitteln(Ausdruck,  Zeichen.span()[1])
    rechts = rechts[rechts.find(")")+1:]
    Zeit = Zeitpunkt_ermitteln(Bezugszeitstempel)
    Zeitpunkt = Zeit[0]
    Zeitpunkt_Zahl = Zeit[1]
    db.query("select * from akt where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' ORDER BY Timestamp DESC LIMIT 1")
    x=db.store_result()
    Ergebnis=x.fetch_row()
    if len(Ergebnis)==0:
        Archiv = Archivtab(Zeitpunkt).decode('UTF-8')
        db.query("select * from " + Archiv + " where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' AND Art = 'rV' ORDER BY Timestamp DESC LIMIT 1")
        x=db.store_result()
        Ergebnis=x.fetch_row()
        try:
            Wert = Ergebnis[0][0]
        except:
            Wert = "Fehler"
    else:
        Wert = Ergebnis[0][1]
    #Ausdruck aktualisieren
    Ausdruck=links + str(Wert) +rechts

#aktueller Wert
Suchtext = "akt\("
while re.search(Suchtext,Ausdruck):
    Zeichen = re.search(Suchtext,Ausdruck)
    links = links + Ausdruck[len(links):Zeichen.span()[0]]
    rechts = Ausdruck[Zeichen.span()[1]:len(Ausdruck)]
    Point_ID = Point_ID_ermitteln(Ausdruck,  Zeichen.span()[1])
    rechts = rechts[rechts.find(")")+1:]
    #Den aktuellen Wert aus akt herauslesen und den Tag durch den Wert ersetzen
    db.query("select * from akt where Point_ID = " + Point_ID + " ORDER BY Timestamp DESC LIMIT 1")
    akt_Werte = db.store_result()
    Ergebnis=akt_Werte.fetch_row()
    links = links + str(Ergebnis[0][3])
    #Ausdruck aktualisieren
    Ausdruck = links + rechts

#Archivwert
Suchtext = "AW\("
while re.search(Suchtext,Ausdruck):
    Zeichen = re.search(Suchtext,Ausdruck)
    links = links + Ausdruck[len(links):Zeichen.span()[0]]
    rechts = Ausdruck[Zeichen.span()[1]:len(Ausdruck)]
    Point_ID = Point_ID_ermitteln(Ausdruck,  Zeichen.span()[1])
    rechts = rechts[rechts.find(")")+1:]
    Zeit = Zeitpunkt_ermitteln(Bezugszeitstempel)
    Zeitpunkt = Zeit[0]
    Zeitpunkt_Zahl = Zeit[1]
    db.query("select * from akt where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' ORDER BY Timestamp DESC LIMIT 1")
    x=db.store_result()
    Ergebnis=x.fetch_row()
    if len(Ergebnis)==0:
        Archiv = Archivtab(Zeitpunkt).decode('UTF-8')
        db.query("select * from " + Archiv + " where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' AND Art = 'rV' ORDER BY Timestamp DESC LIMIT 1")
        x=db.store_result()
        Ergebnis=x.fetch_row()
        Wert = Ergebnis[0][2]
    else:
        Wert = Ergebnis[0][3]
    #Ausdruck aktualisieren
    Ausdruck=links + str(Wert) + rechts
    
#interpolierte Werte, Format=intp(Tag,relativer Zeitpunkt) Beispiel intp(22,1h) = interpolierter Wert vom Tag 22 von vor einer Stunde
Suchtext = "intp\("
while re.search(Suchtext,Ausdruck):
    Zeichen = re.search(Suchtext,Ausdruck)
    links = links + Ausdruck[len(links):Zeichen.span()[0]]
    rechts = Ausdruck[Zeichen.span()[1]:len(Ausdruck)]
    Point_ID = Point_ID_ermitteln(Ausdruck,  Zeichen.span()[1])
    rechts = rechts[rechts.find(")")+1:]
    Zeit = Zeitpunkt_ermitteln(Bezugszeitstempel)
    Zeitpunkt = Zeit[0]
    Zeitpunkt_Zahl = Zeit[1]
    Archiv = Archivtab(Zeitpunkt).decode('UTF-8')
    Wert = interpolieren(Archiv,  Point_ID,  Zeitpunkt)
    #Ausdruck aktualisieren
    Ausdruck=links + str(Wert) +rechts

#Durchschnittswert, Format=MW(Tag,relativer Zeitpunkt_start,relativer Zeitpunkt_ende) Beispiel MW(22,1h,jetzt) = Durchschnittswert vom Tag 22 der letzten Stunde
Suchtext = "MW\("
while re.search(Suchtext,Ausdruck):
    Zeichen = re.search(Suchtext,Ausdruck)
    links = Ausdruck[0:Zeichen.span()[0]]
    rechts = Ausdruck[Zeichen.span()[1]:len(Ausdruck)]
    Point_ID = Point_ID_ermitteln(Ausdruck,  Zeichen.span()[1])
    #Zeitpunkt Start ermitteln
    Zeit = Zeitpunkt_ermitteln(rechts[1:])
    Zeitpunkt_Start = Zeit[0]
    Zeitpunkt_Zahl_Start = Zeit[1]
    rechts = rechts[rechts.find(",")+1:]
    rechts = rechts[rechts.find(",")+1:]
    Archiv = Archivtab(Zeitpunkt_Start).decode('UTF-8')
    #Den interpolierten Wert aus aus dem Archiv ermitteln und den Tag durch den Wert ersetzen
    Wert_Punkt_Start = interpolieren(Archiv,  Point_ID,  Zeitpunkt_Start)
    #Zeitpunkt Ende ermitteln
    Zeit = Zeitpunkt_ermitteln(rechts)
    Zeitpunkt_Ende = Zeit[0]
    Zeitpunkt_Zahl_Ende= Zeit[1]
    Archiv = Archivtab(Zeitpunkt_Ende).decode('UTF-8')
    #Den interpolierten Wert aus aus dem Archiv ermitteln und den Tag durch den Wert ersetzen
    Wert_Punkt_Ende = interpolieren(Archiv,  Point_ID,  Zeitpunkt_Ende)
    
    #print ("Wert_Punkt_Ende: " + str(Wert_Punkt_Ende) + "<br>")
    #Zwischenschritte
    Zeit_mal_Wert = 0
    erster_Archivzeitpunkt = 0
    Tabellen = Archivtabellen(Zeitpunkt_Start,  Zeitpunkt_Ende)
    i = 0
    while i < len(Tabellen):
        db.query("select * from " + Tabellen[i]["Archiv_Name"] + " where Point_ID = " + Point_ID + " AND Timestamp >= '" + Zeitpunkt_Start + "' AND Timestamp <= '" + Zeitpunkt_Ende + "' AND Art = 'rV' ORDER BY Timestamp ASC")
        result = db.use_result()
        x=1
        while  x > 0:
            Daten=result.fetch_row()
            if len(Daten)>0:
                if erster_Archivzeitpunkt == 0:
                    erster_Archivwert = Daten[0][2]
                    erster_Archivzeitpunkt = Daten[0][3]
                    Zeit_mal_Wert = (float(Daten[0][3]) - Zeitpunkt_Zahl_Start) * Wert_Punkt_Start
                else:
                    Zeit_mal_Wert = Zeit_mal_Wert + float(Daten[0][4])
                    letzter_Archivwert = Daten[0][2]
                    letzter_Archivzeitpunkt = Daten[0][3]
            else:
                x=0
        i = i + 1
    if Zeit_mal_Wert == 0:
        Wert = interpolieren(Archiv, Point_ID, Zeitpunkt_Start)
    else:
        Zeit_mal_Wert = Zeit_mal_Wert + float(letzter_Archivwert) * (Zeitpunkt_Zahl_Ende - float(letzter_Archivzeitpunkt))
        Wert = Zeit_mal_Wert / (Zeitpunkt_Zahl_Ende - Zeitpunkt_Zahl_Start)
    #Ausdruck aktualisieren
    ersetzen_ende = re.search("\)", Ausdruck[Zeichen.span()[1]:len(Ausdruck)])
    Ausdruck = links + str(Wert) + Ausdruck[ersetzen_ende.span()[0] + Zeichen.span()[1] + 1:]

#Mit exec wird der Ausdruck berechnet
try:
    exec ("Wert=" + Ausdruck)
except:
    Wert = Ausdruck
print (Wert)
