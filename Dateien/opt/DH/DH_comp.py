#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import time, MySQLdb, DH_biblio2, sys, schreiben, lesen, os
Script = sys.argv[0]
Interface_Name = Script[Script.find("DH_") + 3:len(Script) - 3]
Archivordner = "/var/lib/DH/"

def letzter_Wert(Point_ID,  Art):
    global Archivordner
    Jahr = time.strftime("%Y", time.localtime())
    Monat = time.strftime("%m", time.localtime())
    Ordner = Archivordner + Jahr + "/" + Monat + "/" + Art + "/"
    gefunden = 0
    while gefunden == 0 and Jahr > "2004":
        try:
            Datei = open(Ordner + str(Point_ID), "r")
            Zeilen = Datei.read().split("\n")
            Zeile = Zeilen[len(Zeilen) - 2]
            if len(Zeile) == 0:
                temp = 1/0
            gefunden = 1
        except:
            Monat = str(int(Monat) - 1)
            if Monat == "0":
                Monat = "12"
                Jahr = str(int(Jahr) - 1)
            if len(Monat) == 1:
                Monat = "0" + Monat
            Ordner = Archivordner + Jahr + "/" + Monat + "/" + Art + "/"
    if gefunden == 0:
        return
    else:
        #Zeilen = Datei.read().split("\n")
        #Zeile = Zeilen[len(Zeilen) - 2]
        if len(Zeile) > 0:
            temp = Zeile.split(",")[slice(0, 2)]
            Zeit = temp[0]
            Wert = temp[1]
        else:
            return
        return [Zeit,  Wert]


Initialisierung = DH_biblio2.Initialisierung(Interface_Name)
for Einstellung in DH_biblio2.Einstellungen:
    if Einstellung["Parameter"] == "von_Point_ID":
        Punkt_von = int(Einstellung["Wert"])
    if Einstellung["Parameter"] == "bis_Point_ID":
        Punkt_bis = int(Einstellung["Wert"])
db=MySQLdb.connect(DH_biblio2.dbHost, DH_biblio2.dbUser, DH_biblio2.dbpwd, DH_biblio2.dbdatabase)
#Sperrtabele leeren
db.query("DELETE FROM `gesperrt` WHERE `Zeitpunkt` < '" + str(time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time() - 5))) + "';")
#Arrays fuer die minarch Funktion und die Mittelwerte bauen
db.query("SELECT max(`Point_ID`) AS laenge_Array FROM `Points`;")
Ergebnis = db.store_result()
T = Ergebnis.fetch_row(maxrows=0, how=1)
Laenge = int(T[0]["laenge_Array"]) + 2
hMW=list(range(Laenge))
dMW=list(range(Laenge))
hMWZ=list(range(Laenge))
dMWZ=list(range(Laenge))
lAW=list(range(Laenge))
lAZ=list(range(Laenge))
i=0
while i < Laenge:
    hMW[i] = ""
    dMW[i] = ""
    hMWZ[i] = 0
    dMWZ[i] = 0
    lAW[i]=0
    lAZ[i]=0
    i=i+1

db.query("SELECT `Point_ID`, `first_Value`, `Mittelwerte`, `step` FROM `Points` WHERE `Point_ID` >= " + str(Punkt_von) + " AND `Point_ID` <= " + str(Punkt_bis) + " AND `scan` = 1 AND `archive` = 1 ORDER BY `Point_ID` ASC;")
Ergebnis = db.store_result()
Points = Ergebnis.fetch_row(maxrows=0, how=1)
i=0
while i < len(Points):
    temp = letzter_Wert(Points[i]["Point_ID"],  "rV")
    if temp != None:
        while temp[0][0] == '\x00':
            temp[0] = temp[0][1:] 
        lAZ[i] = time.mktime(time.strptime(temp[0], "%Y-%m-%d %H:%M:%S"))
        lAW[i] = float(temp[1])
    i = i + 1

i = 0
while i < len(Points):
    Punkt = int(Points[i]["Point_ID"])
    if int(Points[i]["Mittelwerte"]) == 1:
        temp = letzter_Wert(Punkt,  "hMW")
        if temp != None:
            while temp[0][0] == '\x00':
                temp[0] = temp[0][1:] 
            hMW[Punkt] = float(temp[1])
            hMWZ[Punkt] = time.mktime(time.strptime(temp[0], "%Y-%m-%d %H:%M:%S"))
        else:
            hMW[Punkt] = 0
            hMWZ[Punkt] = time.mktime(time.strptime(str(Points[i]["first_Value"]), "%Y-%m-%d %H:%M:%S"))
    i = i + 1

