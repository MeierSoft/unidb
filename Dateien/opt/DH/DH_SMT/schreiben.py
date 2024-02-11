#!/usr/bin/python3
import MySQLdb, time, os
Zeitpunkt = ""
Art = ""
Point_ID = ""
Wert = ""
uTime = ""
vt = ""
vt_interpol = ""

#Mariadb vorbereiten
Zeile=list(range(6))
fobj = open("/opt/DH/DH.ini")
i = 0
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

def sortieren(Dateiname):
    Datei = open(Dateiname, "r")
    Text_orig = Datei.read()
    Datei.close()
    Zeilen = Text_orig.split("\n")
    if len(Zeilen) > 1:
        Zeilen.sort()
        i = 0
        Datei = open(Dateiname, "w")
        while i < len(Zeilen):
            if len(Zeilen[i]) > 0:
                if i > 0:
                    if Zeilen[i][:19] != Zeilen[i - 1][:19]:
                        Datei.write(Zeilen[i] + "\n")
            i = i + 1
    Datei.close()

def Datei_sperren(Dateiname):
    db = MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)
    db.query("INSERT INTO `gesperrt` (`Dokument`) VALUES ('" + Dateiname + "');")
    db.close

def Datei_entsperren(Dateiname):
    db = MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)
    db.query("DELETE FROM `gesperrt` WHERE `Dokument` = '" + Dateiname + "';")
    db.close
    
def Datei_frei(Dateiname):
    db = MySQLdb.connect(dbHost, dbUser, dbpwd, dbdatabase)
    db.query("SELECT * FROM `gesperrt` WHERE `Dokument` = '" + Dateiname + "';")
    T=db.store_result()
    db.close
    if T.num_rows() > 0:
        time.sleep(5)
        return 1
    else:
        return 0

def schr(Zeitpunkt, rW, uTime, vt, vt_interpol, Point_ID, Art, Datenverzeichnis):
    Jahr = Zeitpunkt[:4]
    Monat = Zeitpunkt[5:7]
    Dateiname = Datenverzeichnis + Jahr + "/" + Monat + "/" + Art + "/" + Point_ID
    #nachsehen, ob der Wert ans Ende der Datei kommt
    try:
        Datei = open(Dateiname, "r")
        Text_orig = Datei.read()
        Datei.close()
        Zeilen = Text_orig.split("\n")
    except:
        Erfolg = Datenverzeichnis + Jahr + "/" + Monat + "/" + Art
        while Erfolg != "0":
            try:
                os.mkdir(Erfolg)
                Erfolg = "0"
            except:
                try:
                    Erfolg = Datenverzeichnis + Jahr + "/" + Monat
                    os.mkdir(Erfolg)
                    Erfolg = Datenverzeichnis + Jahr + "/" + Monat + "/" + Art
                except:
                    try:
                        Erfolg = Datenverzeichnis + Jahr
                        os.mkdir(Erfolg)
                        Erfolg = Datenverzeichnis + Jahr + "/" + Monat + "/" + Art
                    except:
                        Erfolg = "0"
        Datei = open(Dateiname, "w")
        Datei.close()
        Zeilen = []
    Wert = list(range(len(Zeilen)))
    Zeit = list(range(len(Zeilen)))
    i = 0
    for Zeile in Zeilen:
        Wert[i] = Zeile.split(",")
        Zeit[i] = Wert[i][0]
        i = i + 1
    if (len(Zeilen) > 1 and Zeitpunkt > Zeit[len(Zeilen) - 2]) or len(Zeilen) == 0:
        try:
            while Datei_frei(Dateiname) == 1:
                pass
            Datei_sperren(Dateiname)
            Datei = open(Dateiname, "a")
            Datei.write(str(Zeitpunkt) + "," + str(rW) + "," + str(uTime) + "," + str(vt_interpol) + "," + str(vt) + "\n")
            Datei.close()
            sortieren(Dateiname)
            Datei_entsperren(Dateiname)
            return 0
        except:
            Datei_entsperren(Dateiname)
            Datei.close()
            return 1
    else:
            i = 0
            Wert_temp = list(range(5))
            while i < len(Zeilen):
                if Zeit[i] == Zeitpunkt or Zeit[i] == "":
                    if len(Wert[i]) < 5:
                        Wert[i] = list(range(5))
                    Wert[i][0] = Zeitpunkt
                    Wert[i][1] = rW
                    Wert[i][2] = uTime
                    Wert[i][3] = vt_interpol
                    Wert[i][4] = vt
                    break
                else:
                   if Zeit[i] > Zeitpunkt:
                        Wert_temp[0] = Zeitpunkt
                        Wert_temp[1] = rW
                        Wert_temp[2] = uTime
                        Wert_temp[3] = vt_interpol
                        Wert_temp[4] = vt 
                        Wert = Wert[slice(i)] + [Wert_temp] + Wert[slice(i,len(Wert))]
                        break
                i = i + 1
            while Datei_frei(Dateiname) == 1:
                pass
            Datei_sperren(Dateiname)
            Datei = open(Dateiname, "w")
            i = 0
            if len(Wert) == 1:
                Datei.write(str(Wert[i][0]) + "," + str(Wert[i][1]) + "," + str(Wert[i][2]) + "," + str(Wert[i][3]) + "," + str(Wert[i][4]) + "\n")
            else:
                while i < len(Wert) - 1:
                    Datei.write(str(Wert[i][0]) + "," + str(Wert[i][1]) + "," + str(Wert[i][2]) + "," + str(Wert[i][3]) + "," + str(Wert[i][4]) + "\n")
                    i = i + 1
            Datei.close()
            sortieren(Dateiname)
            Datei_entsperren(Dateiname)

def plusMon(Monat,  Jahr):
    Monat = int(Monat) + 1
    if Monat == 13:
        Monat = "01"
        Jahr = str(int(Jahr) + 1)
    else:
        Monat = str(Monat)
        if len(Monat) == 1:
            Monat = "0" + Monat
    return Jahr + Monat

def loeschen(Start, Ende, Point_ID, Art, Datenverzeichnis):
    aktJahr = Start[:4]
    aktMonat = Start[5:7]
    EndeJahr = Ende[:4]
    EndeMonat = Ende[5:7]
    while aktJahr <= EndeJahr and aktMonat <= EndeMonat:
        Dateiname = Datenverzeichnis + aktJahr + "/" + aktMonat + "/" + Art + "/" + str(Point_ID)
        #nachsehen, ob der Wert ans Ende der Datei kommt
        try:
            Datei = open(Dateiname, "r")
            Text_orig = Datei.read()
            Datei.close()
            Zeilen = Text_orig.split("\n")
        except:
            return 1
        i = 0
        while Datei_frei(Dateiname) == 1:
            pass
        Datei_sperren(Dateiname)
        Datei = open(Dateiname, "w")
        while i < len(Zeilen):
            Zeile = Zeilen[i].split(",")
            if Start <=  Zeile[0] and Ende >=  Zeile[0]:
                pass
            else:
                Datei.write(Zeilen[i] + "\n")
            i = i + 1
        Datei.close()
        Datei_entsperren(Dateiname)
        JahrMonat = plusMon(aktMonat, aktJahr)
        aktMonat = JahrMonat[4:]
        aktJahr = JahrMonat[:4]
