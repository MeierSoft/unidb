#!/usr/bin/env python3
# -*- coding: ISO-8859-1 -*-
Stunden = list()
Tage = list()
import re, os, wx, time, sys, MySQLdb, schreiben, lesen

Kol_Verbindungen = []

def minusMon(Monat,  Jahr):
    Monat = int(Monat) - 1
    if Monat == 0:
        Monat = "12"
        Jahr = str(int(Jahr) - 1)
    else:
        Monat = str(Monat)
        if len(Monat) == 1:
            Monat = "0" + Monat
    return Jahr + Monat
    
def plusMon(Monat,  Jahr):
    Monat = int(Monat) +1
    if Monat == 13:
        Monat = "01"
        Jahr = str(int(Jahr) + 1)
    else:
        Monat = str(Monat)
        if len(Monat) == 1:
            Monat = "0" + Monat
    return Jahr + Monat

def Datei_sperren(Dateiname):
    db.query("INSERT INTO `gesperrt` (`Dokument`) VALUES ('" + Dateiname + "');")
    db.close

def Datei_entsperren(Dateiname):
    db.query("DELETE FROM `gesperrt` WHERE `Dokument` = '" + Dateiname + "';")
    db.close
    
def Datei_frei(Dateiname):
    db.query("SELECT * FROM `gesperrt` WHERE `Dokument` = '" + Dateiname + "';")
    T=db.store_result()
    db.close
    if T.num_rows() > 0:
        time.sleep(5)
        return 1
    else:
        return 0
        
def Verbindung_herstellen(Auswahl):
    global Verb, Verbindungen,  db
    if Auswahl != "":
        Auswahl = int(Auswahl)
        Verb = Verbindungen[Auswahl]
    else:
        for Verbindung in Verbindungen:
            if Verbindung["default"] == "true":
                Verb = Verbindung
    try:
        db.close
    except:
        pass
    try:
        db = MySQLdb.connect(Verb["Server"], Verb["Benutzer"], Verb["Passwort"], Verb["Datenbank"])
    except:
        wx.lib.dialogs.messageDialog(parent=None, message='Es konnte keine Verbindung aufgebaut werden.', title='Problem!', aStyle = wx.OK | wx.CENTRE, pos=wx.DefaultPosition).returnedString
        Verb={}
    if len(Verb) == 0:
        Statuszeile = "nicht verbunden"
    else:
        Statuszeile = "verbunden mit " + Verb["Server"]
    return Statuszeile

def Stunde_merken(Zeitstempel):
    global Stunden
    Jahr = Zeitstempel[:4]
    Monat = Zeitstempel[5:7]
    Tag = Jahr + Monat + Zeitstempel[8:10]
    Stunde =Tag + Zeitstempel[11:13]
    try:
        Stunden.index(Stunde)
    except:
        Stunden.append (Stunde)
    try:
        Tage.index(Tag)
    except:
        Tage.append (Tag)

def Mittelwerte_schreiben(Point_ID, step, Mittelwerte):
    Stunden.sort()
    Tage.sort()
    for Stunde in Stunden:
        if Mittelwerte == 1:
            Start = Stunde[:4] + "-" + Stunde[4:6] + "-" + Stunde[6:8] + " " + Stunde[-2:] + ":00:00"
            EndeZP = time.mktime(time.strptime(Start, "%Y-%m-%d %H:%M:%S")) + 3600
            Ende = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(EndeZP))
            Kollektiv_loeschen(Ende, Ende, Point_ID, "hMW")
            Kollektiv_loeschen(Start, Ende, Point_ID, "hMin")
            Kollektiv_loeschen(Start, Ende, Point_ID, "hMax")
        MW_berechnen(Point_ID, 3600, Stunde, step)
    for Tag in Tage:
        if Mittelwerte == 1:
            Start = Stunde[:4] + "-" + Stunde[4:6] + "-" + Stunde[6:8] + " 00:00:00"
            EndeZP = time.mktime(time.strptime(Start, "%Y-%m-%d %H:%M:%S")) + 86400
            Ende = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(EndeZP))
            Kollektiv_loeschen(Ende, Ende, Point_ID, "dMW")
            Kollektiv_loeschen(Start, Ende, Point_ID, "dMin")
            Kollektiv_loeschen(Start, Ende, Point_ID, "dMax")
        MW_berechnen(Point_ID, 86400, Tag, step)
        