i = 0
while i < len(Points):
    Punkt = int(Points[i]["Point_ID"])
    if int(Points[i]["Mittelwerte"]) == 1:
        temp = letzter_Wert(Punkt,  "dMW")
        if temp != None:
            while temp[0][0] == '\x00':
                temp[0] = temp[0][1:] 
            dMW[Punkt] = float(temp[1])
            dMWZ[Punkt] = time.mktime(time.strptime(temp[0], "%Y-%m-%d %H:%M:%S"))
        else:
            dMW[Punkt] = 0
            dMWZ[Punkt] = time.mktime(time.strptime(str(Points[i]["first_Value"]), "%Y-%m-%d %H:%M:%S"))
    i = i + 1

Zeitpunkt = list(range(3))
Meldung = "Start"

db.query("select `Point_ID` from `Tags` where `archive` = 0;" )
Ergebnis = db.store_result()
narch = Ergebnis.fetch_row(maxrows=0, how=1)

#Werte pro Stunde
db.query("SELECT `Point_ID` FROM `Points` WHERE `Info` = '" + Interface_Name + ": Werte_pro_h_Tag';")
T=db.store_result()
TL = T.fetch_row(maxrows=0, how=1)
try:
    Werte_pro_h_Tag = str(TL[0]['Point_ID'])
except:
    Werte_pro_h_Tag = ""
letzte_Zeitzahl_Werte_pro_h = 0
Werte_pro_h = 0
#Zeitpunkt letzter Wert_Tag ermitteln
db.query("SELECT `Point_ID` FROM `Points` WHERE `Info` = '" + Interface_Name + ": Zeitpunkt_letzter_Wert';")
T=db.store_result()
TL = T.fetch_row(maxrows=0, how=1)
try:
    Zeit_letzter_Wert_Tag = str(TL[0]['Point_ID'])
except:
    Zeit_letzter_Wert_Tag = ""
def Wph():
    global letzte_Zeitzahl_Werte_pro_h, Werte_pro_h
    if int(letzte_Zeitzahl_Werte_pro_h ) + 600 <= int(time.time()):
        Werte_pro_h = float(Werte_pro_h) * 6
        DH_biblio2.schreiben("INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES (" + str(Werte_pro_h_Tag) + ", '" + str(time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time()))) + "', " + str(Werte_pro_h) + ");")
        Werte_pro_h = 0
        letzte_Zeitzahl_Werte_pro_h = time.time()
        SQL_Text = "INSERT INTO `akt` (`Point_ID`, `Timestamp`, `Value`) VALUES ('" + str(Zeit_letzter_Wert_Tag) + "', '" + str(time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time()))) + "', '" + str(time.time()) + "');"
        DH_biblio2.schreiben(SQL_Text)
        
