#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import MySQLdb, time, os

Pfad = '/var/lib/DH/'
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

def schr(Zeitpunkt,  rW, uTime, vt,  vt_interpol,  Point_ID,  Art):
    Jahr = Zeitpunkt[:4]
    Monat = Zeitpunkt[5:7]
    Dateiname = Pfad + Jahr + "/" + Monat + "/" + Art + "/" + Point_ID
    #nachsehen, ob der Wert ans Ende der Datei kommt
    try:
        Datei = open(Dateiname, "r")
        Text_orig = Datei.read()
        Datei.close()
        Zeilen = Text_orig.split("\n")
    except:
        Erfolg = Pfad + Jahr + "/" + Monat + "/" + Art
        while Erfolg != "0":
            try:
                os.mkdir(Erfolg)
                Erfolg = "0"
            except:
                try:
                    Erfolg = Pfad + Jahr + "/" + Monat
                    os.mkdir(Erfolg)
                    Erfolg = Pfad + Jahr + "/" + Monat + "/" + Art
                except:
                    try:
                        Erfolg = Pfad + Jahr
                        os.mkdir(Erfolg)
                        Erfolg = Pfad + Jahr + "/" + Monat + "/" + Art
                    except:
                        Erfolg = "0"
        JahrMon = minusMon(Monat,  Jahr)
        try:
            Datei = open(Pfad + JahrMon[:4] + "/" + JahrMon[4:6] + "/" + Art + "/"  + Point_ID, "r")
            Text_orig = Datei.read()
            Zeilen = Text_orig.split("\n")
            Datei.close()
        except:
            pass
        Datei = open(Dateiname, "w")
        try:
            Datei.write(Zeilen[len(Zeilen) - 1] + "\n")
        except:
            pass
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
            Datei.write(Zeitpunkt + "," + rW + "," + uTime + "," + str(vt_interpol) + "," + str(vt) + "\n")
            Datei.close()
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
                if Zeit[i] == Zeitpunkt:
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
                        Wert = Wert[slice(i)] + [Wert_temp] + Wert[slice(i + 1,len(Wert))]
                        break
                i = i + 1
            while Datei_frei(Dateiname) == 1:
                pass
            Datei_sperren(Dateiname)
            Datei = open(Dateiname, "w")
            i = 0
            while i < len(Wert) - 1:
                try:
                    Datei.write(Wert[i][0] + "," + Wert[i][1] + "," + Wert[i][2] + "," + str(Wert[i][3]) + "," + str(Wert[i][4]) + "\n")
                except:
                    pass
                i = i + 1
            Datei.close()
            Datei_entsperren(Dateiname)