def MW_berechnen(Punkt, Zeitraum, Start_Zeitstempel, step):
    if Zeitraum == 3600:
        Start_Zeitpunkt = time.mktime(time.strptime(Start_Zeitstempel, "%Y%m%d%H"))
    else:
        Start_Zeitpunkt = time.mktime(time.strptime(Start_Zeitstempel, "%Y%m%d"))
    Ende_Zeitpunkt = Start_Zeitpunkt + Zeitraum
    Start_Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Start_Zeitpunkt))
    Ende_Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Ende_Zeitpunkt))
    Max = -10e10
    Min = 10e10
    Zeitstempel_min = "0"
    Zeitstempel_max = "0"
    vt_interpol_ges = 0
    vt_ges = 0
    #Ist die Stunde ueberhaupt vollstaendig.
    Satz = lesen.lesen('rV', str(Punkt), Ende_Zeitstempel, Ende_Zeitstempel, 0, 1, Verb['Datenverzeichnis'])
    try:
        Satz = Satz[len(Satz)-2]
    except:
        pass
    nicht_gefunden = 1
    if len(str(Satz)) < 19:
        db.query("select `Timestamp`, `Value` from `akt` WHERE `Point_ID` = " + str(Punkt) + " AND `Timestamp` > '" + Ende_Zeitstempel + "' ORDER BY `Timestamp` ASC LIMIT 1;")
        Ergebnis = db.store_result()
        Satz = Ergebnis.fetch_row(maxrows=0, how=1)
    #Wenn nein, dann hat sich die Sache hier erledigt.
    if len(Satz) > 0:
        nicht_gefunden = 0
        #Den letzten Wert lesen, der vor dem Startzeitpunkt liegt, oder genau zum Startzeitpunkt geschrieben wurde. Dazu alle Werte bis einschl. dem ersten Wert nach dem Zeitraum
        Saetze = lesen.lesen('rV', str(Punkt), Start_Zeitstempel, Ende_Zeitstempel, 1, 1, Verb['Datenverzeichnis'])
        #Wenn es weniger als zwei Werte gibt, dann ist hier Ende
        if Saetze is None:
            return nicht_gefunden
        if len(Saetze) < 2:
            return nicht_gefunden
        if type(Saetze[0]) is str:
            return nicht_gefunden
        #Alle Zeitstempel auf Unixzeit umrechnen und aus den Strings Floats mchen
        i = 0
        while i < len(Saetze):
            Saetze[i][0] = time.mktime(time.strptime(str(Saetze[i][0]), "%Y-%m-%d %H:%M:%S"))
            Saetze[i][1] = float(Saetze[i][1])
            i = i + 1
        #Ist der Zeitstempel des ersten Wertes = dem Startzeitstempel? Wenn nein, dann interpolieren
        if Saetze[0][0] != Start_Zeitpunkt:
            Zeitdiff = Saetze[1][0] - Saetze[0][0]
            Diff = Saetze[1][1] - Saetze[0][1]
            Steigung = Diff / Zeitdiff
            Zeitpunkt_alt =Saetze[0][0]
            Saetze[0][1] = (Start_Zeitpunkt - Zeitpunkt_alt) * Steigung + Saetze[0][1]
            Saetze[0][0] = Start_Zeitpunkt
        #Ist der Zeitstempel des letzten Wertes = dem Endezeitstempel? Wenn nein, dann interpolieren
        max = len(Saetze) - 1
        if Saetze[max][0] != Ende_Zeitpunkt:
            Zeitdiff = Saetze[max][0] - Saetze[max - 1][0]
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
        if len(Saetze) > 2:
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
            if str(step) == "1":
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
            Kollektiv_schreiben(str(Ende_Zeitstempel), str(Mittelwert),  str(Start_Zeitpunkt + Zeitraum), str(vt_ges), str(vt_interpol_ges), str(Punkt),  Art + "MW")
            if Zeitpunkt_max > 0:
                Kollektiv_schreiben(str(Zeitstempel_max), str(Max),  str(int(Zeitpunkt_max)), 0, 0, str(Punkt),  Art + "Max")
            if Zeitpunkt_min > 0:
                Kollektiv_schreiben(str(Zeitstempel_min), str(Min),  str(int(Zeitpunkt_min)),  0,  0,  str(Punkt),  Art + "Min")
        else:
            nicht_gefunden = 1
    return nicht_gefunden

