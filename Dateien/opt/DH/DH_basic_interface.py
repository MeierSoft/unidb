#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import DH_biblio2, sys, time
Script = sys.argv[0]
Interface_Name = Script[Script.find("DH_") + 3:len(Script) - 3]

#initialisation
Initialisierung = DH_biblio2.Initialisierung(Interface_Name)
#possibly further initialisation



#end of initialisation
#start of the endless loop
Meldung = "weiter"
while Meldung != "abschalten":
    #Here starts your code








    #Here ist the end of your code
    #Listen to the message subsystem
    horchen_bis = time.time() + DH_biblio2.Intervall
    while time.time() < horchen_bis:
        Meldung = DH_biblio2.meldung(Interface_Name)
        if Meldung != "weiter":
            horchen_bis = time.time() - 1000000
        else:
            time.sleep(5)
DH_biblio2.log_schreiben(Interface_Name, "Interface gestoppt")