def MW_berechnen (Punkt, Start_Zeitpunkt,  Zeitraum,  Ende_Zeitstempel):
    global Werte_pro_h
    Start_Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Start_Zeitpunkt))
    if Zeitraum == 3600:
        Start_Zeitstempel = str(Start_Zeitstempel)[0:14] + "00:00"
        Ende_Zeitstempel = str(Ende_Zeitstempel)[0:14] + "00:00"
    else:
        Start_Zeitstempel = str(Start_Zeitstempel)[0:10] + " 00:00:00"
        Ende_Zeitstempel = str(Ende_Zeitstempel)[0:10] + " 00:00:00"
    Start_Zeitpunkt = time.mktime(time.strptime(str(Start_Zeitstempel), "%Y-%m-%d %H:%M:%S"))
    Ende_Zeitpunkt = time.mktime(time.strptime(str(Ende_Zeitstempel), "%Y-%m-%d %H:%M:%S"))
    Max = -10e10
    Min = 10e10
    Zeitstempel_min = "0"
    Zeitstempel_max = "0"
    vt_interpol_ges = 0
    vt_ges = 0
    #Ist die Stunde ueberhaupt vollstaendig.
    Satz = lesen.lesen('rV', str(Punkt), Ende_Zeitstempel, Ende_Zeitstempel, 0, 1)
    try:
        if Satz is None:
            return 1
        if len(Satz) > 0:
            Satz = Satz[0][0]
    except:
        pass
    if len(str(Satz)) == 19:
        #Den letzten Wert lesen, der vor dem Startzeitpunkt liegt, oder genau zum Startzeitpunkt geschrieben wurde. Dazu alle Werte bis einschl. dem ersten Wert nach dem Zeitraum
        Saetze = lesen.lesen('rV', str(Punkt), Start_Zeitstempel, Ende_Zeitstempel, 1, 1)
        #Wenn es weniger als einen Wert gibt, dann ist hier Ende
        if Saetze is None:
            return 1
        if len(Saetze) < 3 and type(Saetze[0]) is str:
            if Zeitraum == 3600:
                Art = "h"
            else:
                Art = "d"
            Satz = lesen.lesen(Art + "MW", str(Punkt), Start_Zeitstempel, Start_Zeitstempel, 1, 0)
            try:
                schreiben.schr(Ende_Zeitstempel, str(Satz[1]), str(Ende_Zeitpunkt), str(float(Satz[1]) * 3600), str(float(Satz[1]) * 3600), str(Punkt),  Art + "MW")
                schreiben.schr(Ende_Zeitstempel, str(Satz[1]), str(Ende_Zeitpunkt), 0,  0, str(Punkt), Art + "Max")
                schreiben.schr(Ende_Zeitstempel, str(Satz[1]), str(Ende_Zeitpunkt),  0,  0, str(Punkt), Art + "Min")
                Werte_pro_h = Werte_pro_h + 1
                Wph()
            except:
                pass
            return 0
        if type(Saetze[0]) is str:
            return 1
        #Alle Zeitstempel auf Unixzeit umrechnen undaus den Strings Floats mchen
        ii = 0
        while ii < len(Saetze):
            Saetze[ii][0] = time.mktime(time.strptime(str(Saetze[ii][0]), "%Y-%m-%d %H:%M:%S"))
            Saetze[ii][1] = float(Saetze[ii][1])
            ii = ii + 1
        #Ist der Zeitstempel des ersten Wertes = dem Startzeitstempel? Wenn nein, dann interpolieren
        if len(Saetze) > 1:
            if Saetze[0][0] != Start_Zeitpunkt:
                Zeitdiff = Saetze[1][0] - Saetze[0][0]
                Diff = Saetze[1][1] - Saetze[0][1]
                if Diff > 0:
                    Steigung = Diff / Zeitdiff
                else:
                    Steigung = 0
                Zeitpunkt_alt =Saetze[0][0]
                Saetze[0][1] = (Start_Zeitpunkt - Zeitpunkt_alt) * Steigung + Saetze[0][1]
                Saetze[0][0] = Start_Zeitpunkt
            #Ist der Zeitstempel des letzten Wertes = dem Endezeitstempel? Wenn nein, dann interpolieren
            max = len(Saetze) - 1
            if Saetze[max][0] != Ende_Zeitpunkt:
                Zeitdiff = Saetze[max][0] - Saetze[max - 1][0]
                if Zeitdiff > 0:
                    Diff = Saetze[max][1] - Saetze[max - 1][1]
                    Steigung = Diff / Zeitdiff
                    Zeitpunkt_alt =Saetze[max - 1][0]
                    Saetze[max][1] = (Ende_Zeitpunkt - Zeitpunkt_alt) * Steigung + Saetze[max - 1][1]
                    Saetze[max][0] = Ende_Zeitpunkt
            #Alle vt berechnen, sowie Min und Max ermitteln.
            Zeitpunkt_min = 0
            Zeitpunkt_max = 0
            alter_Wert = Saetze[0][1]
            alter_Zeitpunkt = Saetze[0][0]
            Reihe = 0
            if len(Saetze) > 1:
                if Saetze[0][1] < Min:
                    Min = Saetze[0][1]
                    Zeitpunkt_min = Saetze[0][0]
                if Saetze[0][1] > Max:
                    Max = Saetze[0][1]
                    Zeitpunkt_max = Saetze[0][0]
                while Reihe < len(Saetze) - 1:
                    Reihe = Reihe +1
                    neuer_Wert = Saetze[Reihe][1]
                    neuer_Zeitpunkt = Saetze[Reihe][0]
                    if neuer_Zeitpunkt >= Start_Zeitpunkt and neuer_Zeitpunkt <= Ende_Zeitpunkt:
                        if neuer_Wert < Min:
                            Min = neuer_Wert
                            Zeitpunkt_min = Saetze[Reihe][0]
                        if neuer_Wert > Max:
                            Max = neuer_Wert
                            Zeitpunkt_max = Saetze[Reihe][0]
                    vt = (neuer_Zeitpunkt - alter_Zeitpunkt) * alter_Wert
                    vt_interpol = (neuer_Zeitpunkt - alter_Zeitpunkt) * (neuer_Wert + alter_Wert) / 2
                    vt_ges = vt_ges + vt
                    vt_interpol_ges = vt_interpol_ges + vt_interpol
                    alter_Wert = neuer_Wert
                    alter_Zeitpunkt = neuer_Zeitpunkt
            else:
                Mittelwert = (Saetze[0][1] + Saetze[1][1]) / 2
                vt_ges = Saetze[0][1] * Zeitraum
                vt_interpol_ges = Mittelwert * Zeitraum
            #Mittelwert berechnen
            try:
                if DH_biblio2.TL[i]["step"] == "1":
                    Mittelwert = vt_ges / Zeitraum
                else:
                    Mittelwert = vt_interpol_ges / Zeitraum
            except:
                Mittelwert = "Fehler"
            #Wenn wir keinen Mittelwert haben, dann hat sich die Sache hier erledigt. Ansonsten werden die ermittelten Werte  geschrieben.
            if str(Mittelwert) != "Fehler":
                if Zeitraum == 3600:
                    Art = "h"
                else:
                    Art = "d"
                Zeitstempel_max = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitpunkt_max))
                Zeitstempel_min = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitpunkt_min))
                schreiben.schr(str(Ende_Zeitstempel), str(Mittelwert),  str(Start_Zeitpunkt + 3600), str(vt_ges),  str(vt_interpol_ges), str(Punkt),  Art + "MW")
                if Zeitpunkt_max > 0:
                    schreiben.schr(str(Zeitstempel_max), str(Max),  str(int(Zeitpunkt_max)), 0, 0, str(Punkt),  Art + "Max")
                if Zeitpunkt_min > 0:
                    schreiben.schr(str(Zeitstempel_min), str(Min),  str(int(Zeitpunkt_min)),  0,  0,  str(Punkt),  Art + "Min")
                Werte_pro_h = Werte_pro_h + 1
                Wph()
            else:
                return 1
            return 0
        else:
            return 0
    else:
        return 1

