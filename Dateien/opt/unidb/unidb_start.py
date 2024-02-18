#!/usr/bin/python3

#Copyright (C) 2024 by Ralf Meier https://MeierSoft.de
#This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License.
#This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
#You should have received a copy of the GNU General Public License along with this program.  If not, see https://www.gnu.org/licenses/gpl-3.0.en.html.

import os, sys

Ordner = sys.argv[0]
while Ordner[len(Ordner) - 1:] != "/":
    Ordner = Ordner[0:len(Ordner) - 1]
os.chdir(Ordner)
os.popen('./unidb_watchdog.py &')