def Verbindungen_einlesen():
    global Verb, Verbindungen,  db
    if sys.platform =="linux":
        INI_Datei = os.environ ['HOME'] + "/.DH/DH_SMT.ini"
        if os.path.isfile(INI_Datei) == False:
            if os.path.isdir(os.environ ['HOME'] + "/.DH") == False:
                os.mkdir(os.environ ['HOME'] + "/.DH")
            fobj = open(INI_Datei, "w")
            fobj.write("")
            fobj.close()
    else:
        path = os.getenv('APPDATA')
        INI_Datei = path + "\DH\DH_SMT.ini"
        if os.path.isfile(INI_Datei) == False:
            if os.path.isdir(path + "\DH") == False:
                os.mkdir(path + "\DH")
            fobj = open(INI_Datei, "w")
            fobj.write("")
            fobj.close()
    fobj = open(INI_Datei)
    Verb = {}
    Schleife = 0
    Verbindungen = []
    for line in fobj:
        if Schleife == 2:
            Schleife = 1
        Zeile = line.rstrip()
        if Zeile == "{":
            Schleife = 2
        else:
            if Zeile == "}":
                Schleife = 0
                Verbindungen.append(Verb.copy())
        if Schleife == 1:
            if Zeile != "":
                Zeile = Zeile.split(":")
                Verb[Zeile[0]] = Zeile[1]
    fobj.close()
def Verbindungen_speichern():
    global Verbindungen
    if sys.platform =="linux":
        INI_Datei = os.environ ['HOME'] + "/.DH/DH_SMT.ini"
    else:
        path = os.getenv('APPDATA')
        INI_Datei = path + "\DH\DH_SMT.ini"
    fobj = open(INI_Datei, "w")
    for Verbindung in Verbindungen:
        fobj.write("{\n")
        fobj.write("Datenverzeichnis:" + Verbindung["Datenverzeichnis"] + "\n")
        fobj.write("Server:" + Verbindung["Server"] + "\n")
        fobj.write("Benutzer:" + Verbindung["Benutzer"] + "\n")
        fobj.write("Passwort:" + Verbindung["Passwort"] + "\n")
        fobj.write("Datenbank:" + Verbindung["Datenbank"] + "\n")
        fobj.write("default:" + Verbindung["default"] + "\n")
        fobj.write("}\n")
    fobj.close()
