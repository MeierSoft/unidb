#!/usr/bin/python

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import time, DH_biblio2, math, sys
Script = sys.argv[0]
Interface_Name = Script[Script.find("DH_") + 3:len(Script) - 3]

Initialisierung = DH_biblio2.Initialisierung(Interface_Name)

Zahl=-1
Meldung = DH_biblio2.meldung(Interface_Name)

while Meldung != "abschalten":
    Zahl+=1
    Zeitstempel=time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(time.time()))

    DH_biblio2.Wert_schreiben(DH_biblio2.TL[0]['Point_ID'], Zeitstempel, math.sin(float(Zahl)/45))

    #Auf das message subsystem horschen
    horchen_bis=time.time()+DH_biblio2.Intervall
    while time.time()<horchen_bis:
        Meldung = DH_biblio2.meldung(Interface_Name)
        if Meldung!="weiter":
            horchen_bis=time.time()-1000000
        else:
            time.sleep(5)

DH_biblio2.log_schreiben(Interface_Name, "Interface gestoppt")