while Meldung != "abschalten": 
    #Auf das message subsystem horschen
    if Meldung != "Start":
        try:
            Meldung = DH_biblio2.meldung(Interface_Name)
        except:
            Meldung = "weiter"
    if Meldung != "abschalten":
        Meldung = "weiter"
    #von allen Tags, die archive = 0 haben, nur den neuesten Wert in der Tabelle akt stehen lassen
    i = 0
    while i < len(narch):
        db.query("SELECT `Timestamp` FROM akt WHERE `Point_ID` = " + str(narch[i]['Point_ID']) + " ORDER BY `Timestamp` DESC LIMIT 1;")
        Ergebnis = db.store_result()
        akt_Werte = Ergebnis.fetch_row(maxrows=0, how=1)
        try:
            db.query("START TRANSACTION;")
            db.query("DELETE FROM akt WHERE `Point_ID` = " + str(narch[i]['Point_ID']) + " AND `Timestamp` < '" + str(akt_Werte[0]['Timestamp']) + "';")
            db.query("COMMIT;")
        except:
            pass
        i = i + 1
    i = -1
    Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
    while i < DH_biblio2.T.num_rows()-1:
        ges_Datensaetze = 0
        i = i + 1
        if int(DH_biblio2.TL[i]['archive']) == 1:
            Punkt = int(DH_biblio2.TL[i]['Point_ID'])
            Jahr = Zeitstempel[:4]
            Monat = Zeitstempel[5:7]
            Dateiname = Archivordner + Jahr + "/" + Monat + "/rV/" + str(Punkt)
            while schreiben.Datei_frei(Dateiname) == 1:
                pass
            schreiben.Datei_sperren(Dateiname)
            try:
                Datei = open(Dateiname, "a")
            except:
                Erfolg = Archivordner + Jahr + "/" + Monat + "/rV"
                while Erfolg != "0":
                    try:
                        os.mkdir(Erfolg)
                        Erfolg = "0"
                    except:
                        try:
                            Erfolg = Archivordner + Jahr + "/" + Monat
                            os.mkdir(Erfolg)
                            Erfolg = Archivordner + Jahr + "/" + Monat + "/rV"
                        except:
                            try:
                                Erfolg = Archivordner + Jahr
                                os.mkdir(Erfolg)
                                Erfolg = Archivordner + Jahr + "/" + Monat + "/rV"
                            except:
                                Erfolg = "0"
                Datei = open(Dateiname, "a")
            if Punkt >= Punkt_von and Punkt <= Punkt_bis:
                del_akt = "delete from `akt` where `id` = "
                db.query("SELECT `id`, `Timestamp`, `Value` from `akt` where `Point_ID` = " + str(DH_biblio2.TL[i]['Point_ID']) + " ORDER BY `Timestamp` ASC LIMIT 10000")
                Ergebnis = db.store_result()
                akt_Werte = Ergebnis.fetch_row(maxrows=0, how=1)
                ges_Datensaetze = ges_Datensaetze + len(akt_Werte)
                Zaehler = 0
                while len(akt_Werte) > 2:
                    # min_erwartet und max_erwartet vom mittleren Wert ueber Interpolation errechnen
                    Zeitpunkt[0] = time.mktime(time.strptime(str(akt_Werte[0]['Timestamp']), "%Y-%m-%d %H:%M:%S"))
                    Zeitpunkt[1] = time.mktime(time.strptime(str(akt_Werte[1]['Timestamp']), "%Y-%m-%d %H:%M:%S"))
                    Zeitpunkt[2] = time.mktime(time.strptime(str(akt_Werte[2]['Timestamp']), "%Y-%m-%d %H:%M:%S"))
                    #Wenn der zweite Wert den gleichen Zeitstempel wie der erste hat, dann diese Werte loeschen
                    if Zeitpunkt[1] == Zeitpunkt[0]:
                        db.query("START TRANSACTION;")
                        db.query("delete from akt where id = " + str(akt_Werte[0]['id']) + ";")
                        db.query("COMMIT;")
                        akt_Werte=akt_Werte[1:]
                    else:
                        verworfen = 0
                        if DH_biblio2.TL[i]['compression'] != "0":
                            #Steigung pro Sekunde errechnen
                            if float(akt_Werte[2]['Value']) - float(akt_Werte[0]['Value']) !=0:
                                Steigung = (float(akt_Werte[2]['Value']) - float(akt_Werte[0]['Value'])) / (float(Zeitpunkt[2]) - float(Zeitpunkt[0]))
                            else:
                                Steigung=0
                            #erwartetes Minimum und Maximum
                            max_erwartet = (Zeitpunkt[1] - Zeitpunkt[0]) * Steigung + float(akt_Werte[0]['Value']) + float(DH_biblio2.TL[i]['compression'])
                            min_erwartet = (Zeitpunkt[1] - Zeitpunkt[0]) * Steigung + float(akt_Werte[0]['Value']) - float(DH_biblio2.TL[i]['compression'])
                            #aus der akt Tabelle loeschen, sonst den mittleren Wert loeschen
                            if float(akt_Werte[1]['Value']) <= max_erwartet and float(akt_Werte[1]['Value']) >= min_erwartet:
                                verworfen = 1
                        else:
                            if float(akt_Werte[1]['Value']) == float(akt_Werte[0]['Value']):
                                verworfen = 1
                        if verworfen == 1:
                            del_akt = del_akt + str(akt_Werte[1]['id']) + " OR `id` = "
                            akt_Werte=akt_Werte[:1] + akt_Werte[2:]
                        else:
                            if str(DH_biblio2.TL[i]['Mittelwerte']) == "1":
                                vt = str(lAW[Punkt]*(Zeitpunkt[1]-lAZ[Punkt]))
                                Diff = (float(akt_Werte[1]['Value'])-lAW[Punkt])
                                if Diff != 0:
                                    Diff=Diff/2
                                vt_interpol = str((lAW[Punkt] + Diff) * (Zeitpunkt[1]-lAZ[Punkt]))
                            else:
                                vt = "0"
                                vt_interpol = "0"
                            Zeitp = str(int(time.mktime(time.strptime(str(akt_Werte[1]['Timestamp']),  "%Y-%m-%d %H:%M:%S"))))
                            lAW[Punkt] = float(akt_Werte[1]['Value'])
                            lAZ[Punkt] = Zeitpunkt[1]
                            try:
                                Datei.write(str(akt_Werte[1]['Timestamp']) + "," +  str(akt_Werte[1]['Value']) + "," + str(Zeitp) + "," + str(vt_interpol) + "," + str(vt) + "\n")
                                Werte_pro_h = Werte_pro_h +1
                                Wph()
                            except:
                                pass
                            if Zaehler < 1001:
                                del_akt = del_akt + str(akt_Werte[0]['id']) + " OR `id` = "
                                Zaehler = Zaehler +1
                            else:
                                del_akt = del_akt[:len(del_akt)-11] + ";"
                                db.query("START TRANSACTION;")
                                db.query(del_akt)
                                db.query("COMMIT;")
                                del_akt = "delete from `akt` where `id` = "
                                Zaehler = 0
                            akt_Werte=akt_Werte[1:]
                    #Wenn der maximale zeitliche Abstand zwischen zwei Archivwerten erreicht wurde, dann auf jeden Fall den juengsten aktuellen Wert ins Archiv schreiben.
                    if float(DH_biblio2.TL[i]['minarch']) > 0:
                        if lAZ[Punkt] + float( DH_biblio2.TL[i]['minarch']) <= Zeitpunkt[2] :
                            Zeitp = str(int(Zeitpunkt[2] ))
                            vt = str(lAW[Punkt]*(float(Zeitp)-lAZ[Punkt]))  
                            Diff = (float(akt_Werte[1]['Value'])-lAW[Punkt])
                            if Diff != 0:
                                Diff=Diff/2 
                            vt_interpol = str((lAW[Punkt] + Diff) * (float(Zeitp)-lAZ[Punkt]))
                            lAZ[Punkt] = float(Zeitp)
                            lAW[Punkt] = float(akt_Werte[1]['Value'])
                            Datei.write(str(akt_Werte[1]['Timestamp']) + "," +  str(lAW[Punkt]) + "," + str(Zeitp) + "," + str(vt_interpol) + "," + str(vt) + "\n")
                            Werte_pro_h = Werte_pro_h + 1
                if len(del_akt) > 31 and len(akt_Werte) > 1:
                    del_akt = del_akt[:len(del_akt)-11] + ";"
                    db.query("START TRANSACTION;")
                    db.query(del_akt)
                    db.query("COMMIT;")
            Datei.close()
            schreiben.Datei_entsperren(Dateiname)
            #ggf Stundenwerte schreiben
            if str(DH_biblio2.TL[i]['Mittelwerte']) == "1":
                try:
                    Ende_Zeitpunkt = time.mktime(time.strptime(str(akt_Werte[len(akt_Werte)-1]['Timestamp']), "%Y-%m-%d %H:%M:%S"))
                except:
                    Ende_Zeitpunkt = time.time()
                MW_berechnet = 0
                while hMWZ[Punkt] > 0 and hMWZ[Punkt] + 3600 <= Ende_Zeitpunkt:
                    Ende_Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(hMWZ[Punkt] + 3600))
                    MW_berechnet = MW_berechnen (Punkt, hMWZ[Punkt],  3600,  Ende_Zeitstempel)
                    #if MW_berechnet == 0:
                    hMWZ[Punkt] = hMWZ[Punkt] + 3600
                #Tagesmittelwert schreiben?
                try:
                    Ende_Zeitpunkt = time.mktime(time.strptime(str(akt_Werte[len(akt_Werte)-1]['Timestamp']), "%Y-%m-%d %H:%M:%S"))
                except:
                    Ende_Zeitpunkt = time.time()
                MW_berechnet = 0
                while dMWZ[Punkt] > 0 and dMWZ[Punkt] + 86400 <= Ende_Zeitpunkt:
                    Ende_Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(dMWZ[Punkt] + 86400))
                    MW_berechnet = MW_berechnen (Punkt, dMWZ[Punkt],  86400,  Ende_Zeitstempel)
                    #if MW_berechnet == 0:
                    dMWZ[Punkt] = dMWZ[Punkt] + 86400
    #Auf das message subsystem horschen
    horchen_bis = time.time() + DH_biblio2.Intervall
    while time.time() < horchen_bis:
        Meldung = DH_biblio2.meldung(Interface_Name)
        if Meldung != "weiter":
            horchen_bis = time.time()-1000000
        else:
            time.sleep(5)

DH_biblio2.log_schreiben(Interface_Name, "Interface gestoppt")
db.close
