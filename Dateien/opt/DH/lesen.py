#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

#import time
#Start = time.time()

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

def lesen(Art, Point_ID, Startzeit,  Endezeit, vor, nach):
    Pfad = '/var/lib/DH/'
    Ergebnis = ()
    if Endezeit < Startzeit:
        return 1
    aktJahr = Startzeit[:4]
    aktMonat = Startzeit[5:7]
    zielJahr = Endezeit[:4]
    zielMonat = Endezeit[5:7]
    #erster Wert ermitteln
    try:
        Datei = open(Pfad + aktJahr + "/" + aktMonat + "/" + Art + "/"  + Point_ID, "r")
        Text_orig = Datei.read()
        Datei.close()
        Zeilen = Text_orig.split("\n")
        i = 0
        for Zeile in Zeilen:
            if len(str(Zeile)) == 0 or type(Zeile) is int or type(Zeile) is list:
                if i == 0:
                    Zeilen = Zeilen[slice(1, len(Zeilen))]
                else:
                    Zeilen = Zeilen[slice(0, i)] + Zeilen[slice(i + 1, len(Zeilen))]
                i = i - 1
                if i < -1:
                    i = -1
            i = i + 1
        if Zeilen[0] == "":
            return
        i = 0
        Nr = -1
        Wert = list(range(len(Zeilen)))
        Zeit = list(range(len(Zeilen)))
    except:
        return
    for Zeile in Zeilen:
        temp = Zeile.split(",")[slice(0, 2)]
        if len(temp) == 2:
            Wert[i] = temp
            Zeit[i] = temp[0]
            i = i + 1
    i = 0
    while i < len(Zeilen):
        if Zeit[i] == Startzeit:
            Nr = i
            break
        else:
            if  i < len(Zeilen) - 1 :
                if str(Zeit[i]) < Startzeit:
                    try:
                        if str(Zeit[i + 1]) > Startzeit:
                            if vor == 1:
                                Nr = i
                                i = i - 1
                                break
                            else:
                                Nr = i + 1
                                i = i - 2
                                break
                    except:
                        pass
        i = i + 1
    #Wenn die Endezeit nicht im gleichen Monat ist, dann den Rest der Datei einlesen. Ansonsten bis zur Endezeit vorrücken.
    if i < 0:
        i = 0
    if aktJahr + aktMonat == zielJahr + zielMonat:
        while i < len(Zeilen):
            if Zeit[i] == Endezeit:
                break
            else:
                if  i < len(Zeilen):
                    if nach == 1 and Zeit[i] > Endezeit:
                        break
                    if nach == 0 and str(Zeit[i]) >= Endezeit:
                        i = i - 1
                        break
            i = i + 1
        if i == Nr and i == len(Zeilen):
            if  i == len(Zeilen):
                if vor == 1:
                    Ergebnis = Wert[i - 1]
                else:
                    if Wert[i - 1][0] > Startzeit:
                        Ergebnis = Wert[i - 1]
            else:
                Ergebnis = Wert[i]
        else:
            Ergebnis = Wert[slice(Nr, i + 1)]
    else:
        Ergebnis = Wert[slice(Nr, len(Wert))]

    #Falls es ueber mehrere Monate geht, dann die kompletten Monate an das Ergebnis anhaengen
    while plusMon(aktMonat,  aktJahr) < zielJahr + zielMonat:
        JahrMonat = plusMon(aktMonat,  aktJahr)
        aktMonat = JahrMonat[4:]
        aktJahr = JahrMonat[:4]
        Datei = open(Pfad + aktJahr + "/" + aktMonat + "/" + Art + "/"  + Point_ID, "r")
        Text_orig = Datei.read()
        Datei.close()
        Zeilen = Text_orig.split("\n")
        i = 0
        Wert = list(range(len(Zeilen)))
        Zeit = list(range(len(Zeilen)))
        for Zeile in Zeilen:
            temp = Zeile.split(",")[slice(0, 2)]
            if len(temp) == 2:
                Wert[i] = temp
                Zeit[i] = temp[0]
                i = i + 1
        Ergebnis = Ergebnis + Wert[slice(0, len(Wert))]
    
    #Den letzten Monat verarbeiten
    JahrMonat = plusMon(aktMonat,  aktJahr)
    aktMonat = JahrMonat[4:]
    aktJahr = JahrMonat[:4]
    if JahrMonat == zielJahr + zielMonat:
        try:
            Datei = open(Pfad + aktJahr + "/" + aktMonat + "/" + Art + "/"  + Point_ID, "r")
            Text_orig = Datei.read()
            Datei.close()
            Zeilen = Text_orig.split("\n")
            i = 0
            for Zeile in Zeilen:
                if len(str(Zeile)) == 0 or type(Zeile) is int or type(Zeile) is list:
                    if i == 0:
                        Zeilen = Zeilen[slice(1, len(Zeilen))]
                    else:
                        Zeilen = Zeilen[slice(0, i)] + Zeilen[slice(i + 1, len(Zeilen))]
                    i = i - 1
                    if i < -1:
                        i = -1
                i = i + 1
            i = 0
            Wert = list(range(len(Zeilen)))
            Zeit = list(range(len(Zeilen)))
        except:
            return
        for Zeile in Zeilen:
            Wert[i] = Zeile.split(",")[slice(0, 2)]
            Zeit[i] = Wert[i][0]
            i = i + 1
        i = 0
        while i < len(Zeilen):
            if Zeit[i] == Endezeit:
                break
            else:
                if  i < len(Zeilen) - 1:
                    if str(Zeit[i]) >= Endezeit:
                        if nach == 1:
                            i = i + 1
                        break
            i = i + 1
        if i > 0:
            Ergebnis  = Ergebnis + Wert[slice(0, i)]
        else:
            Ergebnis  = Ergebnis + Wert[slice(0, 1)]
    return Ergebnis 