def berechnen(Ausdruck, Offset, Bezugszeitstempel):
    rechts=""
    links=""
    if Bezugszeitstempel == "jetzt":
        #Ausdruck = Ausdruck.replace("AW(", "akt(")
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

    def interpolieren(Point_ID,  Zeitpunkt):
        Ergebnis = lesen.lesen('rV',Point_ID, Zeitpunkt, Zeitpunkt, 0, 0, Verb['Datenverzeichnis'])
        if len(Ergebnis) < 5:
            Ergebnis = lesen.lesen('rV',Point_ID, Zeitpunkt, Zeitpunkt, 1, 0, Verb['Datenverzeichnis'])
            Wert_vor = float(Ergebnis[1][0])
            Zeit_vor = Ergebnis[0][0]
            Zeit_vor = time.mktime(time.strptime(Zeit_vor, "%Y-%m-%d %H:%M:%S"))
            Ergebnis = lesen.lesen('rV',Point_ID, Zeitpunkt, Zeitpunkt, 0, 1, Verb['Datenverzeichnis'])
            if len(Ergebnis) < 5:
                #Den aktuellen Wert aus akt herauslesen und den Tag durch den Wert ersetzen
                db.query("select * from akt where Point_ID = " + Point_ID + " ORDER BY Timestamp DESC LIMIT 1")
                x=db.store_result()
                akt_Werte = x.fetch_row()
                Wert_nach = float(akt_Werte[0][3])
                Zeit_nach = akt_Werte[0][1]
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
            return float(Ergebnis[1][0])

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
        db.query("select * from akt where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' ORDER BY Timestamp DESC LIMIT 1")
        x=db.store_result()
        Ergebnis=x.fetch_row()
        if len(Ergebnis) == 0:
            Ergebnis = lesen.lesen('rV',Point_ID, Zeitpunkt, Zeitpunkt, 1, 0, Verb['Datenverzeichnis'])
            try:
                Wert = Ergebnis[1][0]
            except:
                Wert = "Fehler"
        else:
            Wert = time.mktime(time.strptime(Ergebnis[0][1], "%Y-%m-%d %H:%M:%S"))
        #Ausdruck aktualisieren
        Ausdruck = links + str(Wert) + rechts

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
        db.query("select * from akt where Point_ID = " + Point_ID + " AND Timestamp < '" + Zeitpunkt + "' ORDER BY Timestamp DESC LIMIT 1")
        x=db.store_result()
        Ergebnis=x.fetch_row()
        if len(Ergebnis)==0:
            Ergebnis = lesen.lesen('rV',Point_ID, Zeitpunkt, Zeitpunkt, 1, 0, Verb['Datenverzeichnis'])
            try:
                Wert = Ergebnis[1][0]
            except:
                Wert = "Fehler"
        else:
            Wert = Ergebnis[0][1]
        #Ausdruck aktualisieren
        Ausdruck = links + str(Wert) + rechts

    #aktueller Wert
    Suchtext = "akt\("
    while re.search(Suchtext,Ausdruck):
        Zeichen = re.search(Suchtext,Ausdruck)
        links = links + Ausdruck[len(links):Zeichen.span()[0]]
        rechts = Ausdruck[Zeichen.span()[1]:len(Ausdruck)]
        Point_ID = Point_ID_ermitteln(Ausdruck,  Zeichen.span()[1])
        rechts = rechts[rechts.find(")")+1:]
        #Den aktuellen Wert aus akt herauslesen und den Point durch den Wert ersetzen
        db.query("select * from akt where Point_ID = " + Point_ID + " ORDER BY Timestamp DESC LIMIT 1")
        akt_Werte = db.store_result()
        Ergebnis=akt_Werte.fetch_row()
        Wert = Ergebnis[0][3]
        #Sonderbehandlung wegen Windoofs
        if type(Wert) == bytes:
            Wert = Wert.decode('ISO-8859-15', "ignore")
        #Ende Sonderbehandlung
        links = links + str(Wert)
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
        Zeitpunkt = Ausdruck[Zeichen.span()[1] + len(Point_ID) + 1:len(Ausdruck) - len(rechts) - 1]
        try:
            Zeitpunkt = float(Zeitpunkt)
            Zeitstempel = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(Zeitpunkt))
        except:
            Zeitstempel = Zeitpunkt
        Zeit = Zeitpunkt_ermitteln(Zeitstempel)
        Zeitpunkt = Zeit[0]
        Ergebnis = lesen.lesen('rV',Point_ID, Zeitpunkt, Zeitpunkt, 1, 0, Verb['Datenverzeichnis'])
        if Ergebnis == None:
            Wert = "Fehler"
        else:
            if type(Ergebnis[0]) is list:
                Wert = Ergebnis[0][1]
            else:
                Wert = Ergebnis[1]
        #Ausdruck aktualisieren
        Ausdruck=links + str(Wert) + rechts
    
    #interpolierte Werte, Format=intp(Point,relativer Zeitpunkt) Beispiel intp(22,1h) = interpolierter Wert vom Point 22 von vor einer Stunde
    Suchtext = "intp\("
    while re.search(Suchtext,Ausdruck):
        Zeichen = re.search(Suchtext,Ausdruck)
        links = links + Ausdruck[len(links):Zeichen.span()[0]]
        rechts = Ausdruck[Zeichen.span()[1]:len(Ausdruck)]
        Point_ID = Point_ID_ermitteln(Ausdruck,  Zeichen.span()[1])
        rechts = rechts[rechts.find(")")+1:]
        Zeit = Zeitpunkt_ermitteln(Bezugszeitstempel)
        Zeitpunkt = Zeit[0]
        Wert = interpolieren(Point_ID,  Zeitpunkt)
        #Ausdruck aktualisieren
        Ausdruck=links + str(Wert) +rechts

    #Durchschnittswert, Format=MW(Point,relativer Zeitpunkt_start,relativer Zeitpunkt_ende) Beispiel MW(22,1h,jetzt) = Durchschnittswert vom Point 22 der letzten Stunde
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
        #Den interpolierten Wert aus aus dem Archiv ermitteln und den Point durch den Wert ersetzen
        Wert_Punkt_Start = interpolieren(Point_ID,  Zeitpunkt_Start)
        #Zeitpunkt Ende ermitteln
        Zeit = Zeitpunkt_ermitteln(rechts)
        Zeitpunkt_Ende = Zeit[0]
        Zeitpunkt_Zahl_Ende= Zeit[1]
        #Den interpolierten Wert aus aus dem Archiv ermitteln und den Point durch den Wert ersetzen
        #Zwischenschritte
        Zeit_mal_Wert = 0
        erster_Archivzeitpunkt = 0
        Ergebnis = lesen.lesen('rV',Point_ID, Zeitpunkt_Start, Zeitpunkt_Ende, 0, 0, Verb['Datenverzeichnis'])
        x = 1
        while  x > 0:
            if len(Ergebnis[0]) > 0:
                if erster_Archivzeitpunkt == 0:
                    erster_Archivzeitpunkt =  time.mktime(time.strptime(str(Ergebnis[0][0]), "%Y-%m-%d %H:%M:%S"))
                    Zeit_mal_Wert = (float(erster_Archivzeitpunkt) - Zeitpunkt_Zahl_Start) * Wert_Punkt_Start
                else:
                    Zeit_mal_Wert = Zeit_mal_Wert + float(Ergebnis[0][4])
                    letzter_Archivwert = Ergebnis[1][0]
                    letzter_Archivzeitpunkt = time.mktime(time.strptime(str(Ergebnis[0][0]), "%Y-%m-%d %H:%M:%S"))
            else:
                x=0
        if Zeit_mal_Wert == 0:
            Wert = interpolieren(Point_ID, Zeitpunkt_Start)
        else:
            Zeit_mal_Wert = Zeit_mal_Wert + float(letzter_Archivwert) * (Zeitpunkt_Zahl_Ende - float(letzter_Archivzeitpunkt))
            Wert = Zeit_mal_Wert / (Zeitpunkt_Zahl_Ende - Zeitpunkt_Zahl_Start)
        #Ausdruck aktualisieren
        ersetzen_ende = re.search("\)", Ausdruck[Zeichen.span()[1]:len(Ausdruck)])
        Ausdruck = links + str(Wert) + Ausdruck[ersetzen_ende.span()[0] + Zeichen.span()[1] + 1:]

    #Mit exec wird der Ausdruck berechnet
    try:
        return eval(Ausdruck)
    except:
        return "Fehler"
def Kollektiv_loeschen(Start, Ende, Point_ID, Art):
    global Kol_Verbindungen, Datenverzeichnisse
    if len(Kol_Verbindungen) < 1 or len(Datenverzeichnisse) < 1:
        Kollektiv_verbinden()
    for Datenverzeichnis in Datenverzeichnisse:
        schreiben.loeschen(Start, Ende, Point_ID, Art, Datenverzeichnis)
    return 0
        
def Kollektiv_schreiben(Zeitpunkt,  rW, uTime, vt,  vt_interpol,  Point_ID,  Art):
    global Kol_Verbindungen, Datenverzeichnisse
    try:
        if len(Kol_Verbindungen) < 1 or len(Datenverzeichnisse) < 1:
            Kollektiv_verbinden()
    except:
        Kollektiv_verbinden()
    for Datenverzeichnis in Datenverzeichnisse:
        schreiben.schr(Zeitpunkt,  rW, uTime, vt,  vt_interpol,  Point_ID,  Art, Datenverzeichnis)
    return 0
def Kollektiv_verbinden():
    global Kol_Verbindungen, Datenverzeichnisse
    db.query("SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter`= 'Kollektiv';")
    M = db.store_result()
    Kollektiv = M.fetch_row()
    ElternID = Kollektiv[0][0]
    if type(ElternID) == bytes:
        ElternID = ElternID
    Kol_Verbindungen = []
    db.query("SELECT * FROM `Einstellungen` WHERE `Eltern_ID`= " + str(ElternID) + ";")
    M = db.store_result()
    Saetze = M.fetch_row(maxrows=0, how=1)
    i = 0
    while i < len(Saetze):
        db.query("SELECT `Wert` FROM `Einstellungen` WHERE `Parameter` = 'IP' AND `Eltern_ID`= " + str(Saetze[i]["Einstellung_ID"]) + ";")
        M = db.store_result()
        Ergebnis = M.fetch_row()
        IP = Ergebnis[0][0]
        Kol_Verbindungen.append(IP)
        i = i + 1
    Datenverzeichnisse = []
    i = 0
    while i < len(Kol_Verbindungen):
        s = 0
        while s < len(Verbindungen):
            if Verbindungen[s]["Server"] == Kol_Verbindungen[i]:
                Datenverzeichnisse.append(Verbindungen[s]["Datenverzeichnis"])
            s = s + 1
        i = i + 1
